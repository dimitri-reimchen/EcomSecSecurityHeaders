<?php declare(strict_types=1);

namespace EcomSec\SecurityHeaders\Subscriber;

use Shopware\Core\System\SystemConfig\SystemConfigService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface; // Diese Zeile fehlte!
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use EcomSec\SecurityHeaders\Service\HeaderService;

class MaintenanceModeSubscriber implements EventSubscriberInterface
{
    private HeaderService $headerService;
    private SystemConfigService $systemConfigService;

    public function __construct(
        HeaderService $headerService,
        SystemConfigService $systemConfigService
    ) {
        $this->headerService = $headerService;
        $this->systemConfigService = $systemConfigService;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            // Höchste Priorität, um sicherzustellen, dass es vor allen anderen Subscribern ausgeführt wird
            KernelEvents::RESPONSE => ['addSecurityHeadersToMaintenance', 9999]
        ];
    }

    public function addSecurityHeadersToMaintenance(ResponseEvent $event): void
    {
        $response = $event->getResponse();
        $request = $event->getRequest();
        
        // Prüfen, ob es sich um eine Wartungsseite handelt
        // Wartungsseiten haben typischerweise einen 503 Status-Code
        if ($response->getStatusCode() !== 503) {
            return;
        }
        
        // Für Wartungsseiten verwenden wir die globale Konfiguration
        // da der SalesChannel-Kontext möglicherweise nicht verfügbar ist
        $this->headerService->addSecurityHeaders($response, $request, $this->systemConfigService, null);
    }
}
