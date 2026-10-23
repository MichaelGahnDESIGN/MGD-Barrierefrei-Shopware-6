<?php

declare(strict_types=1);

namespace MgdBarrierefrei\Update;

use MgdBarrierefrei\MgdBarrierefrei;

/** Prüft ausschließlich freigegebene GitHub-Metadaten und schreibt keine Plugin-Dateien. */
final class GitHubReleaseChecker
{
    private const REPOSITORY = 'MichaelGahnDESIGN/MGD-Barrierefrei-Shopware-6';

    /** @return array{currentVersion: string, latestVersion: ?string, updateAvailable: bool} */
    public function check(): array
    {
        $current = $this->installedVersion();
        $url = 'https://api.github.com/repos/' . self::REPOSITORY . '/releases/latest';
        $context = stream_context_create(['http' => [
            'method' => 'GET',
            'header' => "Accept: application/vnd.github+json\r\nUser-Agent: MGD-Barrierefrei-Shopware-6\r\nX-GitHub-Api-Version: 2022-11-28\r\n",
            'timeout' => 10,
            'ignore_errors' => true,
            'follow_location' => 0,
        ]]);
        $body = @file_get_contents($url, false, $context);
        if (!is_string($body) || strlen($body) > 1_000_000) {
            throw new \RuntimeException('GitHub-Release-Prüfung ist vorübergehend nicht verfügbar.');
        }
        $release = json_decode($body, true, 32, JSON_THROW_ON_ERROR);
        if (!is_array($release) || ($release['draft'] ?? true) || ($release['prerelease'] ?? true)) {
            throw new \RuntimeException('Keine veröffentlichte stabile Release-Metadaten gefunden.');
        }
        $tag = $release['tag_name'] ?? '';
        if (!is_string($tag) || !preg_match('/^v?(0|[1-9][0-9]*)\.(0|[1-9][0-9]*)\.(0|[1-9][0-9]*)$/D', $tag)) {
            throw new \RuntimeException('GitHub-Release verwendet keine zulässige SemVer-Version.');
        }
        $latest = ltrim($tag, 'v');
        $expectedAsset = 'MgdBarrierefrei-' . $latest . '.zip';
        $assets = $release['assets'] ?? null;
        $validAsset = is_array($assets) && count(array_filter($assets, static fn (mixed $asset): bool => is_array($asset)
            && ($asset['name'] ?? null) === $expectedAsset
            && is_int($asset['size'] ?? null) && $asset['size'] > 0
            && is_string($asset['digest'] ?? null) && preg_match('/^sha256:[a-f0-9]{64}$/D', $asset['digest']) === 1)) === 1;
        if (!$validAsset) {
            throw new \RuntimeException('GitHub-Release enthält kein geprüftes Paket für dieses Plugin.');
        }

        return ['currentVersion' => $current, 'latestVersion' => $latest, 'updateAvailable' => version_compare($latest, $current, '>')];
    }

    private function installedVersion(): string
    {
        $source = (new \ReflectionClass(MgdBarrierefrei::class))->getFileName();
        $manifest = is_string($source) ? dirname($source, 2) . '/composer.json' : '';
        $data = is_file($manifest) ? json_decode((string) file_get_contents($manifest), true, 32) : null;
        $version = is_array($data) ? ($data['version'] ?? null) : null;
        if (!is_string($version) || preg_match('/^(0|[1-9][0-9]*)\.(0|[1-9][0-9]*)\.(0|[1-9][0-9]*)$/D', $version) !== 1) {
            throw new \RuntimeException('Installierte Plugin-Version ist ungültig.');
        }

        return $version;
    }
}
