<?php declare(strict_types=1);

namespace EcomSec\SecurityHeaders\Service;

use Shopware\Core\System\SystemConfig\SystemConfigService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class HeaderService
{
    public function addSecurityHeaders(Response $response, Request $request, SystemConfigService $config, ?string $salesChannelId): void
    {
        $this->addContentSecurityPolicy($response, $config, $salesChannelId);
        $this->addContentSecurityPolicyReportOnly($response, $config, $salesChannelId);
        $this->addPermissionsPolicy($response, $config, $salesChannelId);
        $this->addStrictTransportSecurity($response, $request, $config, $salesChannelId);
        $this->addXFrameOptions($response, $config, $salesChannelId);
        $this->addXContentTypeOptions($response, $config, $salesChannelId);
        $this->addReferrerPolicy($response, $config, $salesChannelId);
    }

    private function addContentSecurityPolicy(Response $response, SystemConfigService $config, ?string $salesChannelId): void
    {
        $enabled = $config->get("EcomSecSecurityHeaders.config.cspEnabled", $salesChannelId);
        if (!$enabled) {
            return;
        }

        $cspValue = $config->get("EcomSecSecurityHeaders.config.cspValue", $salesChannelId);
        if (!empty($cspValue) && !$response->headers->has("Content-Security-Policy")) {
            $response->headers->set("Content-Security-Policy", $cspValue);
        }
    }

    private function addContentSecurityPolicyReportOnly(Response $response, SystemConfigService $config, ?string $salesChannelId): void
    {
        $enabled = $config->get("EcomSecSecurityHeaders.config.cspReportOnlyEnabled", $salesChannelId);
        if (!$enabled) {
            return;
        }

        $cspReportOnlyValue = $config->get("EcomSecSecurityHeaders.config.cspReportOnlyValue", $salesChannelId);
        if (!empty($cspReportOnlyValue) && !$response->headers->has("Content-Security-Policy-Report-Only")) {
            $response->headers->set("Content-Security-Policy-Report-Only", $cspReportOnlyValue);
        }
    }

    private function addPermissionsPolicy(Response $response, SystemConfigService $config, ?string $salesChannelId): void
    {
        $enabled = $config->get("EcomSecSecurityHeaders.config.permissionsPolicyEnabled", $salesChannelId);
        if (!$enabled) {
            return;
        }

        $permissionsPolicyValue = $config->get("EcomSecSecurityHeaders.config.permissionsPolicyValue", $salesChannelId);
        if (!empty($permissionsPolicyValue) && !$response->headers->has("Permissions-Policy")) {
            $response->headers->set("Permissions-Policy", $permissionsPolicyValue);
        }
    }

    private function addStrictTransportSecurity(Response $response, Request $request, SystemConfigService $config, ?string $salesChannelId): void
    {
        // Use native Symfony method to check for secure connection
        if (!$request->isSecure()) {
            return;
        }
        
        $enabled = $config->get("EcomSecSecurityHeaders.config.hstsEnabled", $salesChannelId);
        if (!$enabled) {
            return;
        }

        if (!$response->headers->has("Strict-Transport-Security")) {
            $hstsValue = $config->get("EcomSecSecurityHeaders.config.hstsValue", $salesChannelId);
            if (!empty($hstsValue)) {
                $response->headers->set("Strict-Transport-Security", $hstsValue);
            }
        }
    }

    private function addXFrameOptions(Response $response, SystemConfigService $config, ?string $salesChannelId): void
    {
        $enabled = $config->get("EcomSecSecurityHeaders.config.xFrameOptionsEnabled", $salesChannelId);
        if (!$enabled) {
            return;
        }

        if (!$response->headers->has("X-Frame-Options")) {
            $xFrameOptionsValue = $config->get("EcomSecSecurityHeaders.config.xFrameOptionsValue", $salesChannelId);
            if (!empty($xFrameOptionsValue)) {
                $response->headers->set("X-Frame-Options", $xFrameOptionsValue);
            }
        }
    }

    private function addXContentTypeOptions(Response $response, SystemConfigService $config, ?string $salesChannelId): void
    {
        $enabled = $config->get("EcomSecSecurityHeaders.config.xContentTypeOptionsEnabled", $salesChannelId);
        if (!$enabled) {
            return;
        }

        if (!$response->headers->has("X-Content-Type-Options")) {
            $xContentTypeOptionsValue = $config->get("EcomSecSecurityHeaders.config.xContentTypeOptionsValue", $salesChannelId);
            if (!empty($xContentTypeOptionsValue)) {
                $response->headers->set("X-Content-Type-Options", $xContentTypeOptionsValue);
            }
        }
    }

    private function addReferrerPolicy(Response $response, SystemConfigService $config, ?string $salesChannelId): void
    {
        $enabled = $config->get("EcomSecSecurityHeaders.config.referrerPolicyEnabled", $salesChannelId);
        if (!$enabled) {
            return;
        }

        if (!$response->headers->has("Referrer-Policy")) {
            $referrerPolicyValue = $config->get("EcomSecSecurityHeaders.config.referrerPolicyValue", $salesChannelId);
            if (!empty($referrerPolicyValue)) {
                $response->headers->set("Referrer-Policy", $referrerPolicyValue);
            }
        }
    }
}
