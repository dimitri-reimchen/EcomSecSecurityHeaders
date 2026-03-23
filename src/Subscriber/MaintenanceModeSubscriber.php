<?php declare(strict_types=1);

namespace EcomSec\SecurityHeaders\Subscriber;

use EcomSec\SecurityHeaders\Service\HeaderService;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

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
            KernelEvents::RESPONSE => ['addSecurityHeadersToMaintenance', 9999],
        ];
    }

    public function addSecurityHeadersToMaintenance(ResponseEvent $event): void
    {
        $response = $event->getResponse();
        $request = $event->getRequest();

        // Only apply to maintenance pages (503 status code)
        if ($response->getStatusCode() !== 503) {
            return;
        }

        // Use global configuration as SalesChannel context may not be available
        $this->headerService->addSecurityHeaders($response, $request, $this->systemConfigService, null);
    }
}
