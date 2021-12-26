<?php

namespace Sajad\Upload;

use Illuminate\Support\ServiceProvider;

class UploadServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind('upload' , function (){
            return new Upload;
        });

        $this->mergeConfigFrom(__DIR__ . '/Config/upload.php', 'upload');
    }

    public function boot()
    {
        $this->publishes([
            __DIR__ . '/Database' => database_path('/migrations'),
            __DIR__ . '/Config' => config_path('')

        ]);
    }
}
