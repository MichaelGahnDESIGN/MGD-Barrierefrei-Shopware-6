<?php

declare(strict_types=1);

// Free-Lizenz: Schalter, Version, Kurztext, Dienste
// Mini-Stubs statt Shopware
namespace Shopware\Core\System\SystemConfig { if (!class_exists(SystemConfigService::class)) { class SystemConfigService {} } }
namespace Doctrine\DBAL { if (!class_exists(Connection::class)) { class Connection {} } }
namespace {
$root = dirname(__DIR__);
$fail = 0;
$check = static function (bool $ok, string $name) use (&$fail): void {
    echo ($ok ? 'OK   ' : 'FAIL ') . $name . "\n";
    $fail += $ok ? 0 : 1;
};
require $root . '/src/InstallReport/InstallReporter.php';
$r = \MgdBarrierefrei\InstallReport\InstallReporter::class;
$check($r::isOn(null) && $r::isOn(true) && $r::isOn('1'), 'Schalter: leer oder an = melden');
$check(!$r::isOn(false) && !$r::isOn('false') && !$r::isOn(0), 'Schalter: aus (auch "false" aus der Konsole) = nicht melden');
$composer = json_decode((string) file_get_contents($root . '/composer.json'), true);
$check($r::version() === $composer['version'], 'Version kommt aus composer.json');
$kurz = 'Meldet sich höchstens einmal am Tag bei theme.michael-gahn.de (Plugin, Version, Shopsystem, Domain, zufällige ID). Abschaltbar in den Einstellungen.';
$check(str_contains($composer['description'], $kurz) && str_contains($composer['extra']['description']['de-DE'], $kurz), 'Kurztext in composer.json');
$config = (string) file_get_contents($root . '/src/Resources/config/config.xml');
$check(str_contains($config, '<name>installReport</name><defaultValue>true</defaultValue>'), 'Schalter in der Plugin-Konfig, ab Werk an');
$services = (string) file_get_contents($root . '/src/Resources/config/services.xml');
$check(str_contains($services, 'shopware.scheduled.task') && str_contains($services, 'messenger.message_handler') && str_contains($services, 'kernel.event_subscriber'), 'Task, Handler und Schalter-Subscriber registriert');
$check($r::PRODUCT === 'mgd-barrierefrei-shopware' && !str_contains((string) file_get_contents($root . '/src/Kit/Install/Settings.php'), 'ENTWURF'), 'Produktname wie Update-Datei, Hinweistext freigegeben');
exit($fail === 0 ? 0 : 1);
}
