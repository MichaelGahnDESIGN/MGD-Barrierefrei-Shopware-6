<?php

declare(strict_types=1);

namespace MgdBarrierefrei\InstallReport;

use Doctrine\DBAL\Connection;
use MgdBarrierefrei\Install\InstallClient;
use MgdBarrierefrei\Install\Platform\Shopware;
use Shopware\Core\System\SystemConfig\SystemConfigService;

// Free-Lizenz Anmeldung bei theme.michael-gahn.de - darf nie den Shop oder das Plugin-Setup killen
final class InstallReporter
{
    public const PRODUCT = 'mgd-barrierefrei';
    public const STATE_KEY = 'MgdBarrierefrei.config.installState';
    public const SWITCH_KEY = 'MgdBarrierefrei.config.installReport';

    public function __construct(
        private readonly SystemConfigService $config,
        private readonly Connection $db,
    ) {
    }

    public function client(): ?InstallClient
    {
        $version = self::version();
        if ($version === '') {
            return null;
        }

        return Shopware::client($this->config, self::STATE_KEY, self::PRODUCT, $version, $this->domain());
    }

    // Schalter aus der Plugin-Konfig übernehmen, dann melden (max 1x am Tag ausser $force)
    public function run(bool $force = false): void
    {
        try {
            $c = $this->client();
            if ($c === null) {
                return;
            }
            $want = self::isOn($this->config->get(self::SWITCH_KEY));
            if ($want !== $c->isEnabled()) {
                $c->setEnabled($want);
            }
            $c->ping($force);
        } catch (\Throwable) {
        }
    }

    // null = noch nie gespeichert = an. Konsole speichert "false" als Text
    public static function isOn(mixed $v): bool
    {
        return !in_array($v, [false, 0, '0', 'false', ''], true);
    }

    // Version aus composer.json, nie fest im Code
    public static function version(): string
    {
        $raw = @file_get_contents(dirname(__DIR__, 2) . '/composer.json');
        $data = is_string($raw) ? json_decode($raw, true) : null;
        $v = is_array($data) ? (string) ($data['version'] ?? '') : '';

        return preg_match('/^\d+\.\d+\.\d+$/', $v) === 1 ? $v : '';
    }

    // erste Storefront-Domain, sonst APP_URL
    public function domain(): string
    {
        try {
            $url = $this->db->fetchOne(
                'SELECT d.url FROM sales_channel_domain d INNER JOIN sales_channel s ON s.id = d.sales_channel_id '
                . 'WHERE s.type_id = UNHEX(:type) AND s.active = 1 ORDER BY d.created_at ASC LIMIT 1',
                ['type' => '8a243080f92e4c719546314b577cf82b'],
            );
            $host = is_string($url) ? (string) parse_url($url, PHP_URL_HOST) : '';
            if ($host !== '') {
                return strtolower($host);
            }
        } catch (\Throwable) {
        }
        $app = (string) ($_SERVER['APP_URL'] ?? $_ENV['APP_URL'] ?? getenv('APP_URL') ?: '');

        return strtolower((string) parse_url($app, PHP_URL_HOST));
    }
}
