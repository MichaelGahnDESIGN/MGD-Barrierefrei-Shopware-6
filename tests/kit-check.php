<?php

declare(strict_types=1);

// Brückenversion: Baukasten drin, alte Update-/Katalog-Teile abgemeldet, Abmelden beim Deinstallieren
$root = dirname(__DIR__);
$fail = 0;
$check = static function (bool $ok, string $name) use (&$fail): void {
    echo ($ok ? 'OK   ' : 'FAIL ') . $name . "\n";
    $fail += $ok ? 0 : 1;
};
$read = static fn (string $f): string => (string) file_get_contents($root . '/' . $f);
$services = $read('src/Resources/config/services.xml');
$routes = $read('src/Resources/config/routes.xml');
$kitServices = $read('src/Kit/Platform/Shopware/Resources/config/services.xml');
$plugin = $read('src/MgdBarrierefrei.php');
$adminJs = $read('src/Resources/public/administration/js/mgd-barrierefrei.js');

$check(is_file($root . '/src/Kit/Platform/Shopware/ShopwareKit.php') && is_file($root . '/src/Kit/Platform/Shopware/administration/mgd-kit.js'), 'Baukasten liegt unter src/Kit');
$check(str_contains($services, '<import resource="../../Kit/Platform/Shopware/Resources/config/services.xml"/>'), 'Kit-Dienste importiert');
$check(str_contains($routes, '<import resource="../../Kit/Platform/Shopware/Resources/config/routes.xml"/>'), 'Kit-Routen importiert');
$check(!str_contains($services, 'GitHubReleaseChecker') && !str_contains($services, 'CatalogController') && !str_contains($services, 'PluginCatalog'), 'alter Update-Check + alte Liste nicht mehr angemeldet');
$check(!str_contains($routes, 'Administration/Controller'), 'alte Admin-Routen raus');
$check(str_contains($kitServices, '<argument>mgd-barrierefrei-shopware</argument>') && str_contains($kitServices, '<argument>MgdBarrierefrei</argument>') && str_contains($kitServices, '<argument>plugin-barrierefrei-shopware</argument>'), 'Produkt, technischer Name, Katalog-Kennung');
$check((bool) preg_match('~<argument>false</argument>\s*</service>~', $kitServices), 'Lizenz aus');
$check(str_contains($plugin, "ShopwareKit::forUninstall(\$uninstallContext, \$this->container->get(SystemConfigService::class), 'mgd-barrierefrei-shopware')?->uninstall(\$uninstallContext)"), 'Abmelden beim Deinstallieren');
$check($adminJs === $read('src/Kit/Platform/Shopware/administration/mgd-kit.js'), 'Admin-JS = Kit-Seite');
$check(!str_contains($adminJs, "Module.register('mgd-barrierefrei-catalog'"), 'alte Seite Weitere Plugins nicht mehr angemeldet');
$check(!is_dir($root . '/src/Install'), 'alter Install-Client raus');
$placeholders = 0;
foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root . '/src/Kit', FilesystemIterator::SKIP_DOTS)) as $f) {
    $placeholders += str_contains((string) file_get_contents((string) $f), '{{KIT_') ? 1 : 0;
}
$check($placeholders === 0, 'keine Platzhalter im Baukasten');
exit($fail === 0 ? 0 : 1);
