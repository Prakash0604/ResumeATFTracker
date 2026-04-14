<?php

namespace App\Providers;
use App\Services\ResumeAnalyzerService;
use App\Services\TextExtractorService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
         // Register TextExtractor as singleton (stateless, reusable)
        $this->app->singleton(TextExtractorService::class);

        // ResumeAnalyzerService depends on TextExtractorService
        // Laravel will auto-inject it via constructor autowiring
        $this->app->bind(ResumeAnalyzerService::class, function ($app) {
            return new ResumeAnalyzerService(
                $app->make(TextExtractorService::class)
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \Illuminate\Pagination\Paginator::useBootstrap();
    }
}
