<?php

declare(strict_types=1);

namespace User;

use Laminas\Db\Adapter\Adapter;
use User\Model\Table\ForgotPasswordTable;
use User\Model\Table\RolesTable;
use User\Model\Table\UsersTable;
use User\Plugin\AuthPlugin;
use User\Plugin\Factory\AuthPluginFactory;
use User\View\Helper\AuthHelper;

class Module
{
    public function getConfig(): array
    {
        /** @var array $config */
        $config = include __DIR__ . '/../config/module.config.php';
        return $config;
    }

    public function getServiceConfig(): array
    {
        return [
            'factories' => [
                UsersTable::class => function($sm) {
                    $dbAdapter = $sm->get(Adapter::class);
                    return new UsersTable($dbAdapter);
                },
                ForgotPasswordTable::class => function($sm) {
                    $dbAdapter = $sm->get(Adapter::class);
                    return new ForgotPasswordTable($dbAdapter);
                },
                RolesTable::class => function($sm) {
                    $dbAdapter = $sm->get(Adapter::class);
                    return new RolesTable($dbAdapter);
                },
            ],
            
        ];
    }

    public function getControllerPluginConfig()
    {
        return [
            'aliases' => [
                'authPlugin' => AuthPlugin::class,
            ],
            'factories' => [
                AuthPlugin::class => AuthPluginFactory::class,
            ],
        ];
    }

    public function getViewHelperConfig()
    {
        return [
            'aliases' => [
                'authHelper' => AuthHelper::class,
            ],
            'factories' => [
                AuthHelper::class => AuthPluginFactory::class,
            ],
        ];
    }
}
