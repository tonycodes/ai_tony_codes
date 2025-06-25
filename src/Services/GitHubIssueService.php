<?php

namespace TonyCodes\GitFlowReporter\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GitHubIssueService
{
    protected array $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * Create a GitHub issue
     */
    public function createIssue(array $issueData): string
    {
        $token = $this->config['github']['token'] ?? null;
        $owner = $this->config['github']['owner'] ?? null;
        $repo = $this->config['github']['repo'] ?? null;

        if (!$token || !$owner || !$repo) {
            throw new \Exception('GitHub configuration is incomplete');
        }

        $title = $this->formatTitle($issueData);
        $body = $this->formatBody($issueData);
        $labels = $this->getLabels($issueData);

        $payload = [
            'title' => $title,
            'body' => $body,
            'labels' => $labels
        ];

        $response = Http::withHeaders([
            'Authorization' => 'token ' . $token,
            'Accept' => 'application/vnd.github.v3+json',
            'User-Agent' => 'GitFlow-Reporter-v1.0'
        ])->post("https://api.github.com/repos/{$owner}/{$repo}/issues", $payload);

        if (!$response->successful()) {
            Log::error('GitFlow Reporter: GitHub API error', [
                'status' => $response->status(),
                'response' => $response->body(),
                'payload' => $payload
            ]);
            throw new \Exception('Failed to create GitHub issue: ' . $response->body());
        }

        $issueData = $response->json();
        return $issueData['number'];
    }

    /**
     * Get issue URL
     */
    public function getIssueUrl(string $issueNumber): string
    {
        $owner = $this->config['github']['owner'];
        $repo = $this->config['github']['repo'];
        
        return "https://github.com/{$owner}/{$repo}/issues/{$issueNumber}";
    }

    /**
     * Format the issue title
     */
    protected function formatTitle(array $issueData): string
    {
        $template = $this->config['templates'][$issueData['type']] ?? $this->config['templates']['bug'];
        $prefix = $template['title_prefix'] ?? '📝';
        
        $priorityIndicator = '';
        if (in_array($issueData['priority'], ['high', 'urgent'])) {
            $priorityIndicator = $issueData['priority'] === 'urgent' ? ' [URGENT]' : ' [HIGH]';
        }

        return "{$prefix} {$issueData['title']}{$priorityIndicator}";
    }

    /**
     * Format the issue body
     */
    protected function formatBody(array $issueData): string
    {
        $user = $issueData['user'];
        $context = $issueData['context'] ?? [];
        $appInfo = $issueData['app_info'] ?? [];
        
        $body = "## Issue Description\n\n";
        $body .= $issueData['description'] . "\n\n";

        $body .= "## Reporter Information\n\n";
        if ($user) {
            $body .= "- **User**: {$user->name} ({$user->email})\n";
            $body .= "- **User ID**: {$user->id}\n";
            
            if (method_exists($user, 'roles') && $user->roles) {
                $body .= "- **Role**: " . $user->roles->pluck('name')->join(', ') . "\n";
            }
            
            if (isset($user->school) && $user->school) {
                $body .= "- **Organization**: {$user->school->name}\n";
            }
        } else {
            $body .= "- **User**: Guest\n";
        }
        $body .= "\n";

        $body .= "## Technical Context\n\n";
        if (isset($context['url'])) {
            $body .= "- **Page URL**: {$context['url']}\n";
        }
        if (isset($context['userAgent'])) {
            $body .= "- **User Agent**: {$context['userAgent']}\n";
        }
        if (isset($context['viewport'])) {
            $body .= "- **Viewport**: {$context['viewport']['width']}x{$context['viewport']['height']}\n";
        }
        if (isset($context['timestamp'])) {
            $body .= "- **Timestamp**: {$context['timestamp']}\n";
        }

        // Add application info
        if (!empty($appInfo)) {
            $body .= "- **Environment**: " . ($appInfo['environment'] ?? 'unknown') . "\n";
            $body .= "- **Laravel Version**: " . ($appInfo['laravel_version'] ?? 'unknown') . "\n";
            $body .= "- **PHP Version**: " . ($appInfo['php_version'] ?? 'unknown') . "\n";
            $body .= "- **GitFlow Reporter**: v" . ($appInfo['package_version'] ?? '1.0.0') . "\n";
        }
        $body .= "\n";

        // Add screenshot if available
        if (!empty($issueData['screenshot_url'])) {
            $body .= "## Screenshot\n\n";
            $body .= "![Screenshot]({$issueData['screenshot_url']})\n\n";
        }

        // Add browser storage data if available (limited)
        if (!empty($context['localStorage']) || !empty($context['sessionStorage'])) {
            $body .= "## Browser Storage (Limited)\n\n";
            if (!empty($context['localStorage'])) {
                $storageKeys = is_array($context['localStorage']) ? array_keys($context['localStorage']) : [];
                $body .= "**Local Storage Keys**: " . implode(', ', $storageKeys) . "\n";
            }
            if (!empty($context['sessionStorage'])) {
                $storageKeys = is_array($context['sessionStorage']) ? array_keys($context['sessionStorage']) : [];
                $body .= "**Session Storage Keys**: " . implode(', ', $storageKeys) . "\n";
            }
            $body .= "\n";
        }

        $body .= "---\n";
        $body .= "_This issue was automatically created by [GitFlow Reporter](https://tonycodes.com/gitflow-reporter) - Premium Laravel Error Reporting._";

        return $body;
    }

    /**
     * Get labels for the issue
     */
    protected function getLabels(array $issueData): array
    {
        $template = $this->config['templates'][$issueData['type']] ?? $this->config['templates']['bug'];
        $labels = $template['labels'] ?? ['auto-generated'];

        // Priority labels
        $priorityLabels = [
            'urgent' => 'priority:urgent',
            'high' => 'priority:high',
            'medium' => 'priority:medium',
            'low' => 'priority:low'
        ];

        if (isset($priorityLabels[$issueData['priority']])) {
            $labels[] = $priorityLabels[$issueData['priority']];
        }

        // User role labels
        $user = $issueData['user'];
        if ($user && method_exists($user, 'hasRole')) {
            if ($user->hasRole('admin')) {
                $labels[] = 'reported-by:admin';
            } elseif ($user->hasRole('manager')) {
                $labels[] = 'reported-by:manager';
            } elseif ($user->hasRole('user')) {
                $labels[] = 'reported-by:user';
            }
        } elseif (!$user) {
            $labels[] = 'reported-by:guest';
        }

        return array_unique($labels);
    }
}