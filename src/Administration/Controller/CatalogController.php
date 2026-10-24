<?php

declare(strict_types=1);

namespace MgdBarrierefrei\Administration\Controller;

use MgdBarrierefrei\Catalog\PluginCatalog;
use Shopware\Core\Framework\Routing\ApiRouteScope;
use Shopware\Core\PlatformRequest;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

/** Liste "Weitere Plugins" für die Admin-Seite; nur lesen, installiert wird hier nichts. */
#[Route(defaults: [PlatformRequest::ATTRIBUTE_ROUTE_SCOPE => [ApiRouteScope::ID]])]
final class CatalogController
{
    public function __construct(private readonly PluginCatalog $catalog)
    {
    }

    #[Route(path: '/api/_action/mgd-barrierefrei/catalog', name: 'api.action.mgd_barrierefrei.catalog', defaults: [PlatformRequest::ATTRIBUTE_ACL => ['system_config:read']], methods: ['POST'])]
    public function list(Request $request): JsonResponse
    {
        $refresh = $request->getPayload()->getBoolean('refresh');
        $result = $this->catalog->load($refresh);
        if ($result === null) {
            // Shop läuft normal weiter, nur die Liste fehlt
            return new JsonResponse(['available' => false, 'items' => [], 'fetchedAt' => null, 'stale' => false]);
        }

        return new JsonResponse(['available' => true] + $result);
    }
}
