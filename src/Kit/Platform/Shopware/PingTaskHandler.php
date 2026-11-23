<?php

declare(strict_types=1);

namespace MgdBarrierefrei\Kit\Platform\Shopware;

use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\MessageQueue\ScheduledTask\ScheduledTaskHandler;

// Handler zur PingTask (angemeldet per Tag messenger.message_handler in services.xml, kein Attribut > sonst doppelt): meldet nur, wenn "Installation melden" an ist (macht der Install-Client selbst), wirft nie
final class PingTaskHandler extends ScheduledTaskHandler
{
    public function __construct(EntityRepository $scheduledTaskRepository, private readonly ShopwareKit $kit)
    {
        parent::__construct($scheduledTaskRepository);
    }

    public function run(): void
    {
        try {
            $this->kit->installClient()?->ping();
        } catch (\Throwable) {
        }
    }
}
