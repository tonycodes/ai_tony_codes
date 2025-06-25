<?php

namespace TonyCodes\GitFlowReporter\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use TonyCodes\GitFlowReporter\Services\GitHubIssueService;
use TonyCodes\GitFlowReporter\Services\LicenseValidator;

class GitFlowReporterController extends Controller
{
    protected GitHubIssueService $githubService;
    protected LicenseValidator $licenseValidator;

    public function __construct(GitHubIssueService $githubService, LicenseValidator $licenseValidator)
    {
        $this->githubService = $githubService;
        $this->licenseValidator = $licenseValidator;
        
        $this->middleware('gitflow-reporter.licensed');
    }

    /**
     * Store a new error report and create GitHub issue
     */
    public function store(Request $request): JsonResponse
    {
        // Validate license in real-time
        if (!$this->licenseValidator->isValid()) {
            return response()->json([
                'success' => false,
                'message' => 'License expired. Please contact support to renew your GitFlow Reporter license.'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'type' => 'required|in:bug,feature,improvement,question,other',
            'title' => 'required|string|max:100',
            'description' => 'required|string|max:5000',
            'priority' => 'nullable|in:low,medium,high,urgent',
            'context' => 'nullable|string|max:10000',
            'screenshot' => 'nullable|image|max:' . config('gitflow-reporter.security.max_file_size', 5120)
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Rate limiting check
            if (!$this->checkRateLimit($request)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Rate limit exceeded. Please wait before submitting another report.'
                ], 429);
            }

            $validated = $validator->validated();
            
            // Handle screenshot upload if present
            $screenshotUrl = null;
            if ($request->hasFile('screenshot') && config('gitflow-reporter.features.screenshots', true)) {
                $screenshotUrl = $this->handleScreenshotUpload($request->file('screenshot'));
            }

            // Prepare issue data
            $issueData = [
                'type' => $validated['type'],
                'title' => $validated['title'],
                'description' => $validated['description'],
                'priority' => $validated['priority'] ?? 'medium',
                'user' => auth()->user(),
                'context' => $this->sanitizeContext(json_decode($validated['context'] ?? '{}', true)),
                'screenshot_url' => $screenshotUrl,
                'app_info' => $this->collectAppInfo()
            ];

            // Create GitHub issue
            $issueNumber = $this->githubService->createIssue($issueData);

            // Log successful report
            Log::info('GitFlow Reporter: Issue created successfully', [
                'package' => 'gitflow-reporter',
                'user_id' => auth()->id(),
                'issue_number' => $issueNumber,
                'type' => $validated['type'],
                'title' => $validated['title']
            ]);

            return response()->json([
                'success' => true,
                'message' => config('gitflow-reporter.notifications.success_message'),
                'issue_number' => $issueNumber,
                'issue_url' => $this->githubService->getIssueUrl($issueNumber)
            ]);

        } catch (\Exception $e) {
            Log::error('GitFlow Reporter: Failed to create error report', [
                'package' => 'gitflow-reporter',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => auth()->id(),
                'request_data' => $request->except(['screenshot'])
            ]);

            return response()->json([
                'success' => false,
                'message' => config('gitflow-reporter.notifications.error_message')
            ], 500);
        }
    }

    /**
     * Get package configuration for frontend
     */
    public function config(): JsonResponse
    {
        return response()->json([
            'ui' => config('gitflow-reporter.ui'),
            'features' => config('gitflow-reporter.features'),
            'notifications' => config('gitflow-reporter.notifications'),
            'security' => [
                'max_file_size' => config('gitflow-reporter.security.max_file_size'),
                'allowed_file_types' => config('gitflow-reporter.security.allowed_file_types'),
            ]
        ]);
    }

    /**
     * Handle screenshot upload
     */
    protected function handleScreenshotUpload($file): ?string
    {
        try {
            $filename = 'gitflow-reporter/' . uniqid() . '_' . time() . '.png';
            $path = $file->storeAs('gitflow-reporter', basename($filename), 'public');
            return Storage::disk('public')->url($path);
        } catch (\Exception $e) {
            Log::warning('GitFlow Reporter: Screenshot upload failed', [
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Check rate limiting
     */
    protected function checkRateLimit(Request $request): bool
    {
        $limit = config('gitflow-reporter.security.rate_limit', 5);
        $key = 'gitflow-reporter:rate-limit:' . ($request->user()?->id ?? $request->ip());
        
        $cache = app('cache');
        $attempts = $cache->get($key, 0);
        
        if ($attempts >= $limit) {
            return false;
        }
        
        $cache->put($key, $attempts + 1, now()->addHour());
        return true;
    }

    /**
     * Sanitize context data for security
     */
    protected function sanitizeContext(array $context): array
    {
        if (!config('gitflow-reporter.security.sanitize_data', true)) {
            return $context;
        }

        // Remove sensitive data
        $sensitiveKeys = ['password', 'token', 'secret', 'key', 'auth', 'session'];
        
        return $this->recursiveKeyCleaner($context, $sensitiveKeys);
    }

    /**
     * Recursively clean sensitive keys
     */
    private function recursiveKeyCleaner(array $data, array $sensitiveKeys): array
    {
        foreach ($data as $key => $value) {
            $lowerKey = strtolower($key);
            
            // Check if key contains sensitive terms
            foreach ($sensitiveKeys as $sensitive) {
                if (str_contains($lowerKey, $sensitive)) {
                    $data[$key] = '[REDACTED]';
                    continue 2;
                }
            }
            
            // Recursively clean nested arrays
            if (is_array($value)) {
                $data[$key] = $this->recursiveKeyCleaner($value, $sensitiveKeys);
            }
        }
        
        return $data;
    }

    /**
     * Collect application information
     */
    protected function collectAppInfo(): array
    {
        return [
            'package_version' => '1.0.0',
            'laravel_version' => app()->version(),
            'php_version' => PHP_VERSION,
            'environment' => app()->environment(),
            'license_expires' => $this->licenseValidator->getExpirationDate(),
        ];
    }
}