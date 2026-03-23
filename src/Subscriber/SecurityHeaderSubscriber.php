<?php declare(strict_types=1);

namespace EcomSec\SecurityHeaders\Subscriber;

use EcomSec\SecurityHeaders\Service\HeaderService;
use Psr\Log\LoggerInterface;
use Shopware\Core\PlatformRequest;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class SecurityHeaderSubscriber implements EventSubscriberInterface
{
    private HeaderService $headerService;
    private SystemConfigService $systemConfigService;
    private LoggerInterface $logger;

    public function __construct(
        HeaderService $headerService,
        SystemConfigService $systemConfigService,
        LoggerInterface $logger
    ) {
        $this->headerService = $headerService;
        $this->systemConfigService = $systemConfigService;
        $this->logger = $logger;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::RESPONSE => ['addSecurityHeaders', 0],
        ];
    }

    public function addSecurityHeaders(ResponseEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $response = $event->getResponse();
        $request = $event->getRequest();

        try {
            $salesChannelId = $request->attributes->get(PlatformRequest::ATTRIBUTE_SALES_CHANNEL_ID);

            $this->headerService->addSecurityHeaders($response, $request, $this->systemConfigService, $salesChannelId);
        } catch (\Exception $e) {
            $this->logger->error('EcomSec SecurityHeaders Plugin Error: ' . $e->getMessage(), [
                'exception' => $e,
            ]);
        }
    }
}
