<?php

declare(strict_types=1);

namespace User\Controller;

use Laminas\Authentication\AuthenticationService;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use User\Model\Table\UsersTable;

class AdminController extends AbstractActionController
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

        if (!$this->authPlugin()->getRoleId() == 1) {
            return $this->notFoundAction();
        }

        $paginator = $this->usersTable->fetchAllAccounts(true);
        $page = (int) $this->params()->fromQuery('page', 1);
        $page = ($page < 1) ? 1 : $page;
        $paginator->setCurrentPageNumber($page);
        $paginator->setItemCountPerPage(10);

        return new ViewModel(['accounts' => $paginator]);
    }
}