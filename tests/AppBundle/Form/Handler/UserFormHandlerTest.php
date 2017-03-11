<?php

namespace Tests\AppBundle\Form\Handler;

use AppBundle\Entity\User;
use AppBundle\Form\Handler\UserFormHandler;
use AppBundle\Form\Type\UserType;
use AppBundle\Generator\GeneratorInterface;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\FormConfigInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\Test\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Tests\AppBundle\AbstractTestCase;

class UserFormHandlerTest extends AbstractTestCase
{
    protected $formFactory;
    protected $formClassName;
    protected $entityManager;
    protected $passwordGenerator;

    /**
     * @var UserFormHandler
     */
    protected $userFormHandler;

    protected function setUp()
    {
        $this->formFactory = $this->createMock(FormFactoryInterface::class);
        $this->formClassName = UserType::class;
        $this->entityManager = $this->createMock(EntityManager::class);
        $this->passwordGenerator = $this->createMock(GeneratorInterface::class);

        $this->userFormHandler = new UserFormHandler(
            $this->formFactory,
            $this->formClassName,
            $this->entityManager,
            $this->passwordGenerator
        );
    }

    public function testCreateFormNoUser()
    {
        $editor = $this->createUserMock();

        $this->formFactory->expects($this->once())
            ->method('create')
            ->with($this->equalTo($this->formClassName), $this->isInstanceOf(User::class), $this->equalTo(['editor' => $editor]));

        $this->userFormHandler->createForm($editor);
    }

    public function testCreateForm()
    {
        $editor = $this->createUserMock();
        $user = $this->createUserMock();

        $this->formFactory->expects($this->once())
            ->method('create')
            ->with($this->equalTo($this->formClassName), $this->equalTo($user), $this->equalTo(['editor' => $editor]));

        $this->userFormHandler->createForm($editor, $user);
    }

    public function testProcessUserIsEditor()
    {
        $userId = 42;
        $editor = $this->createUserMock();
        $user = $this->createUserMock();
        $form = $this->createMock(FormInterface::class);
        $request = $this->createMock(Request::class);
        $formConfig = $this->createMock(FormConfigInterface::class);

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
            ->method('getConfig')
            ->will($this->returnValue($formConfig));
        $formConfig->expects($this->once())
            ->method('getOption')
            ->with($this->equalTo('editor'))
            ->will($this->returnValue($editor));
        $form->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($user));
        $user->expects($this->once())
            ->method('getId')
            ->will($this->returnValue($userId));
        $editor->expects($this->once())
            ->method('getId')
            ->will($this->returnValue($userId));


        $form->expects($this->once())
            ->method('addError');
        $this->entityManager->expects($this->never())
            ->method('persist');

        $this->assertFalse($this->userFormHandler->process($form, $request));
    }

    public function testProcess()
    {
        $editor = $this->createUserMock();
        $user = $this->createUserMock();
        $form = $this->createMock(FormInterface::class);
        $request = $this->createMock(Request::class);
        $formConfig = $this->createMock(FormConfigInterface::class);

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
            ->method('getConfig')
            ->will($this->returnValue($formConfig));
        $formConfig->expects($this->once())
            ->method('getOption')
            ->with($this->equalTo('editor'))
            ->will($this->returnValue($editor));
        $form->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($user));
        $user->expects($this->once())
             ->method('getId')
             ->will($this->returnValue(21));
        $editor->expects($this->once())
           ->method('getId')
           ->will($this->returnValue(42));


        $form->expects($this->never())
            ->method('addError');
        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with($this->equalTo($user));
        $this->entityManager->expects($this->once())
            ->method('flush');

        $this->assertTrue($this->userFormHandler->process($form, $request));
    }
}
