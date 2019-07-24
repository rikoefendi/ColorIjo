<?php

namespace ColorIjo\MediaManager\Events;

use Illuminate\Support\Facades\Event;
use ColorIjo\MediaManager\Events\FileUploaded;
use ColorIjo\MediaManager\Listeners\FileUploadListener;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        FileUploaded::class => [
            FileUploadListener::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
        //
    }
}
