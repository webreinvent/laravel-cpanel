<?php

namespace WebReinvent\CPanel;

use Illuminate\Support\ServiceProvider;

class CPanelServiceProvider extends ServiceProvider {

    public function boot() {
        $this->registerConfigs();
    }

    public function register() {

        $this->registerConfigs();

        $this->app->bind('CPanel', function ($app) {
            return new CPanel();
        });
    }

    private function registerConfigs() {
        $configPath = __DIR__ . '/Config/cpanel.php';
        $this->publishes([$configPath => config_path('cpanel.php')], 'config');
    }

}