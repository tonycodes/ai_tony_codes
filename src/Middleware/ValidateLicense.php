<?php

namespace TonyCodes\GitFlowReporter\Middleware;

use Closure;
use Illuminate\Http\Request;
use TonyCodes\GitFlowReporter\Services\LicenseValidator;

class ValidateLicense
{
    protected LicenseValidator $licenseValidator;

    public function __construct(LicenseValidator $licenseValidator)
    {
        $this->licenseValidator = $licenseValidator;
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Skip validation in local environment
        if (app()->environment('local', 'testing')) {
            return $next($request);
        }

        if (!$this->licenseValidator->isValid()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'GitFlow Reporter license expired or invalid.',
                    'message' => 'Please contact support to renew your license.',
                    'support_url' => 'https://tonycodes.com/support'
                ], 403);
            }

            abort(403, 'GitFlow Reporter license expired or invalid.');
        }

        return $next($request);
    }
}