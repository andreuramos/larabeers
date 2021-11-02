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
        if (env('APP_ENV', 'production') === 'local') {
            $this->app->bind(
                \Larabeers\Domain\Common\ImageUploader::class,
                \Larabeers\External\Images\Uploader\LocalStorageImageUploader::class
            );
        } else {
            $this->app->bind(
                \Larabeers\Domain\Common\ImageUploader::class,
                \Larabeers\External\Images\Uploader\CloudinaryImageUploader::class
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
        $this->app->bind(
            \Larabeers\Domain\Label\LabelRepository::class,
            \Larabeers\External\EloquentLabelRepository::class
        );
        $this->app->bind(
            \Larabeers\Domain\Beer\StyleRepository::class,
            \Larabeers\External\EloquentStyleRepository::class
        );
        $this->app->bind(
            \Larabeers\Domain\Label\TagRepository::class,
            \Larabeers\External\EloquentTagRepository::class
        );
        $this->app->bind(
            \Larabeers\Domain\Location\FlagRepository::class,
            \Larabeers\External\Images\FlagpediaFlagRepository::class
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
