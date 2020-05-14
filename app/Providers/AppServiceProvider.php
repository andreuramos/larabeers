<?php

namespace App\Providers;

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
        if(env('APP_ENV', 'production') == 'local'){
            $this->app->bind(
                \Larabeers\External\Images\Uploader\ImageUploader::class,
                \Larabeers\External\Images\Uploader\LocalStorageImageUploader::class
            );
        } else {
            $this->app->bind(
                \Larabeers\External\Images\Uploader\ImageUploader::class,
                \Larabeers\External\Images\Uploader\GoogleDriveImageUploader::class
            );
        }

        $this->app->bind(
            \Larabeers\Domain\Beer\BeerRepository::class,
            \Larabeers\External\EloquentBeerRepository::class
        );
        $this->app->bind(
            \Larabeers\Domain\Brewer\BrewerRepository::class,
            \Larabeers\External\EloquentBrewerRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
