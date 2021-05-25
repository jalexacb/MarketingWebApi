<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Usuario;
use App\Models\ConfigUsuario;

use App\Models\Registro;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
class UsuarioController extends Controller {

    public function index() {
        try {
            //code...
            $usuarios = null;
            // Usuario::?:
            if(isset($_GET['status'])){
                // 
                $usuarios = Usuario::where('status',$_GET['status']);
            }else{
                // echo json_encode($_GET['status']); die();
                $usuarios = Usuario::whereIn('status',['A','I']);
            }
            
            if(isset($_GET['busqueda'])){
                $busqueda = $_GET['busqueda'];
                $usuarios->where(function ($query) use ($busqueda) {
                    $query->orWhere('usuario', 'like', '%' . $busqueda . '%')
                          ->orWhere('nombres', 'like', '%' . $busqueda . '%')
                          ->orWhere('apellidos','like', '%' . $busqueda . '%')
                          ->orWhere('apellidos','like', '%' . $busqueda . '%');
                        //   ->orWhere('rol.nombre','like', '%' . $busqueda . '%');
                });
            }
            $usuarios->orderBy('usuario.usuario');
            if(isset($_GET['per_page'])) {
                $usuarios = $usuarios->with('rol')->paginate($_GET['per_page']);
            }else {
                $usuarios = $usuarios->get()->load('rol');
            }
            

          
            return response()->json($usuarios, 200); 
        } catch (\Exception $e) {
            //throw $th;
        }
        
    }

    public function getById($id){
        try {
            //code...
            $usuario = Usuario::find($id);
            

          
            return response()->json($usuario, 200); 
        } catch (\Exception $e) {
            //throw $th;
        }
    }
    
    public function comprobarUsuarioExistente($usuario){
        try {
           
            $usuario = Usuario::where('status',"A")->where('usuario',$usuario)->first();
            $data = [];
            if($usuario){
                $data = [
                    "is_permitido"  => false,
                    "message"   => "Ya existe el usuario",
                    "usuario"   => $usuario,
                ];
                
            }else{
                $data = [
                    "message"   => "No existe el usuario",
                    "is_permitido"  => true,
                ];
            }
            return response()->json($data, 200); 
        } catch (\Exception $e) {
            return response()->json(["message"=>$e->getMessage()]); 
        }

    }

    public function addImage(Request $request){
        $input = $request->all();
        $image = $_FILES['image'];
        if($image){
            $name = $_FILES['image']['name'];
            $guardado = $_FILES['image']['tmp_name'];
            // $destinationPath = base_path() . '/images/campanias/' . $name.'.jpg';
            Storage::disk('usuarios')->put($name,  file_get_contents($guardado));
            // move_uploaded_file($_FILES['image']['tmp_name'], SITE_ROOT.'/images/campanias/'.$name);
        }
        return response()->json($image,200);
    }

    public function getImage(){
        // $_GET['imagen'] = 'aplicacionesweb1.jpg';

        $file = Storage::disk('usuarios')->get($_GET['imagen']);
        return response($file, 200)->header('Content-Type', 'image/jpeg');
    }
    // public function addImage(Request $request){
    //     $input = $request->all();
    //     $image = $_FILES['image'];
    //     if($image){
    //         $name = $_FILES['image']['name'];
    //         $guardado = $_FILES['image']['tmp_name'];
    //         $destinationPath = base_path() . '/images/usuarios/' . $name;
    //         move_uploaded_file($_FILES['image']['tmp_name'], SITE_ROOT.'/images/usuarios/'.$name);
    //     }
    //     return response()->json($image,200);
    // }


    

    // public function getImage(){

    //     $path = SITE_ROOT.'/images/usuarios/'.$_GET['imagen'];
    //     $image = file_get_contents($path );
    //     header('content-type: image/gif');
    //     if($image)
    //         echo $image;
    //     else echo '';   
    // }

   

    public function login(Request $request) {
        $credentials = $request->all();
        $jwtAuth = new \JwtAuth();



        $validate = \Validator::make($credentials, [
            'usuario'       => 'required',
            'password'      => 'required',
        ]);



        if($validate->fails()){
            $signup = [
                'status'    => 'error',
                'errors'    => $validate->errors(),
                'code'      => 404,
                'message'   => 'El usuario no se ha podido identificar.'     
            ];
            // return response()->json($data['status'], $data['code']);
        }else { 
        // $pwd =
            $credentials['password'] =  hash('sha256', $credentials['password']);
            $signup = $jwtAuth->signin($credentials);
            if(!empty($credentials['getToken'])){
                $signup = $jwtAuth->signin($credentials, true);
            }
            
            return response()->json($signup, 200);
        // echo $jwtAuth->signin();
        }
    }

    public function register(Request $request) {
        //Recoger datos de usuario por post
        $input = $request->all();
        if(!empty($input)){
             //limpiar datos
            // $input = array_map('trim', $input);
            //Validar datos
            
            $validate = \Validator::make($input, [
                'usuario'       => 'required|unique:usuario',
                'password'      => 'required',
                'nombres'       => 'required',
                'apellidos'     => 'required',
                'sexo'          => 'required',
            ]);

            if($validate->fails()){
                $data = [
                    'status'    => 'error',
                    'errors'    => $validate->errors(),
                    'code'      => 400,
                    'message'   => 'Faltan campos.'     
                ];
                // return response()->json($data['status'], $data['code']);
            }else {
                //Cifrar contraseña
                $input['password']= hash('sha256', $input['password']);
                // $input['password']
                //Crear usuario
                $usuario = Usuario::create($input);

                $configUsuario = ConfigUsuario::create([
                    'intento_login' => 0,
                    'fecha_bloqueado' => null,
                    'usuario_id'    => $usuario->id,
                    'usuario_ingresa_id' => 1,
                ]);
                $data = [
                    'status'    => 'success',
                    'code'      => 201,
                    'message'   => 'Usuario creado correctamente.',  
                    'usuario'   => $usuario,   
                ];
            }
        }else {
            $data = [
                'status'    => 'error',
                'code'      => 400,
                'message'   => 'Datos vacios.'     
            ];
        }

        Registro::create([
            'tipo'  => 'G',
            'nombre'   => 'Se ha guardado un usuario.',
            'menu_id'       => '1',
            'usuario_ingresa_id'    => $usuario->usuario_ingresa_id
        ]);
        return response()->json($data, $data['code']);
    }

    public function desbloqueoUsuario($id){
        // $usuario = Usuario::where('status','A')->where('id', $id)->first();
        $configUsuario = ConfigUsuario::find($id);
        $configUsuario->intento_login = 0;
        $configUsuario->fecha_bloqueado = null;
        $configUsuario->update();
        return response()->json($configUsuario, 200);
    }

    

    public function update(Request $request, $id){

        //Determinar si el usuario está identificado
        $input = $request->all();
        $token = $request->header('Authorization');
        // echo json_encode($token); die();
        $jwtAuth = new \JwtAuth();
        $checkToken = $jwtAuth->checkToken($token);
        
        // echo json_encode($input); die();
        if($checkToken && !empty($input)){
            //recoger datos por post
            

            //sacar usuario identificado

            $usuario = $jwtAuth->checkToken($token, true);

            //validar datos
            $validate = \Validator::make($input, [
                'usuario'       => 'required|unique:usuario,'.$usuario->sub,
                'password'      => 'required',
                // 'nombres'       => 'required',
                // 'apellidos'     => 'required',
                // 'sexo'          => 'required',
            ]);

            // quitar los campos
            unset($input['id']); 
            unset($input['rol_id']);  
            

            if(isset($input['password']) && $input['password'] != ''){
                $input['password']= hash('sha256', $input['password']);
            }else{
                unset($input['password']);
            }
            

            //actualiza en bd
            $usuario_actualizado = Usuario::find($id);
            $input['usuario_ingresa_id'] = $usuario->sub;
            
            $usuario_actualizado->update($input);  

            $data = [
                'code'  => 200,
                'status'    => 'success',
                'usuario'   => $usuario,
                'changes'   => $usuario_actualizado,
            ];

            //devolver array
        }else {
            $data = [
                'code'  => 400,
                'status'    => 'error',
                'message'   => 'Error.'
            ];
        }

        Registro::create([
            'tipo'  => 'M',
            'nombre'   => 'Se ha modificado un usuario.',
            'menu_id'       => '1',
            'usuario_ingresa_id'    => $usuario_actualizado->usuario_ingresa_id
        ]);
        return response()->json($data, $data['code']);
    }

    public function delete($id){
        try {
            $usuario = Usuario::find($id);
            $tipo = "";
            $nombre = "";
            if($usuario->status == 'A'){
                $usuario->status = 'I';
                $tipo = "E";
                $nombre = "Se ha eliminado un usuario.";
            }else if($usuario->status == 'I'){
                $usuario->status = 'A';
                $tipo = "A";
                $nombre = "Se ha activado un usuario.";
            }
            
                
            
            $usuario->update();
            Registro::create([
                'tipo'  => $tipo,
                'nombre'   => $nombre,
                'menu_id'       => '1',
                'usuario_ingresa_id'    => $usuario->usuario_ingresa_id
            ]);
            return response()->json($usuario, 200); 
        } catch (\Throwable $th) {
            //throw $th;
        }
    }

   
}
