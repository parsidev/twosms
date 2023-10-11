<?php
namespace Parsidev\MeliPayamak;
use Illuminate\Support\ServiceProvider;
class MeliPayamakServiceProvider extends ServiceProvider {
    protected $defer = true;
    public function boot() {
        // $this->publishes([
        //     __DIR__ . '/../../config/melipayamak.php' => config_path('melipayamak.php'),
        // ]);
    }
    public function register() {
        // $this->app->singleton('melipayamak', function($app) {
        //     $config = config('melipayamak');
        //     return new MeliPayamak($config);
        // });
    }
    public function provides() {
        return ['melipayamak'];
    }
}
