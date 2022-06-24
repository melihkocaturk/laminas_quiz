<?php

declare(strict_types=1);

namespace User\Controller\Factory;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use User\Controller\PasswordController;
use User\Model\Table\UsersTable;
use User\Model\Table\ForgotPasswordTable;

class PasswordControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        return new PasswordController(
            $container->get(ForgotPasswordTable::class),
            $container->get(UsersTable::class)
        );
    }
}