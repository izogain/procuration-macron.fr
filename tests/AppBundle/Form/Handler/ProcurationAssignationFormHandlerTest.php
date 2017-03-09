<?php

namespace Tests\AppBundle\Form\Handler;

use AppBundle\Entity\ElectionRound;
use AppBundle\Entity\Procuration;
use AppBundle\Entity\User;
use AppBundle\Entity\VotingAvailability;
use AppBundle\Form\Handler\ProcurationAssignationFormHandler;
use AppBundle\FPDI\FPDIWriter;
use Doctrine\ORM\EntityManager;
use EnMarche\Bundle\MailjetBundle\Client\MailjetClient;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class ProcurationAssignationFormHandlerTest extends TestCase
{
    /**
     * @var ProcurationAssignationFormHandler
     */
    protected $formHandler;

    protected $formFactory;
    protected $formClassName;
    protected $entityManager;
    protected $fpdiWriter;
    protected $mailjetClient;

    protected function setUp()
    {
        $this->formFactory = $this->createMock(FormFactoryInterface::class);
        $this->formClassName = 'AppBundle\Form\Type\ProcurationAssignationType';
        $this->entityManager = $this->createMock(EntityManager::class);
        $this->fpdiWriter = $this->createMock(FPDIWriter::class);
        $this->mailjetClient = $this->createMock(MailjetClient::class);

        $this->formHandler = new ProcurationAssignationFormHandler(
            $this->formFactory,
            $this->formClassName,
            $this->entityManager,
            $this->fpdiWriter,
            $this->mailjetClient
        );
    }

    public function testProcess()
    {
        $form = $this->createMock(FormInterface::class);
        $request = $this->createMock(Request::class);
        $procuration = $this->createMock(Procuration::class);
        $requester = $this->createMock(User::class);
        $electionRound = $this->createMock(ElectionRound::class);
        $requesterEmail = 'je@marche.fr';
        $votingAvailability = $this->createMock(VotingAvailability::class);
        $voter = $this->createMock(User::class);
        $voterEmail = 'helper@en-marche.fr';

        $form->expects($this->once())
            ->method('handleRequest')
            ->with($this->equalTo($request));

        $form->expects($this->once())
            ->method('isSubmitted')
            ->will($this->returnValue(true));
        $form->expects($this->once())
            ->method('isValid')
            ->will($this->returnValue(true));
        $form->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($procuration));

        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with($this->equalTo($procuration));
        $this->entityManager->expects($this->once())
            ->method('flush');

        $this->fpdiWriter->expects($this->once())
            ->method('generateForProcuration')
            ->with($this->equalTo($procuration));

        $this->mailjetClient->expects($this->once())
            ->method('sendMessage');

        // Mail generation
        $procuration->expects($this->once())
            ->method('getRequester')
            ->will($this->returnValue($requester));
        $procuration->expects($this->once())
            ->method('getVotingAvailability')
            ->will($this->returnValue($votingAvailability));
        $votingAvailability->expects($this->once())
            ->method('getVoter')
            ->will($this->returnValue($voter));
        $voter->expects($this->once())
            ->method('getEmail')
            ->will($this->returnValue($voterEmail));
        $requester->expects($this->once())
            ->method('getEmail')
            ->will($this->returnValue($requesterEmail));
        $procuration->expects($this->once())
            ->method('getElectionRound')
            ->will($this->returnValue($electionRound));
        $electionRound->expects($this->once())
            ->method('getPerformanceDate')
            ->will($this->returnValue(new \DateTime()));
        // End of mail generation

        $this->assertTrue($this->formHandler->process($form, $request));
    }
}
