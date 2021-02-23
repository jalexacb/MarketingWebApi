<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Parametro;

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

   
    public function delete($id){
        try {
            $parametro = Parametro::find($id);
            $parametro->status = 'I';
            $parametro->update();

            return response()->json($parametro, 200); 
        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    // public function update(Request $request){
    //     try {
    //         //code...
    //     } catch (\Throwable $th) {
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

            return response()->json($data, 200); 
        } catch (\Exception $e) {
            //throw $th;
            return response()->json(["message"=>$e->getMessage()]); 
        }
    }
}
