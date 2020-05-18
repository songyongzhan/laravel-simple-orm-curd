<?php

namespace Songyz\Providers;

use Illuminate\Support\ServiceProvider;
use Songyz\Command\GeneratorValidatorRequestCommand;
use Songyz\Commands\Scaffold;

/**
 * 配置文件发布
 * Class ScaffoldConfigPublishProvider
 * @package Songyz\Providers
 * @author songyz <574482856@qq.com>
 * @date 2020/5/10 20:03
 */
class ScaffoldConfigPublishProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $configPath = __DIR__ . '/../config/songyz_scaffold.php';

        $this->publishes([
            $configPath => base_path('config' . DIRECTORY_SEPARATOR . 'songyz_scaffold.php'),
        ]);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        // Merge configs
        $this->mergeConfigFrom(
            __DIR__ . '/../config/songyz_scaffold.php',
            'songyz_scaffold'
        );

        $this->app->singleton('songyz.scaffold.generator', function ($app) {
            return new Scaffold();
        });
        $this->commands([
            'songyz.scaffold.generator'
        ]);
    }
}
