<?php

declare(strict_types=1);

namespace MgdBarrierefrei;

use Doctrine\DBAL\Connection;
use MgdBarrierefrei\InstallReport\InstallReporter;
use Shopware\Core\Framework\Plugin;
use Shopware\Core\Framework\Plugin\Context\ActivateContext;
use Shopware\Core\Framework\Plugin\Context\InstallContext;
use Shopware\Core\Framework\Plugin\Context\UpdateContext;
use Shopware\Core\System\SystemConfig\SystemConfigService;

/** Einstiegspunkt; Einstellungen und Storefront bleiben bewusst getrennt. */
final class MgdBarrierefrei extends Plugin
{
    // Free-Lizenz anmelden bei Install/Aktivieren/Update
    public function postInstall(InstallContext $installContext): void
    {
        parent::postInstall($installContext);
        $this->report();
    }

    public function activate(ActivateContext $activateContext): void
    {
        parent::activate($activateContext);
        $this->report();
    }

    public function postUpdate(UpdateContext $updateContext): void
    {
        parent::postUpdate($updateContext);
        $this->report();
    }

    // Lifecycle darf nie scheitern
    private function report(): void
    {
        try {
            if ($this->container === null) {
                return;
            }
            $config = $this->container->get(SystemConfigService::class);
            $db = $this->container->get(Connection::class);
            if ($config instanceof SystemConfigService && $db instanceof Connection) {
                (new InstallReporter($config, $db))->run(true);
            }
        } catch (\Throwable) {
        }
    }
}
