<?php

namespace App\Http\Controllers;

use Firebase\JWT\JWT;
use Illuminate\Support\Facades\DB;
use App\Models\Usuario;
use App\Models\Parametro;
use App\Models\ConfigUsuario;

class AuthController {
    public $key;

    public function __construct() {
        $this->key = 'esto_es_una_clave';
    }
    public function signin($credentials, $getToken = null){
        //Buscar si existe usuario con credencial
        $data = $this->comprobarIntentosLogin($credentials);
        // echo json_encode($data); die();
        $usuario = Usuario::where([
            'usuario'   => $credentials['usuario'],
            'password'  => $credentials['password'],
            'status'    => 'A'
        ])->first();
        // return "hOLA";
        //Comprobar si devuelve objeto

        $signup = false;

        if(is_object($usuario)){
            $signup = true;
        }

        if($data == null && $signup) {
            $configUsuario = ConfigUsuario::where('status','A')->where('usuario_id',$usuario->id);
            // $configUsuario->intento_login = 1;
            // $configUsuario->fecha_bloqueado = null;
            $configUsuario->update([
                'intento_login'     => 1,
                'fecha_bloqueado'   => null
            ]);
            //Generar token
            // if($signup) {
            $token = [
                'sub'       => $usuario->id,
                'usuario'   => $usuario->usuario,
                'nombres'   => $usuario->nombres,
                'apellidos' => $usuario->apellidos,
                'rol_id'       => $usuario->rol_id,
                'iat'       => time(),
                'exp'       => time() + 54681451515757578,
            ];

            $jwt = JWT::encode($token,$this->key, 'HS256');
            $decoded = JWT::decode($jwt, $this->key, ['HS256']);
            if (is_null($getToken)){
                $data =  $jwt;
            }else {
                $data = $decoded;
            }

            
        }   
        // }else if(!$data){
        //     $data = [
        //         'status' => 'error',
        //         'message'   => 'Login incorrecto',
        //     ];
        // }
        // echo json_encode($data); die();
        //Devolver datos decodificados o el token en función del parámetro
        return $data;
    }

    private function comprobarIntentosLogin($credentials) {
        // $credentials['password'] =  hash('sha256', $credentials['password']);

        $usuario = Usuario::where('status','A')->where('usuario',$credentials['usuario'])->first();

        $usuarioCorrecto = Usuario::where('status','A')->where('id',$usuario->id)->where('password', $credentials['password'])->first();
        $parametroIntentos = Parametro::where('status', 'A')
                                ->where('nombre', 'max_intentos_login')->first();
        $parametroTiempo = Parametro::where('status', 'A')
                                ->where('nombre', 'tiempo_espera_login')->first();        
        $configUsuario = ConfigUsuario::where('status','A')
        ->where('usuario_id', $usuario->id)->first();   
        
        
                                              
        if(is_null($usuarioCorrecto)){
            
            
                                      
            if($configUsuario->intento_login < $parametroIntentos->valor) {
                $configUsuarioActualizado = ConfigUsuario::find($configUsuario->id);
                  
                
                
                
                $data = [
                    'status' => 'error',
                    'code'   => 400,
                    'info'   => ['intento_login' => $configUsuarioActualizado->intento_login,
                                 'max_intentos_login' => $parametroIntentos->valor,
                                 'tiempo_espera_login' => $parametroTiempo->valor,
                                ],
                    'message' => 'Advertencia',
                ];
                $configUsuarioActualizado->intento_login = $configUsuario->intento_login + 1;
                $configUsuarioActualizado->update();
                
                return $data;
            } else if ( $configUsuario->intento_login === $parametroIntentos->valor){
                $configUsuarioActualizado = ConfigUsuario::find($configUsuario->id);
                if($configUsuario->fecha_bloqueado == NULL ){
                    
                    $hora = new \DateTime("now", new \DateTimeZone('America/Guayaquil'));
                    // echo $hora->format('G');
                    $hora->modify('+'.$parametroTiempo->valor.' minute');
                    $configUsuarioActualizado->fecha_bloqueado = $hora->format('Y-m-j H:i:s');
                // echo json_encode($configUsuarioActualizado->fecha_bloqueado); die();
                    $configUsuarioActualizado->update();
                }
                
                $data = [
                    'status' => 'error',
                    'code'  => 400,
                    'info'  => ['intento_login' => $configUsuario->intento_login,
                                    'max_intentos_login'    => $parametroIntentos->valor,
                                    'tiempo_espera_login'   => $parametroTiempo->valor,
                                    'fecha_bloqueado'       => $configUsuarioActualizado->fecha_bloqueado,
                                    'config_usuario_bloqueado_id'  => $configUsuarioActualizado->id,
                                    ''
                                ],
                    'message' => 'Bloqueado'
                ];
    
                return $data;
            }                        
        }else if(isset($usuarioCorrecto) && $configUsuario->intento_login === $parametroIntentos->valor ){
            $data = [
                'status' => 'error',
                'code'  => 400,
                'info'  => ['intento_login' => $configUsuario->intento_login,
                                'max_intentos_login' => $parametroIntentos->valor,
                                'tiempo_espera_login' => $parametroTiempo->valor,
                            ],
                'message' => 'Bloqueado'
            ];
            
            return $data;
        }
        

        return null;
        
    }

    public function checkToken($jwt, $getIdentity = false){
        $auth = false;
        try {
            // echo json_encode($jwt); die();
            $decoded = JWT::decode($jwt, $this->key, ['HS256']);
            // echo json_encode($decoded); die();
            if(!empty($decoded) && is_object($decoded) && isset($decoded->sub)){
            $auth = true;

            }else {
                $auth = false;
            }

            if($getIdentity){
                return $decoded;
            }

            return $decoded;
        } catch (\UnexpectedValueException $e) {
            $auth = false;
        } catch (\DomainException $e) {
            $auth = false;
        }
        
        

        
    }
   


}