<?php

declare(strict_types=1);

namespace User\Controller;

use Laminas\Authentication\AuthenticationService;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\Mail\Message;
use Laminas\Mail\Transport\Sendmail;
use Laminas\View\Model\ViewModel;
use User\Form\Auth\ForgotPasswordForm;
use User\Form\Auth\ResetPasswordForm;
use User\Model\Table\ForgotPasswordTable;
use User\Model\Table\UsersTable;

class PasswordController extends AbstractActionController
{
    private $forgotPasswordTable;
    private $usersTable;

    public function __construct(ForgotPasswordTable $forgotPasswordTable, UsersTable $usersTable)
    {
        $this->forgotPasswordTable = $forgotPasswordTable;
        $this->usersTable = $usersTable;
    }

    public function indexAction()
    {
        $auth = new AuthenticationService();

        if ($auth->hasIdentity()) {
            return $this->redirect()->toRoute('home');
        }

        $forgotPasswordForm = new ForgotPasswordForm;
        /** @var \Laminas\Http\Request $request */
        $request = $this->getRequest();

        if ($request->isPost()) {
            $formData = $request->getPost()->toArray();
            $forgotPasswordForm->setInputFilter($this->usersTable->getForgotPasswordFormFilter());
            $forgotPasswordForm->setData($formData);

            if($forgotPasswordForm->isValid()) {
                try {
                    $data = $forgotPasswordForm->getData();
                    $info = $this->usersTable->fetchAccountByEmail($data['email']);
                    $id = (int) $info->getId();

                    $this->forgotPasswordTable->deleteToken($id);
                    $token = $this->forgotPasswordTable->generateToken(20);
                    $this->forgotPasswordTable->saveToken((string) $token, (int) $id);

                    $file = dirname(dirname(dirname(__FILE__))) . DS . 'data' . DS . 'tpl' . DS . 'forgotPassword.tpl';
                    $file = file_get_contents($file);
                    $body = str_replace('#USERNAME#', $info->getUsername(), $file);
                    $link = $_SERVER['HTTP_HOST'] . '/reset_password/' . $id . '/' . $token;
                    $body = str_replace('#LINK#', $link, $body);

                    var_dump($body); die();

                    $message = new Message();
                    $message->setFrom('info@laminas.quiz')
                            ->setTo($info->getEmail())
                            ->setSubject('I forgot my password')
                            ->setBody($body);
                    
                    if ($message->isValid()) {
                        (new Sendmail())->send($message);
                    }

                    $this->flashMessenger()->addSuccessMessage('Message succesfully send.');
                    return $this->redirect()->toRoute('home');
                } catch (\RuntimeException $exception) {
                    $this->flashMessenger()->addErrorMessage($exception->getMessage());
                    return $this->redirect()->refresh();
                }
            }

        }

        return (new ViewModel(['form' => $forgotPasswordForm]))->setTemplate('user/auth/forgot_password');
    }

    public function resetPasswordAction()
    {
        $auth = new AuthenticationService();

        if ($auth->hasIdentity()) {
            return $this->redirect()->toRoute('home');
        }

        $id = (int) $this->params()->fromRoute('id');
        $token = (string) $this->params()->fromRoute('token');
        $info = $this->usersTable->fetchAccountById((int) $id);

        if (empty($id) || empty($token) || !$info) {
            return $this->notFoundAction();
        }

        $this->forgotPasswordTable->clearOldTokens();
        $verify = $this->forgotPasswordTable->fetchToken($token, (int) $info->getId());

        if (!$verify) {
            $this->flashMessenger()->addErrorMessage('Invalid token!');
            return $this->redirect()->toRoute('forgot_password');
        }

        $resetPasswordForm = new ResetPasswordForm;
        /** @var \Laminas\Http\Request $request */
        $request = $this->getRequest();

        if ($request->isPost()) {
            $formData = $request->getPost()->toArray();
            $resetPasswordForm->setInputFilter($this->usersTable->getResetPasswordFormFilter());
            $resetPasswordForm->setData($formData);

            if($resetPasswordForm->isValid()) {
                try {
                    $data = $resetPasswordForm->getData();

                    if ($this->usersTable->updatePassword($data['new_password'], (int) $info->getId())) {
                        $this->forgotPasswordTable->deleteToken((int) $info->getId());
                    }

                    $this->flashMessenger()->addSuccessMessage('Your password successfully reset. You can now login.');
                    return $this->redirect()->toRoute('login');
                } catch (\RuntimeException $exception) {
                    $this->flashMessenger()->addErrorMessage($exception->getMessage());
                    return $this->redirect()->refresh();
                }
            }

        }

        return (new ViewModel(['form' => $resetPasswordForm]))->setTemplate('user/auth/reset_password');
    }
}