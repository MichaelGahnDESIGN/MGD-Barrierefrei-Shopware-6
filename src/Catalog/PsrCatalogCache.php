<?php

declare(strict_types=1);

namespace MgdBarrierefrei\Catalog;

use Psr\Cache\CacheItemPoolInterface;

/** Shopware cache.object als Speicher; Fehler im Cache dürfen nie die Admin-Seite kaputt machen. */
final class PsrCatalogCache implements CatalogCache
{
    private const KEY = 'mgd_barrierefrei_catalog_v1';

    public function __construct(private readonly CacheItemPoolInterface $pool)
    {
    }

    public function get(): ?array
    {
        try {
            $item = $this->pool->getItem(self::KEY);
            $value = $item->isHit() ? $item->get() : null;
        } catch (\Throwable) {
            return null;
        }

        return is_array($value) ? $value : null;
    }

    public function set(array $record, int $ttlSeconds): void
    {
        try {
            $item = $this->pool->getItem(self::KEY);
            $item->set($record)->expiresAfter($ttlSeconds);
            $this->pool->save($item);
        } catch (\Throwable) {
            // ohne Cache geht es auch, nur langsamer
        }
    }
}
