<?php namespace App\Providers;

use Laravel\Lumen\Providers\EventServiceProvider as ServiceProvider;
use Neomerx\LimoncelloIlluminate\Events\BoardCreatedEvent;
use Neomerx\LimoncelloIlluminate\Events\BoardUpdatedEvent;
use Neomerx\LimoncelloIlluminate\Events\CommentCreatedEvent;
use Neomerx\LimoncelloIlluminate\Events\CommentUpdatedEvent;
use Neomerx\LimoncelloIlluminate\Events\PostCreatedEvent;
use Neomerx\LimoncelloIlluminate\Events\PostUpdatedEvent;
use Neomerx\LimoncelloIlluminate\Listeners\BoardCreatedListener;
use Neomerx\LimoncelloIlluminate\Listeners\BoardUpdatedListener;
use Neomerx\LimoncelloIlluminate\Listeners\CommentCreatedListener;
use Neomerx\LimoncelloIlluminate\Listeners\CommentUpdatedListener;
use Neomerx\LimoncelloIlluminate\Listeners\PostCreatedListener;
use Neomerx\LimoncelloIlluminate\Listeners\PostUpdatedListener;

/**
 * @package App
 */
class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        BoardCreatedEvent::class   => [BoardCreatedListener::class],
        BoardUpdatedEvent::class   => [BoardUpdatedListener::class],
        PostCreatedEvent::class    => [PostCreatedListener::class],
        PostUpdatedEvent::class    => [PostUpdatedListener::class],
        CommentCreatedEvent::class => [CommentCreatedListener::class],
        CommentUpdatedEvent::class => [CommentUpdatedListener::class],
    ];
}
