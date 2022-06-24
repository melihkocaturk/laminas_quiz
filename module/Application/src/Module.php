<?php

declare(strict_types=1);

namespace Application;

use Application\Form\Quiz\CreateForm;
use Laminas\Db\Adapter\Adapter;
use Application\Model\Table\AnswersTable;
use Application\Model\Table\CategoriesTable;
use Application\Model\Table\QuizzesTable;
use Application\Model\Table\TalliesTable;

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
                AnswersTable::class => function($sm) {
                    $dbAdapter = $sm->get(Adapter::class);
                    return new AnswersTable($dbAdapter);
                },
                CategoriesTable::class => function($sm) {
                    $dbAdapter = $sm->get(Adapter::class);
                    return new CategoriesTable($dbAdapter);
                },
                QuizzesTable::class => function($sm) {
                    $dbAdapter = $sm->get(Adapter::class);
                    return new QuizzesTable($dbAdapter);
                },
                TalliesTable::class => function($sm) {
                    $dbAdapter = $sm->get(Adapter::class);
                    return new TalliesTable($dbAdapter);
                },
            ],
        ];
    }

    public function getFormElementConfig()
    {
        return [
            'factories' => [
                CreateForm::class => function($sm) {
                    $categoriesTable = $sm->get(CategoriesTable::class);
                    return new CreateForm($categoriesTable);
                },
            ],
        ];
    } 
}
