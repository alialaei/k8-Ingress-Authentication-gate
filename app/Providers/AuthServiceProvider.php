<?php

namespace App\Providers;

// Import required classes
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate; // Often used with policies, good to have
use Laravel\Passport\Passport; // Make sure this line is present
use Laravel\Passport\Bridge\UserRepository;
use Laravel\Passport\Bridge\RefreshTokenRepository;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Grant\PasswordGrant;
use DateInterval;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // This line is for registering your policies defined in $policies array.
        // It's a method inherited from Illuminate\Foundation\Support\Providers\AuthServiceProvider.
        $this->registerPolicies();
        // Optional: Set token expiry times (adjust as needed)
        Passport::tokensExpireIn(now()->addDays(15));
        Passport::refreshTokensExpireIn(now()->addDays(30));
        Passport::personalAccessTokensExpireIn(now()->addMonths(6));
        $this->enablePasswordGrant();
    }
    protected function enablePasswordGrant()
    {
        $grant = new PasswordGrant(
            app(UserRepository::class),
            app(RefreshTokenRepository::class)
        );

        $grant->setRefreshTokenTTL(new DateInterval('P1M')); // optional, if you want custom refresh token duration

        app(AuthorizationServer::class)->enableGrantType(
            $grant,
            new DateInterval('PT1H') // Access token duration
        );
    }

    /**
     * Register any authentication / authorization services.
     */
    public function register(): void
    {
        // This method is optional, but you can use it to register any additional services or bindings.
        // For example, you might want to bind custom repositories or services related to authentication.
    }
}