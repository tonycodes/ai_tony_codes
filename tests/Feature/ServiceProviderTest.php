<?php

namespace TonyCodes\GitFlowReporter\Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use TonyCodes\GitFlowReporter\Tests\TestCase;
use TonyCodes\GitFlowReporter\Services\LicenseValidator;
use TonyCodes\GitFlowReporter\Services\GitHubIssueService;

class ServiceProviderTest extends TestCase
{
    #[Test]
    public function it_registers_the_license_validator_service()
    {
        $service = $this->app->make(LicenseValidator::class);
        
        $this->assertInstanceOf(LicenseValidator::class, $service);
    }

    #[Test]
    public function it_registers_the_github_issue_service()
    {
        $service = $this->app->make(GitHubIssueService::class);
        
        $this->assertInstanceOf(GitHubIssueService::class, $service);
    }

    #[Test]
    public function it_publishes_configuration_file()
    {
        $this->artisan('vendor:publish', [
            '--tag' => 'gitflow-reporter-config',
            '--force' => true
        ]);

        $this->assertFileExists(config_path('gitflow-reporter.php'));
    }

    #[Test]
    public function it_loads_configuration_correctly()
    {
        $config = config('gitflow-reporter');
        
        $this->assertIsArray($config);
        $this->assertArrayHasKey('license_key', $config);
        $this->assertArrayHasKey('github', $config);
        $this->assertArrayHasKey('ui', $config);
    }
}