<?php

declare(strict_types=1);

namespace MgdBarrierefrei\InstallReport;

use Shopware\Core\Framework\MessageQueue\ScheduledTask\ScheduledTask;

// täglicher Ping (läuft auch über den Admin-Worker)
final class InstallReportTask extends ScheduledTask
{
    public static function getTaskName(): string
    {
        return 'mgd_barrierefrei.install_report';
    }

    public static function getDefaultInterval(): int
    {
        return 86400;
    }
}
