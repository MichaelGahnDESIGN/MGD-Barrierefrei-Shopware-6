<?php

declare(strict_types=1);

namespace MgdBarrierefrei\Administration\Controller;

use MgdBarrierefrei\Update\GitHubReleaseChecker;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\Routing\ApiRouteScope;
use Shopware\Core\PlatformRequest;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

/** Geschützter Status-Endpunkt: Er verrät weder Serverpfade noch Zugangsdaten. */
#[Route(defaults: [PlatformRequest::ATTRIBUTE_ROUTE_SCOPE => [ApiRouteScope::ID]])]
final class UpdateController
{
    public function __construct(private readonly GitHubReleaseChecker $checker)
    {
    }

    #[Route(path: '/api/_action/mgd-barrierefrei/update/check', name: 'api.action.mgd_barrierefrei.update.check', defaults: [PlatformRequest::ATTRIBUTE_ACL => ['system_config:read']], methods: ['POST'])]
    public function check(Context $context): JsonResponse
    {
        try {
            return new JsonResponse($this->checker->check());
        } catch (\Throwable) {
            return new JsonResponse(['error' => 'Update-Status ist derzeit nicht verfügbar.'], 503);
        }
    }
}
