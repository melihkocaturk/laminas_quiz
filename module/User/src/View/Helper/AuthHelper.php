<?php

declare(strict_types=1);

namespace User\View\Helper;

use Interop\Container\ContainerInterface;
use Laminas\Authentication\AuthenticationService;
use Laminas\View\Helper\AbstractHelper;
use User\Model\Table\UsersTable;
use User\Plugin\AuthPlugin;

class AuthHelper extends AbstractHelper
{
    protected $authPlugin;

    public function getAuthPlugin()
    {
        return $this->authPlugin;
    }

    public function setAuthPlugin($authPlugin)
    {
        if (!$this->authPlugin instanceof AuthPlugin) {
            throw new \InvalidArgumentException();
        }

        $this->authPlugin = $authPlugin;
    }

    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        if (null === $this->authPlugin) {
            return new AuthHelper(
                $this->setAuthPlugin(
                    new AuthPlugin(
                        $container->get(AuthenticationService::class),
                        $container->get(UsersTable::class)
                    )
                )
            );
        }

        return $this->getAuthPlugin();
    }
}