<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Doctrine\DBAL\Types\Type;
use App\Http\ViewComposers\NotificationComposer;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        if ($this->app->runningInConsole()) {
            try {
                $platform = \DB::getDoctrineSchemaManager()->getDatabasePlatform();

                if (!Type::hasType('enum')) {
                    Type::addType('enum', \Doctrine\DBAL\Types\StringType::class);
                }

                $platform->markDoctrineTypeCommented(Type::getType('enum'));
                $platform->registerDoctrineTypeMapping('enum', 'string');
            } catch (\Throwable $e) {
                // Log or silently fail in non-DB contexts
            }
        }
        
        // Register view composers
        View::composer('*', NotificationComposer::class);
    }
}
