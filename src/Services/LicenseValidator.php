<?php

namespace TonyCodes\GitFlowReporter\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class LicenseValidator
{
    protected string $licenseKey;
    protected string $licenseServer;
    protected int $cacheMinutes = 60; // Cache validation for 1 hour

    public function __construct(?string $licenseKey = null, ?string $licenseServer = null)
    {
        $this->licenseKey = $licenseKey ?? config('gitflow-reporter.license_key');
        $this->licenseServer = $licenseServer ?? config('gitflow-reporter.license_server');
    }

    /**
     * Check if license is valid
     */
    public function isValid(): bool
    {
        if (empty($this->licenseKey)) {
            return false;
        }

        // Check cache first
        $cacheKey = 'gitflow_reporter_license_' . md5($this->licenseKey);
        
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        // Validate with license server
        $isValid = $this->validateWithServer();
        
        // Cache the result
        Cache::put($cacheKey, $isValid, now()->addMinutes($this->cacheMinutes));
        
        return $isValid;
    }

    /**
     * Validate license with remote server
     */
    protected function validateWithServer(): bool
    {
        try {
            $response = Http::timeout(10)->post($this->licenseServer . '/validate', [
                'license_key' => $this->licenseKey,
                'package' => 'gitflow-reporter',
                'domain' => request()->getHost(),
                'laravel_version' => app()->version(),
                'php_version' => PHP_VERSION,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['valid'] ?? false;
            }

            // If server is down, allow usage but log the issue
            Log::warning('GitFlow Reporter: License server unavailable', [
                'status' => $response->status(),
                'response' => $response->body()
            ]);
            
            return true; // Graceful fallback

        } catch (\Exception $e) {
            Log::error('GitFlow Reporter: License validation failed', [
                'error' => $e->getMessage()
            ]);
            
            return true; // Graceful fallback
        }
    }

    /**
     * Get license expiration date
     */
    public function getExpirationDate(): ?string
    {
        try {
            $response = Http::timeout(5)->post($this->licenseServer . '/info', [
                'license_key' => $this->licenseKey,
                'package' => 'gitflow-reporter',
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['expires_at'] ?? null;
            }
        } catch (\Exception $e) {
            // Silently fail
        }

        return null;
    }

    /**
     * Get license information
     */
    public function getLicenseInfo(): array
    {
        try {
            $response = Http::timeout(5)->post($this->licenseServer . '/info', [
                'license_key' => $this->licenseKey,
                'package' => 'gitflow-reporter',
            ]);

            if ($response->successful()) {
                return $response->json();
            }
        } catch (\Exception $e) {
            // Silently fail
        }

        return [
            'valid' => false,
            'message' => 'Unable to fetch license information'
        ];
    }

    /**
     * Force refresh license validation
     */
    public function refresh(): bool
    {
        $cacheKey = 'gitflow_reporter_license_' . md5($this->licenseKey);
        Cache::forget($cacheKey);
        
        return $this->isValid();
    }
}