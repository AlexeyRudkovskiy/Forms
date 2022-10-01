<?php

namespace YProjects\Forms\Providers;

use Illuminate\Support\ServiceProvider;

class FormsServiceProvider extends ServiceProvider
{

    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/forms.php' => config_path('forms.php')
        ]);
    }

}
