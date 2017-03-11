<?php

namespace Tests\AppBundle\Mediator;

use AppBundle\Entity\ElectionRound;
use AppBundle\Entity\Procuration;
use AppBundle\Entity\User;
use AppBundle\Entity\VotingAvailability;
use AppBundle\Mediator\ProcurationMediator;
use AppBundle\Repository\ProcurationRepository;
use Doctrine\ORM\EntityManager;
use EnMarche\Bundle\MailjetBundle\Client\MailjetClient;
use Knp\Component\Pager\PaginatorInterface;
use PHPUnit\Framework\TestCase;

class ProcurationMediatorTest extends TestCase
{
    /**
     * @var ProcurationMediator
     */
    protected $mediator;

    protected $procurationRepository;
    protected $entityManager;
    protected $cerfaOutputDir;
    protected $mailjetClient;
    protected $paginator;
    protected $paginatorParameterName;
    protected $paginationSize;

    protected function setUp()
    {
        $this->procurationRepository = $this->createMock(ProcurationRepository::class);
        $this->entityManager = $this->createMock(EntityManager::class);
        $this->cerfaOutputDir = __DIR__;
        $this->mailjetClient = $this->createMock(MailjetClient::class);
        $this->paginator = $this->createMock(PaginatorInterface::class);
        $this->paginatorParameterName = 'some_random_param';
        $this->paginationSize = 42;

        $this->mediator = new ProcurationMediator(
            $this->procurationRepository,
            $this->entityManager,
            $this->cerfaOutputDir,
            $this->mailjetClient,
            $this->paginator,
            $this->paginatorParameterName,
            $this->paginationSize
        );
    }

    public function testUnbind()
    {
        $procuration = $this->createMock(Procuration::class);
        $requester = $this->createMock(User::class);
        $electionRound = $this->createMock(ElectionRound::class);
        $requesterEmail = 'je@marche.fr';
        $votingAvailability = $this->createMock(VotingAvailability::class);
        $voter = $this->createMock(User::class);
        $voterEmail = 'helper@en-marche.fr';

        $procuration->expects($this->once())
            ->method('setVotingAvailability')
            ->with($this->equalTo(null));

        // Mail generation
        $procuration->expects($this->once())
            ->method('getRequester')
            ->will($this->returnValue($requester));
        $requester->expects($this->once())
            ->method('getEmail')
            ->will($this->returnValue($requesterEmail));
        $procuration->expects($this->once())
            ->method('getElectionRound')
            ->will($this->returnValue($electionRound));
        $electionRound->expects($this->once())
            ->method('getPerformanceDate')
            ->will($this->returnValue(new \DateTime()));
        $procuration->expects($this->once())
                    ->method('getVotingAvailability')
                    ->will($this->returnValue($votingAvailability));
        $votingAvailability->expects($this->once())
                           ->method('getVoter')
                           ->will($this->returnValue($voter));
        $voter->expects($this->once())
              ->method('getEmail')
              ->will($this->returnValue($voterEmail));

        // End of mail generation


        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with($this->equalTo($procuration));
        $this->entityManager->expects($this->once())
            ->method('flush');

        $this->mailjetClient->expects($this->once())
            ->method('sendMessage');

        $this->mediator->unbind($procuration);
    }
}
