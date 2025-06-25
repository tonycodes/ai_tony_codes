<?php

namespace TonyCodes\GitFlowReporter\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use TonyCodes\GitFlowReporter\GitFlowReporterServiceProvider;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function getPackageProviders($app)
    {
        return [
            GitFlowReporterServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');
        config()->set('gitflow-reporter.license_key', 'test-license-key');
        config()->set('gitflow-reporter.github.token', 'test-github-token');
        config()->set('gitflow-reporter.github.owner', 'test-owner');
        config()->set('gitflow-reporter.github.repo', 'test-repo');
    }
}