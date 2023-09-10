<?php

namespace App\Providers;

use App\Domains\Accessibles\Listeners\SyncAccessGrantsListener;
use App\Domains\Shop\Events\OrderUpsertedEvent;
use App\Domains\Shop\Events\ProductUpsertedEvent;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        //
    ];

    public function boot(): void
    {
    }

    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}

