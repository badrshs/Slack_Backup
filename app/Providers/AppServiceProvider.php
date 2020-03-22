<?php

namespace App\Providers;

use App\Socialite\SlackProvider;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
       $this->bootSlackSocialite();
    }

    private function bootSlackSocialite()
    {
        $socialite = $this->app->make('Laravel\Socialite\Contracts\Factory');
        $socialite->extend(
            'slack',
            function ($app) use ($socialite) {
                $config = $app['config']['services.slack'];
                return $socialite->buildProvider(SlackProvider::class, $config);
            }
        );
    }
}
