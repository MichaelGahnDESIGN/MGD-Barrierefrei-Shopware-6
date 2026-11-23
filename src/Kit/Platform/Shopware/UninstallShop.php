<?php

declare(strict_types=1);

namespace MgdBarrierefrei\Kit\Platform\Shopware;

// Mini-Shop nur fürs Deinstallieren: Plugin-Dienste sind da evtl schon weg, SystemConfigService ist public
// Version aus dem UninstallContext, Updates gehen hier nie
final class UninstallShop implements ShopwareApi
{
    /** @param object $config Shopware\Core\System\SystemConfig\SystemConfigService */
    public function __construct(private readonly object $config, private readonly string $technicalName, private readonly string $version)
    {
    }

    public function plugin(string $technicalName, ?object $context = null): ?array
    {
        if ($technicalName !== $this->technicalName) {
            return null;
        }

        return ['version' => $this->version, 'upgrade' => null, 'installed' => true, 'active' => false, 'path' => '', 'composer' => false];
    }

    public function extract(string $zipFile, object $context): void
    {
        throw new \LogicException('Beim Deinstallieren nicht erlaubt.');
    }

    public function update(string $technicalName, object $context): void
    {
        throw new \LogicException('Beim Deinstallieren nicht erlaubt.');
    }

    public function runtimeAllowed(): bool
    {
        return false;
    }

    public function domain(): string
    {
        return '';
    }

    public function get(string $key): mixed
    {
        return $this->config->get($key);
    }

    // null = Shopware löscht den Eintrag
    public function set(string $key, mixed $value): void
    {
        $this->config->set($key, $value);
    }
}
