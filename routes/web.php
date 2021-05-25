<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});


$router->group(['prefix' => 'api'], function ($api) {

    //Usuarios
    
    $api->post('login', 'UsuarioController@login');
    $api->post('usuario/desbloqueoUsuario/{id}', 'UsuarioController@desbloqueoUsuario');
    $api->post('campania/recieve-sms', 'CampaniaController@recieveStatusCallback');
    $api->post('campania/recieve-msg', 'CampaniaController@recieveRespuesta');
    $api->get('campania/get-image', 'CampaniaController@getImage');
    $api->get('usuario/get-image', 'UsuarioController@getImage');
    // $api->post('update', 'UsuarioController@update')->middleware(\ApiAuthMiddleware::class);
    // $api->post('/', ['middleware' => 'api.auth', 'uses' => 'UsuarioController@update']);
    $api->group(['middleware' => 'api.auth'], function($api) { 
        $api->get('usuario/', 'UsuarioController@index'); 
        $api->get('usuario/comprobar/{usuario}', 'UsuarioController@comprobarUsuarioExistente'); 
        $api->get('usuario/{id}', 'UsuarioController@getById'); 
        $api->post('usuario/register', 'UsuarioController@register');
        $api->post('usuario/add-image', 'UsuarioController@addImage');
        $api->post('usuario/{id}', 'UsuarioController@update'); 
        $api->post('usuario/delete/{id}', 'UsuarioController@delete');
    
        // Route::get('user/profile', function() { 
        //  // Uses Auth Middleware 
        // }); comprobarUsuarioExistente

        //Rol
        $api->get('rol/', 'RolController@index');
        $api->get('rol/{id}', 'RolController@getById');
        $api->post('rol/store', 'RolController@store');
        $api->post('rol/{id}', 'RolController@update');
        $api->post('rol/delete/{id}', 'RolController@delete');
        //Menu
        $api->get('menu/', 'MenuController@index');
        $api->get('menu/all', 'MenuController@indexAll');
        $api->get('menu/permisos', 'MenuController@indexByPermisos');

        //Permisos
        $api->get('permiso/filter', 'PermisoController@getPermisoByFilter');
        $api->get('permiso/', 'PermisoController@index');
        
        $api->post('permiso/store', 'PermisoController@store');

        //Parametros
        $api->get('parametro/', 'ParametroController@index');
        $api->get('parametro/seguridad', 'ParametroController@getParametrosSeguridad');
        $api->post('parametro/store', 'ParametroController@store');
        $api->post('parametro/', 'ParametroController@update');
        $api->post('parametro/seguridad', 'ParametroController@updateSeguridad');
        $api->post('parametro/delete/{id}', 'ParametroController@delete');

        //Contactos
        $api->get('contacto/', 'ContactoController@index');
        $api->get('contacto/reportecomportamiento', 'ContactoController@getReporteComportamiento');
        $api->get('contacto/{id}', 'ContactoController@getById');
        $api->post('contacto/store', 'ContactoController@store');
        
        $api->post('contacto/recomendados', 'ContactoController@getContactosRecomendados');
        $api->post('contacto/{id}', 'ContactoController@update');
        $api->post('contacto/delete/{id}', 'ContactoController@delete');

        //Campañas
        $api->get('campania/', 'CampaniaController@index');
        $api->get('campania/recomendados', 'CampaniaController@getCampaniaRecomendacion');
        $api->get('campania/totales', 'CampaniaController@getTotales');
        $api->get('campania/seguimientos', 'CampaniaController@indexSeguimiento');
        $api->get('campania/totales', 'CampaniaController@getTotales');
        $api->get('campania/reporteseguimiento', 'CampaniaController@getReporteSeguimiento');
        $api->get('campania/ultimacampania', 'CampaniaController@getUltimaCampania');

        // $api->get('campania/get-image/', 'CampaniaController@getImage');
        $api->get('campania/{id}', 'CampaniaController@getById');
        $api->post('campania/sms', 'SmsController@sendSms');
        $api->post('campania/store', 'CampaniaController@store');
        $api->post('campania/add-image', 'CampaniaController@addImage');
        $api->post('campania/ejecutar/evento', 'CampaniaController@ejecutarEvento');
        $api->post('campania/ejecutar/{id}', 'CampaniaController@ejecutar');
        $api->post('campania/archivar/{id}', 'CampaniaController@archivar');
        $api->post('campania/activar/{id}', 'CampaniaController@activar');
        
        
        
        //Recibir mensaje sms
        
        $api->post('campania/ws', 'SmsController@sendWhatsApp');
        $api->post('campania/recibirSMS', 'SmsController@receiveSMS');
        $api->post('campania/pruebaevento', function(){
            event(new \App\Events\CampaniaCreadaEvent());
            dd('Mensaje público ejecutado exitosamente.');
        });
        $api->post('campania/{id}', 'CampaniaController@update');
        $api->post('campania/delete/{id}', 'CampaniaController@delete');

        //Objetivo
        $api->get('objetivo/', 'ObjetivoController@index');
        $api->get('objetivo/{id}', 'ObjetivoController@getById');
        $api->post('objetivo/store', 'ObjetivoController@store');
        $api->post('objetivo/{id}', 'ObjetivoController@update');
        $api->post('objetivo/delete/{id}', 'ObjetivoController@delete');
        //Canal
        $api->get('canal/', 'CanalController@index');
        $api->get('canal/canalesporcentaje', 'CanalController@getCanalesPorcentaje');
        $api->get('canal/{id}', 'CanalController@getById');
        $api->post('canal/store', 'CanalController@store');
        $api->post('canal/{id}', 'CanalController@update');
        $api->post('canal/delete/{id}', 'CanalController@delete');
        //Interés
        $api->get('interes/', 'InteresController@index');
        $api->get('interes/interesesporcentaje', 'InteresController@getInteresesPorcentaje');
        $api->get('interes/{id}', 'InteresController@getById');
        $api->post('interes/store', 'InteresController@store');
        $api->post('interes/{id}', 'InteresController@update');
        $api->post('interes/delete/{id}', 'InteresController@delete');

        //Registrp
        $api->get('registro/', 'RegistroController@index');
        //eventoCampania
        $api->get('eventoCampania/', 'EventoCampaniaController@index');
        $api->get('eventoCampania/{id}', 'EventoCampaniaController@getById');
        $api->post('eventoCampania/store', 'EventoCampaniaController@store');
        $api->post('eventoCampania/{id}', 'EventoCampaniaController@update');
        $api->post('eventoCampania/delete/{id}', 'EventoCampaniaController@delete');

    }); 
    
    // $api::post('update', 'UsuarioController@update');
});
