<?php namespace App\Providers;

use Illuminate\Contracts\Auth\Access\Gate as GateInterface;
use Illuminate\Support\ServiceProvider;
use Neomerx\LimoncelloIlluminate\Api\Policies\BoardPolicy;
use Neomerx\LimoncelloIlluminate\Api\Policies\CommentPolicy;
use Neomerx\LimoncelloIlluminate\Api\Policies\PostPolicy;
use Neomerx\LimoncelloIlluminate\Api\Policies\RolePolicy;
use Neomerx\LimoncelloIlluminate\Api\Policies\UserPolicy;
use Neomerx\LimoncelloIlluminate\Database\Models\Board;
use Neomerx\LimoncelloIlluminate\Database\Models\Comment;
use Neomerx\LimoncelloIlluminate\Database\Models\Post;
use Neomerx\LimoncelloIlluminate\Database\Models\Role;
use Neomerx\LimoncelloIlluminate\Database\Models\User;

/**
 * @package App
 */
class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        Board::class   => BoardPolicy::class,
        Comment::class => CommentPolicy::class,
        Post::class    => PostPolicy::class,
        Role::class    => RolePolicy::class,
        User::class    => UserPolicy::class,
    ];

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
    }

    /**
     * Boot the authentication services for the application.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
    }

    /**
     * Register the application's policies.
     *
     * @return void
     */
    public function registerPolicies()
    {
        $gate = $this->app->make(GateInterface::class);
        foreach ($this->policies as $key => $value) {
            $gate->policy($key, $value);
        }
    }
}
