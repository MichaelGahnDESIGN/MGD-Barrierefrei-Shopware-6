<?php

declare(strict_types=1);

// Einfacher Test ohne Composer/PHPUnit: php tests/catalog-check.php
// Prüft Feed-Filter + Cache. Kein Netz, kein Shopware nötig.

use MgdBarrierefrei\Catalog\CatalogCache;
use MgdBarrierefrei\Catalog\FeedNormalizer;
use MgdBarrierefrei\Catalog\PluginCatalog;

require __DIR__ . '/../src/Catalog/CatalogCache.php';
require __DIR__ . '/../src/Catalog/FeedNormalizer.php';
require __DIR__ . '/../src/Catalog/PluginCatalog.php';

$failed = 0;
$total = 0;
function check(bool $ok, string $name): void
{
    global $failed, $total;
    ++$total;
    if (!$ok) {
        ++$failed;
        fwrite(STDERR, "FEHLER: $name\n");
    }
}

final class MemoryCache implements CatalogCache
{
    public ?array $record = null;
    public int $writes = 0;

    public function get(): ?array
    {
        return $this->record;
    }

    public function set(array $record, int $ttlSeconds): void
    {
        $this->record = $record;
        ++$this->writes;
    }
}

function entry(string $id, string $platform, array $extra = []): array
{
    return $extra + [
        'id' => $id, 'name' => 'Plugin ' . $id, 'description' => 'Text', 'platform' => $platform, 'tier' => 'free', 'status' => 'live',
        'version' => 'v1.2.3', 'repositoryUrl' => 'https://github.com/MichaelGahnDESIGN/Repo',
        'downloadUrl' => 'https://theme.michael-gahn.de/download/' . $id, 'detailsUrl' => 'https://theme.michael-gahn.de/plugins',
        'related' => [], 'iconUrl' => null, 'imageUrl' => null, 'imageAlt' => null, 'accent' => null,
    ];
}

function feed(array $items): array
{
    return ['schemaVersion' => 1, 'items' => $items];
}

$normalizer = new FeedNormalizer(['shopware', 'shopware6'], 'plugin-barrierefrei-shopware');
$byId = static function (?array $items): array {
    return array_column($items ?? [], null, 'id');
};

// --- Plattformfilter, eigenes Plugin, related
$result = $normalizer->normalize(feed([
    entry('plugin-a-shopware', 'shopware'),
    entry('plugin-b-shopware', 'shopware6'),
    entry('plugin-c-jtl', 'jtl'),
    entry('plugin-d-wordpress', 'wordpress'),
    entry('plugin-e-other', 'other'),
    entry('plugin-barrierefrei-shopware', 'shopware', ['related' => ['plugin-b-shopware']]),
]));
$ids = array_column($result ?? [], 'id');
check($result !== null, 'Feed wird angenommen');
check(!in_array('plugin-barrierefrei-shopware', $ids, true), 'eigene Plugin-ID ist ausgeblendet');
check(!in_array('plugin-c-jtl', $ids, true) && !in_array('plugin-d-wordpress', $ids, true), 'fremde Plattformen fehlen');
check(in_array('plugin-e-other', $ids, true), 'plattformübergreifend (other) bleibt');
check(($ids[0] ?? '') === 'plugin-b-shopware', 'related wird bevorzugt (steht vorn)');
check($byId($result)['plugin-b-shopware']['recommended'] === true && $byId($result)['plugin-a-shopware']['recommended'] === false, 'recommended-Flag stimmt');

// --- Version, Akzent, Bilder
$result = $byId($normalizer->normalize(feed([
    entry('plugin-v1', 'shopware', ['version' => 'v1.0.1', 'accent' => '#CE1517']),
    entry('plugin-v2', 'shopware', ['version' => 'beta', 'accent' => 'red']),
    entry('plugin-v3', 'shopware', ['accent' => 'url(javascript:1)', 'version' => null]),
    entry('plugin-img', 'shopware', [
        'iconUrl' => 'https://theme.michael-gahn.de/uploads/catalog/3f9a0c5d1b7e4c2a8d6f0e1b2c3d4e5f.webp',
        'imageUrl' => 'https://theme.michael-gahn.de/uploads/catalog/a1b2c3d4e5f60718293a4b5c6d7e8f90.webp',
        'imageAlt' => 'Vorschau',
    ]),
    entry('plugin-img-bad', 'shopware', [
        'iconUrl' => 'http://theme.michael-gahn.de/uploads/catalog/3f9a0c5d1b7e4c2a8d6f0e1b2c3d4e5f.webp',
        'imageUrl' => 'https://evil.example/uploads/catalog/a1b2c3d4e5f60718293a4b5c6d7e8f90.webp',
        'imageAlt' => 'Alt ohne Bild',
    ]),
])));
check($result['plugin-v1']['version'] === '1.0.1', 'Version ohne führendes v');
check($result['plugin-v1']['accent'] === '#ce1517', 'Akzent #rrggbb wird kleingeschrieben übernommen');
check($result['plugin-v2']['version'] === null && $result['plugin-v2']['accent'] === null, 'kaputte Version/Akzent werden null');
check($result['plugin-v3']['accent'] === null, 'Akzent mit Code-Anteil wird null');
check($result['plugin-img']['imageUrl'] !== null && $result['plugin-img']['iconUrl'] !== null && $result['plugin-img']['imageAlt'] === 'Vorschau', 'gute Bilder bleiben');
check($result['plugin-img-bad']['iconUrl'] === null && $result['plugin-img-bad']['imageUrl'] === null, 'http und fremder Host bei Bildern wird null');
check($result['plugin-img-bad']['imageAlt'] === '', 'Alt-Text nur zusammen mit Bild');

// --- Links, Allowlist, Bezahl-Plugins
$result = $byId($normalizer->normalize(feed([
    entry('plugin-evil', 'shopware', [
        'downloadUrl' => 'https://evil.example/download/x', 'detailsUrl' => 'javascript:alert(1)',
        'repositoryUrl' => 'https://github.com/SomeoneElse/Repo',
    ]),
    entry('plugin-http', 'shopware', ['downloadUrl' => 'http://theme.michael-gahn.de/download/x', 'detailsUrl' => 'http://theme.michael-gahn.de/plugins']),
    entry('plugin-userinfo', 'shopware', ['downloadUrl' => 'https://theme.michael-gahn.de@evil.example/download/x']),
    entry('plugin-paid', 'shopware', ['tier' => 'paid', 'purchase' => ['mode' => 'info', 'url' => 'https://theme.michael-gahn.de/plugins']]),
    entry('plugin-soon', 'shopware', ['status' => 'announced']),
    entry('plugin-private', 'shopware', ['status' => 'private']),
])));
check($result['plugin-evil']['downloadUrl'] === null, 'fremder Download-Host wird null');
check($result['plugin-evil']['detailsUrl'] === FeedNormalizer::DEFAULT_DETAILS_URL, 'kaputte Detailseite fällt auf /plugins zurück');
check($result['plugin-evil']['repositoryUrl'] === null, 'fremdes GitHub-Konto wird null');
check($result['plugin-http']['downloadUrl'] === null && $result['plugin-http']['detailsUrl'] === FeedNormalizer::DEFAULT_DETAILS_URL, 'http wird abgelehnt');
check($result['plugin-userinfo']['downloadUrl'] === null, 'user@host-Trick wird abgelehnt');
check($result['plugin-paid']['downloadUrl'] === null && $result['plugin-paid']['tier'] === 'paid', 'bezahlt: nie Download-Link');
check($result['plugin-soon']['downloadUrl'] === null && isset($result['plugin-soon']), 'angekündigt: kein Download-Link');
check(!isset($result['plugin-private']), 'private Einträge werden nicht gezeigt');
check($result['plugin-soon']['status'] === 'announced', 'Status announced bleibt');

// --- Text säubern, kaputte Einträge
$result = $byId($normalizer->normalize(feed([
    entry('plugin-html', 'shopware', ['name' => '<b>Fett</b> <script>alert(1)</script>Name', 'description' => "Zeile1\n\n  Zeile2\x00"]),
    entry('plugin-noname', 'shopware', ['name' => '']),
    entry('Plugin Bad ID!', 'shopware'),
    'kein array',
    entry('plugin-dupe', 'shopware'),
    entry('plugin-dupe', 'shopware', ['name' => 'Doppelt']),
])));
check(strpos($result['plugin-html']['name'], '<') === false, 'HTML-Tags fliegen aus dem Namen');
check($result['plugin-html']['description'] === 'Zeile1 Zeile2', 'Beschreibung: Leerzeichen/Steuerzeichen bereinigt');
check(!isset($result['plugin-noname']) && !isset($result['Plugin Bad ID!']), 'Eintrag ohne Namen oder mit kaputter ID entfällt');
check($result['plugin-dupe']['name'] === 'Plugin plugin-dupe', 'doppelte ID: erster Eintrag gewinnt');

// --- Feed-Struktur
check($normalizer->normalize(['schemaVersion' => 2, 'items' => []]) === null, 'schemaVersion 2 wird abgelehnt');
check($normalizer->normalize(['schemaVersion' => 1]) === null, 'Feed ohne items wird abgelehnt');
check($normalizer->normalize('text') === null && $normalizer->normalize(null) === null, 'kein Array wird abgelehnt');
check($normalizer->normalize(feed([])) === [], 'leere Liste ist okay');

// --- echter Feed (nur wenn Datei per Umgebungsvariable da ist)
$live = getenv('MGD_FEED_FILE');
if (is_string($live) && is_file($live)) {
    $items = $normalizer->normalize(json_decode((string) file_get_contents($live), true));
    check(is_array($items) && $items !== [], 'echter Feed lässt sich lesen');
    foreach ($items ?? [] as $item) {
        check($item['version'] === null || strpos($item['version'], 'v') !== 0, 'echter Feed: Version ohne v (' . $item['id'] . ')');
    }
}

// --- PluginCatalog: Cache, Fehler, Refresh
$now = 1_000_000;
$clock = static function () use (&$now): int {
    return $now;
};
$calls = 0;
$body = json_encode(feed([entry('plugin-a-shopware', 'shopware')]));
$ok = static function (string $url) use (&$calls, &$body): array {
    ++$calls;
    return ['status' => 200, 'body' => $body];
};

$cache = new MemoryCache();
$catalog = new PluginCatalog($cache, $normalizer, $ok, $clock);
$first = $catalog->load();
check($first !== null && count($first['items']) === 1 && $first['stale'] === false, 'erster Abruf liefert Liste');
$catalog->load();
check($calls === 1, 'zweiter Abruf kommt aus dem Cache');
$now += 30;
$catalog->load(true);
check($calls === 1, 'Aktualisieren innerhalb 60 s wird gedrosselt');
$now += 60;
$catalog->load(true);
check($calls === 2, 'Aktualisieren nach 60 s holt neu');
$now += 21600 + 1;
$catalog->load();
check($calls === 3, 'nach 6 Stunden wird neu geladen');

// Fehler ohne alte Daten > null, danach 5 Min Ruhe
$calls = 0;
$cache = new MemoryCache();
$fail = static function (string $url) use (&$calls): ?array {
    ++$calls;
    return null;
};
$catalog = new PluginCatalog($cache, $normalizer, $fail, $clock);
check($catalog->load() === null, 'Feed nicht erreichbar > null');
check($catalog->load() === null && $calls === 1, 'nach Fehler keine neue Anfrage innerhalb 5 Min');
$now += 301;
$catalog->load();
check($calls === 2, 'nach 5 Min wieder ein Versuch');

// Fehler mit alten Daten > Notnagel (stale)
$cache = new MemoryCache();
$good = new PluginCatalog($cache, $normalizer, $ok, $clock);
$good->load();
$now += 21600 + 1;
$broken = new PluginCatalog($cache, $normalizer, static fn (string $url): ?array => ['status' => 503, 'body' => ''], $clock);
$stale = $broken->load();
check($stale !== null && $stale['stale'] === true && count($stale['items']) === 1, 'Fehler: alte Liste als Notnagel (stale)');

// kaputte Antworten
foreach ([
    'Status 404' => ['status' => 404, 'body' => $body],
    'Weiterleitung 301' => ['status' => 301, 'body' => ''],
    'kein JSON' => ['status' => 200, 'body' => '<html>'],
    'falsche schemaVersion' => ['status' => 200, 'body' => '{"schemaVersion":9,"items":[]}'],
    'zu groß' => ['status' => 200, 'body' => str_repeat('x', 524289)],
] as $name => $response) {
    $catalog = new PluginCatalog(new MemoryCache(), $normalizer, static fn (string $url): array => $response, $clock);
    check($catalog->load() === null, "Antwort '$name' > null");
}
$catalog = new PluginCatalog(new MemoryCache(), $normalizer, static function (string $url): array {
    throw new RuntimeException('Netz weg');
}, $clock);
check($catalog->load() === null, 'Exception im Transport > null');

// Standard-Transport: fremder Host + http werden ohne Netz abgelehnt
check(PluginCatalog::httpGet('https://evil.example/api/catalog/plugins.json') === null, 'httpGet: fremder Host abgelehnt');
check(PluginCatalog::httpGet('http://theme.michael-gahn.de/api/catalog/plugins.json') === null, 'httpGet: http abgelehnt');
check(PluginCatalog::httpGet('https://theme.michael-gahn.de@evil.example/x') === null, 'httpGet: user@host abgelehnt');
check(parse_url(PluginCatalog::FEED_URL, PHP_URL_HOST) === FeedNormalizer::HOST && str_starts_with(PluginCatalog::FEED_URL, 'https://'), 'Feed-URL: https + erlaubter Host');

// Admin-Einstieg für Shopware 6.7 (Vite-Datei zeigt auf das fertige JS)
$entryFile = __DIR__ . '/../src/Resources/public/administration/.vite/entrypoints.json';
$entry = is_file($entryFile) ? json_decode((string) file_get_contents($entryFile), true) : null;
$entryJs = $entry['entryPoints']['mgd-barrierefrei']['js'][0] ?? '';
check(is_array($entry) && $entryJs === '/bundles/mgdbarrierefrei/administration/js/mgd-barrierefrei.js', '6.7 entrypoints.json: gültig + zeigt auf mgd-barrierefrei.js');
check(is_file(__DIR__ . '/../src/Resources/public/administration/js/mgd-barrierefrei.js'), 'Admin-JS-Datei existiert');

echo ($failed === 0 ? "OK" : "FEHLGESCHLAGEN") . ": $total Prüfungen, $failed Fehler\n";
exit($failed === 0 ? 0 : 1);
