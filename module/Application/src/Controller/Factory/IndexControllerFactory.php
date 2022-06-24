<?php

declare(strict_types=1);

namespace Application\Controller\Factory;

use Application\Controller\IndexController;
use Application\Model\Table\QuizzesTable;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class IndexControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        return new IndexController(
            $container->get(QuizzesTable::class)
        );
    }
}