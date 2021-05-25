<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Parametro;
use App\Models\Registro;
class ParametroController extends Controller {

    public function index() {
        try {
            // $roles = Rol::whereIn('status',['A','I']);

            $parametros = null;
            // Usuario::?:
            if(isset($_GET['status'])){
                // 
                $parametros = Parametro::where('status',$_GET['status']);
            }else{
                // echo json_encode($_GET['status']); die();
                $parametros = Parametro::whereIn('status',['A','I']);
            }
            if(isset($_GET['busqueda'])){
                $busqueda = $_GET['busqueda'];
                $parametros->where(function ($query) use ($busqueda) {
                    $query->orWhere('nombre', 'like', '%' . $busqueda . '%');
                        //   ->orWhere('rol.nombre','like', '%' . $busqueda . '%');
                });     
            }
            $parametros->orderBy('nombre');
            if(isset($_GET['per_page'])) {
                $parametros = $parametros->paginate($_GET['per_page']);
            }else {
                $parametros = $parametros->get();
            }
            

          
            return response()->json($parametros, 200); 
        } catch (\Exception $e) {
            //throw $th;
        }
        
    }

    public function getParametrosSeguridad(){
        try {
            // $roles = Rol::whereIn('status',['A','I']);

            $parametros = Parametro::whereIn('id',[1,2])->where('status',"A")->get();

          
            return response()->json($parametros, 200); 
        } catch (\Exception $e) {
            //throw $th;
        }
    }

   
    public function delete($id){
        try {
            $parametro = Parametro::find($id);
            $tipo = "";
            if($parametro->status == 'A'){
                $parametro->status = 'I';
                $tipo = "E";
                $nombre = "Se ha eliminado un parametro.";
            }else if($parametro->status == 'I'){
                $parametro->status = 'A';
                $nombre = "Se ha activado un parametro.";
                $tipo = "A";
            }
            $parametro->update();
            Registro::create([
                'tipo'  => $tipo,
                'nombre'   => $nombre,
                'menu_id'       => '1',
                'usuario_ingresa_id'    => $_GET['usuario_id']
            ]);
            return response()->json($parametro, 200); 
        } catch (\Exception $e) {
            return response()->json(["message"=>$e->getMessage()]); 
        }
    }

    // public function update(Request $request){
    //     try {
    //         //code...
    //     } catch (\Exception $e) {
    //         //throw $th;
    //     }
    // }

    public function store(Request $request){
        try {
            $input = $request->all();
            $parametro = Parametro::create($input);
            $data = [
                'status'    => 'success',
                'code'      => 201,
                'message'   => 'parametro creado correctamente.',  
                'usuario'   => $parametro,   
            ];
            Registro::create([
                'tipo'  => 'G',
                'nombre'   => 'Se ha guardado un parámetro.',
                'menu_id'       => '20',
                'usuario_ingresa_id'    => $parametro->usuario_ingresa_id
            ]);
            return response()->json($parametro, 200); 
        } catch (\Exception $e) {
            return response()->json(["message"=>$e->getMessage()]); 
        }
    }

    public function update(Request $request){
        try {
            $input= $request->all(); 

           
            $parametro = Parametro::find($input['id']);
            $parametro->valor = $input['valor'];
            $parametro->update();
    
            $data = [
                'status'    => 'success',
                'code'      => 201,
                'message'   => 'Permiso creado correctamente.',  
                // 'usuario'   => $permiso,   
            ];
            Registro::create([
                'tipo'  => 'M',
                'nombre'   => 'Se ha modificado un parámetro.',
                'menu_id'       => '20',
                'usuario_ingresa_id'    => $input['usuario_ingresa_id'],
            ]);
            return response()->json($data, 200); 
        } catch (\Exception $e) {
            //throw $th;
            return response()->json(["message"=>$e->getMessage()]); 
        }
    }

    public function updateSeguridad(Request $request){
        try {
            $input= $request->all(); 

            foreach ($input as $key => $value) {
                # code...
                $parametro = Parametro::find($value['id']);
                $parametro->valor = $value['valor'];
                $parametro->update();
            }
            // echo json_encode($input); die();
            // $rol = Rol::create($input);
            $data = [
                'status'    => 'success',
                'code'      => 201,
                'message'   => 'Permiso creado correctamente.',  
                // 'usuario'   => $permiso,   
            ];
            Registro::create([
                'tipo'  => 'M',
                'nombre'   => 'Se han modificado parámetros.',
                'menu_id'       => '1',
                'usuario_ingresa_id'    => $permiso->usuario_ingresa_id
            ]);
            return response()->json($data, 200); 
        } catch (\Exception $e) {
            //throw $th;
            return response()->json(["message"=>$e->getMessage()]); 
        }
    }
}
