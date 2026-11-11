<?php

declare(strict_types=1);

// Test Twig-Config fürs Widget: TWIG_AUTOLOAD=/pfad/vendor/autoload.php php tests/widget-config.php
// config() und path() wie in Shopware nachgebaut

$autoload = getenv('TWIG_AUTOLOAD') ?: '/tmp/twig/vendor/autoload.php';
if (!is_file($autoload)) {
    fwrite(STDERR, "Twig fehlt: composer require twig/twig:^3 -d /tmp/twig\n");
    exit(1);
}
require $autoload;

$tpl = (string) file_get_contents(__DIR__ . '/../src/Resources/views/storefront/layout/accessibility-panel.html.twig');
$render = static function (array $cfg) use ($tpl): array {
    $twig = new \Twig\Environment(new \Twig\Loader\ArrayLoader(['t' => $tpl]), ['autoescape' => 'html']);
    $twig->addFunction(new \Twig\TwigFunction('config', static fn (string $k) => $cfg[substr($k, strlen('MgdBarrierefrei.config.'))] ?? null));
    $twig->addFunction(new \Twig\TwigFunction('path', static fn (string $r) => '/barrierefreiheit'));
    $html = $twig->render('t');
    preg_match('#<script type="application/json" id="mgd-barrierefrei-config">(.*)</script>#s', $html, $m);

    return [$html, json_decode($m[1] ?? 'null', true)];
};

$fail = 0;
$check = static function (bool $ok, string $msg) use (&$fail): void {
    echo ($ok ? 'OK   ' : 'FAIL ') . $msg . "\n";
    $fail += $ok ? 0 : 1;
};

[, $c] = $render([]);
$check(is_array($c) && $c['position'] === 'left' && $c['lang'] === 'auto' && $c['theme'] === 'auto' && $c['offset'] === 0, 'Defaults');

[, $c] = $render(['position' => 'right', 'defaultLang' => 'en', 'defaultTheme' => 'dark', 'accentColor' => '#123456', 'offset' => 60, 'buttonLabel' => 'Hilfe']);
$check($c['position'] === 'right' && $c['lang'] === 'en' && $c['theme'] === 'dark' && $c['accent'] === '#123456' && $c['offset'] === 60 && $c['labels']['de'] === 'Hilfe', 'Gespeicherte Standards');

[, $c] = $render(['defaultLang' => 'fr', 'defaultTheme' => 'x', 'accentColor' => 'red', 'offset' => 900]);
$check($c['lang'] === 'auto' && $c['theme'] === 'auto' && $c['accent'] === '#b91c1c' && $c['offset'] === 200, 'Ungültig > Standard');

[$html] = $render(['buttonLabel' => '</script><script>alert(1)</script>']);
$check(substr_count($html, '</script>') === 1, 'Config kann Script-Tag nicht schließen');

exit($fail > 0 ? 1 : 0);
