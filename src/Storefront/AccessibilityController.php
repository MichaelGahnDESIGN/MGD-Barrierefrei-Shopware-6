<?php

declare(strict_types=1);

namespace MgdBarrierefrei\Storefront;

use Shopware\Core\PlatformRequest;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Shopware\Storefront\Controller\StorefrontController;
use Shopware\Storefront\Framework\Routing\StorefrontRouteScope;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/** Rendert eine zentrale, konfigurierbare und stets aktuelle Erklärung. */
#[Route(defaults: [PlatformRequest::ATTRIBUTE_ROUTE_SCOPE => [StorefrontRouteScope::ID]])]
final class AccessibilityController extends StorefrontController
{
    public function __construct(private readonly SystemConfigService $config)
    {
    }

    #[Route(path: '/barrierefreiheit', name: 'frontend.mgd_barrierefrei.statement', methods: ['GET'])]
    public function statement(): Response
    {
        return $this->renderStorefront('@MgdBarrierefrei/storefront/page/accessibility-statement.html.twig', [
            'statement' => [
                'organisation' => $this->text('organisation', 'Ihr Unternehmen'),
                'website' => $this->text('websiteUrl', ''),
                'responsible' => $this->text('responsibleName', ''),
                'role' => $this->text('responsibleRole', ''),
                'email' => $this->text('contactEmail', ''),
                'phone' => $this->text('contactPhone', ''),
                'status' => $this->status(),
                'barriers' => $this->text('knownBarriers', ''),
                'updatedAt' => $this->text('statementDate', date('Y-m-d')),
            ],
        ]);
    }

    private function text(string $key, string $fallback): string
    {
        $value = $this->config->get('MgdBarrierefrei.config.' . $key);

        return is_string($value) ? trim($value) : $fallback;
    }

    private function status(): string
    {
        return match ($this->text('statementStatus', 'teilweise')) {
            'vollstaendig' => 'vollständig barrierefrei',
            'nicht' => 'noch nicht vollständig barrierefrei',
            default => 'teilweise barrierefrei',
        };
    }
}
