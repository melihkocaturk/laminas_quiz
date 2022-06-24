<?php

declare(strict_types=1);

namespace User\Plugin;

use Laminas\Authentication\AuthenticationService;
use Laminas\Authentication\AuthenticationServiceInterface;
use Laminas\Mvc\Controller\Plugin\AbstractPlugin;
use User\Model\Table\UsersTable;

class AuthPlugin extends AbstractPlugin
{
    protected $authenticationService;
    protected $usersTable;

    public function __construct(AuthenticationService $authenticationService, UsersTable $usersTable)
    {
        $this->authenticationService = $authenticationService;
        $this->usersTable = $usersTable;
    }

    public function __invoke()
    {
        if (!$this->authenticationService instanceof AuthenticationServiceInterface) {
            return;
        }

        if (!$this->authenticationService->hasIdentity()) {
            return;
        }

        return $this->usersTable->fetchAccountById(
            (int) $this->authenticationService->getIdentity()->id
        );
    }
}
