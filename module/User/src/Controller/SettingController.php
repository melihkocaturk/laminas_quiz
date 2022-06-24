<?php

declare(strict_types=1);

namespace User\Controller;

use Laminas\Authentication\AuthenticationService;
use Laminas\Crypt\Password\Bcrypt;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use PhpParser\Node\Expr\FuncCall;
use User\Form\Setting\DeleteForm;
use User\Form\Setting\EmailForm;
use User\Form\Setting\PasswordForm;
use User\Form\Setting\UsernameForm;
use User\Model\Table\UsersTable;

class SettingController extends AbstractActionController
{
    private $usersTable;

    public function __construct(UsersTable $usersTable)
    {
        $this->usersTable = $usersTable;
    }

    public function indexAction()
    {
        $auth = new AuthenticationService();

        if (!$auth->hasIdentity()) {
            return $this->redirect()->toRoute('login');
        }
        
        return new ViewModel();
    }

    public function emailAction()
    {
        $auth = new AuthenticationService();

        if (!$auth->hasIdentity()) {
            return $this->redirect()->toRoute('login');
        }

        $emailForm = new EmailForm();
        /** @var \Laminas\Http\Request $request */
        $request = $this->getRequest();

        if ($request->isPost()) {
            $formData = $request->getPost()->toArray();
            $emailForm->setInputFilter($this->usersTable->getEmailFormFilter());
            $emailForm->setData($formData);

            if($emailForm->isValid()) {
                try {
                    $data = $emailForm->getData();
                    $this->usersTable->updateEmail($data['new_email'], (int) $this->authPlugin()->getId());

                    $this->flashMessenger()->addSuccessMessage('Email succesfully changed.');
                    return $this->redirect()->toRoute('profile', 
                        [
                            'id' => $this->authPlugin()->getId(),
                            'username' => $this->authPlugin()->getUsername(),
                        ]
                    );
                } catch (\RuntimeException $exception) {
                    $this->flashMessenger()->addErrorMessage($exception->getMessage());
                    return $this->redirect()->refresh();
                }
            }
        }

        return new ViewModel(['form' => $emailForm]);
    }

    public function passwordAction()
    {
        $auth = new AuthenticationService();

        if (!$auth->hasIdentity()) {
            return $this->redirect()->toRoute('login');
        }

        $passwordForm = new PasswordForm();
        /** @var \Laminas\Http\Request $request */
        $request = $this->getRequest();

        if ($request->isPost()) {
            $formData = $request->getPost()->toArray();
            $passwordForm->setInputFilter($this->usersTable->getPasswordFormFilter());
            $passwordForm->setData($formData);

            if($passwordForm->isValid()) {
                try {
                    $data = $passwordForm->getData();
                    $hash = new Bcrypt();

                    if ($hash->verify($data['current_password'], $this->authPlugin()->getPassword())) {
                        $this->usersTable->updatePassword($data['new_password'], (int) $this->authPlugin()->getId());

                        $this->flashMessenger()->addSuccessMessage('Password succesfully changed.');
                        return $this->redirect()->toRoute('logout'); 
                    } else {
                        $this->flashMessenger()->addErrorMessage('Incorrect password!');
                        return $this->redirect()->refresh();
                    }
                } catch (\RuntimeException $exception) {
                    $this->flashMessenger()->addErrorMessage($exception->getMessage());
                    return $this->redirect()->refresh();
                }
            }
        }

        return new ViewModel(['form' => $passwordForm]);
    }

    public function usernameAction()
    {
        $auth = new AuthenticationService();

        if (!$auth->hasIdentity()) {
            return $this->redirect()->toRoute('login');
        }

        $usernameForm = new UsernameForm();
        /** @var \Laminas\Http\Request $request */
        $request = $this->getRequest();

        if ($request->isPost()) {
            $formData = $request->getPost()->toArray();
            $usernameForm->setInputFilter($this->usersTable->getUsernameFormFilter());
            $usernameForm->setData($formData);

            if($usernameForm->isValid()) {
                try {
                    $data = $usernameForm->getData();
                    $this->usersTable->updateUsername($data['new_username'], (int) $this->authPlugin()->getId());

                    $this->flashMessenger()->addSuccessMessage('Username succesfully changed.');
                    return $this->redirect()->toRoute('profile', 
                        [
                            'id' => $this->authPlugin()->getId(),
                            'username' => $this->authPlugin()->getUsername(),
                        ]
                    );
                } catch (\RuntimeException $exception) {
                    $this->flashMessenger()->addErrorMessage($exception->getMessage());
                    return $this->redirect()->refresh();
                }
            }
        }

        return new ViewModel(['form' => $usernameForm]);
    }
    
    public function deleteAction()
    {
        $auth = new AuthenticationService();

        if (!$auth->hasIdentity()) {
            return $this->redirect()->toRoute('login');
        }

        $deleteForm = new DeleteForm();
        /** @var \Laminas\Http\Request $request */
        $request = $this->getRequest();

        if ($request->isPost()) {
            $formData = $request->getPost()->toArray();
            $deleteForm->setData($formData);

            if($deleteForm->isValid()) {
                try {
                    if ($request->getPost()->get('delete_account') == 'Yes') {
                        $data = $deleteForm->getData();
                        $this->usersTable->deleteAccount((int) $data['id']);
    
                        $this->flashMessenger()->addSuccessMessage('Account suspended.');
                        return $this->redirect()->toRoute('logout');
                    }

                    return $this->redirect()->toRoute('home');
                } catch (\RuntimeException $exception) {
                    $this->flashMessenger()->addErrorMessage($exception->getMessage());
                    return $this->redirect()->refresh();
                }
            }
        }

        return new ViewModel(['form' => $deleteForm]);
    }
}