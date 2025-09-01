<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        \App\Models\Board::class => \App\Policies\Api\V1\BoardPolicy::class,
        \App\Models\Column::class => \App\Policies\Api\V1\ColumnPolicy::class,
        \App\Models\Task::class => \App\Policies\Api\V1\TaskPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
    }
}