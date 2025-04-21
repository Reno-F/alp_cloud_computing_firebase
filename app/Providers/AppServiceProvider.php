<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
          // Registrasi Firebase ke dalam aplikasi, jika perlu
    $this->app->singleton('firebase', function ($app) {
        $serviceAccount = ServiceAccount::fromJsonFile(public_path('firebase/cloud-computing-alp-firebase-adminsdk-fbsvc-b86050d660.json'));
        return (new Factory)
            ->withServiceAccount($serviceAccount)
            ->withDatabaseUri(env('FIREBASE_DATABASE_URL'))
            ->createDatabase();
    });


    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();
    }
}
