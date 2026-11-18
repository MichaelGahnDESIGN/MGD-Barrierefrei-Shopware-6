<?php

declare(strict_types=1);

namespace MgdBarrierefrei\InstallReport;

use Shopware\Core\System\SystemConfig\Event\SystemConfigChangedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

// Schalter in der Plugin-Konfig sofort umsetzen (aus = Eintrag auf der Plattform löschen)
final class InstallSwitchSubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly InstallReporter $reporter)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [SystemConfigChangedEvent::class => 'onChange'];
    }

    public function onChange(SystemConfigChangedEvent $event): void
    {
        if ($event->getKey() !== InstallReporter::SWITCH_KEY || $event->getSalesChannelId() !== null) {
            return;
        }
        $this->reporter->run(true);
    }
}
