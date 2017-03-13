<?php

namespace Tests\AppBundle\Form\Handler\Subscription;

use AppBundle\Entity\ElectionRound;
use AppBundle\Entity\Procuration;
use AppBundle\Entity\User;
use AppBundle\Form\Handler\Subscription\SubscriptionReasonFormHandler;
use AppBundle\Form\Type\Subscription\SubscriptionReasonType;
use AppBundle\Mediator\ProcurationMediator;
use AppBundle\Repository\UserRepository;
use Doctrine\ORM\EntityManager;
use FOS\UserBundle\Doctrine\UserManager;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\Session;

class SubscriptionReasonFormHandlerTest extends TestCase
{
    protected $formFactory;
    protected $formClassName;
    protected $userManager;
    protected $userRepository;
    protected $entityManager;

    /**
     * @var SubscriptionReasonFormHandler
     */
    protected $formHandler;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->formFactory = $this->createMock(FormFactoryInterface::class);
        $this->formClassName = SubscriptionReasonType::class;
        $this->userManager = $this->createMock(UserManager::class);
        $this->userRepository = $this->createMock(UserRepository::class);
        $this->entityManager = $this->createMock(EntityManager::class);

        $this->formHandler = new SubscriptionReasonFormHandler(
            $this->formFactory,
            $this->formClassName,
            $this->userManager,
            $this->userRepository,
            $this->entityManager
        );
    }

    public function testProcessExistingUserWithSameElectionRound()
    {
        $email = 'test@en-marche.fr';
        $form = $this->createFormMock();
        $request = $this->createRequestMock();
        $session = $this->createSessionMock();
        $user = $this->createUserMock();
        $procuration = $this->createProcurationMock();
        $electionRound = $this->createElectionRoundMock();

        $formData = [
            'contact' => [
                'email' => $email,
            ],
            'election_rounds' => [
                $electionRound,
            ],
            'reason' => [
                'reason' => ProcurationMediator::REASON_PROFESSIONAL,
            ],
        ];

        $form->expects($this->once())
            ->method('isSubmitted')
            ->will($this->returnValue(true));
        $form->expects($this->once())
            ->method('isValid')
            ->will($this->returnValue(true));
        $form->expects($this->atLeastOnce())
            ->method('getData')
            ->will($this->returnValue($formData));
        $request->expects($this->atLeastOnce())
            ->method('getSession')
            ->will($this->returnValue($session));
        $session->expects($this->atLeastOnce())
            ->method('get')
            ->will($this->returnValue($formData));
        $this->userRepository->expects($this->once())
            ->method('findOneByEmailWithProcurations')
            ->with($this->equalTo($email))
            ->will($this->returnValue($user));

        $this->entityManager->expects($this->once())
            ->method('merge')
            ->with($this->equalto($electionRound))
            ->will($this->returnValue($electionRound));

        $user->expects($this->once())
            ->method('getProcurations')
            ->will($this->returnValue([$procuration]));
        $procuration->expects($this->atLeastOnce())
            ->method('getElectionRound')
            ->will($this->returnValue($electionRound));

        $this->userManager->expects($this->never())
            ->method('updateUser');
        $this->entityManager->expects($this->once())
            ->method('flush');
        $session->expects($this->once())
            ->method('getFlashBag')
            ->will($this->returnValue($this->createFlashBagMock()));

        $this->formHandler->process($form, $request);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function createFormMock()
    {
        return $this->createMock(FormInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function createRequestMock()
    {
        return $this->createMock(Request::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function createSessionMock()
    {
        return $this->createMock(Session::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function createFlashBagMock()
    {
        return $this->createMock(FlashBagInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function createUserMock()
    {
        return $this->createMock(User::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function createProcurationMock()
    {
        return $this->createMock(Procuration::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function createElectionRoundMock()
    {
        return $this->createMock(ElectionRound::class);
    }
}
