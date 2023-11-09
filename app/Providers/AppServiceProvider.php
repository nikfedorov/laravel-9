<?php

namespace App\Providers;

use App\Jobs\SendMail;
use App\Services\ElasticsearchHelper;
use App\Services\RedisHelper;
use App\Utilities\Contracts\ElasticsearchHelperInterface;
use App\Utilities\Contracts\RedisHelperInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->bindHelperImplementations();
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->injectIntoSendMailJob();
    }

    /**
     * Bind elasticsearch and redis helper implementations.
     */
    protected function bindHelperImplementations()
    {
        $this->app->bind(ElasticsearchHelperInterface::class, ElasticsearchHelper::class);
        $this->app->bind(RedisHelperInterface::class, RedisHelper::class);
    }

    /**
     * Inject dependencies for SendMail job.
     */
    protected function injectIntoSendMailJob()
    {
        $this->app->bindMethod([SendMail::class, 'handle'], function ($job, $app) {

            // resolve helper instances
            $elasticsearchHelper = $app->make(ElasticsearchHelperInterface::class);
            $redisHelper = $app->make(RedisHelperInterface::class);

            // pass helpers to handle method
            return $job->handle($elasticsearchHelper, $redisHelper);
        });
    }
}
