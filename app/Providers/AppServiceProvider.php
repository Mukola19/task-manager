<?php

namespace App\Providers;

use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Models\Task;
use App\Policies\CategoryPolicy;
use App\Policies\TaskPolicy;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if ($this->app->isLocal()) {
            $this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configurePolicy();
    }

    protected function configurePolicy(): void
    {
        Gate::policy(Category::class, CategoryPolicy::class);
        Gate::policy(Task::class, TaskPolicy::class);

        JsonResource::withoutWrapping();
        AnonymousResourceCollection::withoutWrapping();
    }
}
