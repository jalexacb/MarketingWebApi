<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class JwtAuthServiceProvider extends ServiceProvider {


    public function boot(){
    }

     /**
     * Register the service provider.
     *
     * @return void
     */

    public function register(){

        require_once app()->path().'\Http\Controllers\AuthController.php';
    }
}