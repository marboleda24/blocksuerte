<?php

namespace App\Providers;

use App\Providers\Rules\CustomSQLConnector;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;
use Laravel\Fortify\Fortify;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->bind('db.connector.sqlsrv', CustomSQLConnector::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        Inertia::share([
            'errors' => function () {
                return Session::get('errors')
                    ? Session::get('errors')->getBag('default')->getMessages()
                    : (object) [];
            },
            'app_version' => config('app.version'),
        ]);

        Fortify::confirmPasswordsUsing(function ($user, string $password) {
            return Hash::check($password, $user->password);
        });
    }
}
