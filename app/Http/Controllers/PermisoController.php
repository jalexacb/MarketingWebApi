<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Permiso;

class PermisoController extends Controller {

    public function index() {
        try {
            //code...
            $permisos = Permiso::where('status','A');
            if(isset($_GET['usuario_id']))
                $permisos->where('usuario_id', $_GET['usuario_id']);
            if(isset($_GET['rol_id']))
                $permisos->where('rol_id', $_GET['rol_id']);
            $permisos = $permisos->get();
            return response()->json($permisos, 200); 
        } catch (\Exception $e) {
            //throw $th;
        }
        
    }

    public function getPermisoByFilter(){
        try {

            
            $permiso = Permiso::where('status','A');
            $permiso->where(function ($join){
                if(isset($_GET['usuario_id'])){
                    
                    $join->orWhere('usuario_id',$_GET['usuario_id']);
                }else{
                    if(isset($_GET['rol_id'])){
                        $join->orWhere('rol_id',$_GET['rol_id']);
                    }
                }
               
            });

            
            // if(isset($_GET['usuario_id'])){
            //     $permiso->where('usuario_id',$_GET['usuario_id']);
            // }
            // if(isset($_GET['rol_id'])){
            //     $permiso->where('rol_id',$_GET['rol_id']);
            // }
            
            $permiso = $permiso->where('menu_id', $_GET['menu_id'])->first();
            // echo json_encode($_GET['usuario_id']); die();
            return response()->json($permiso, 200); 
        } catch (\Exception $e) {
            //throw $th;
        }
        


    }

   
    public function delete($id){
        try {
            $rol = Rol::find($id);
            $rol->status = 'I';
            $rol->update();

            return response()->json($rol, 200); 
        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    public function store(Request $request){
        try {
            $input = $request->all();

            $permiso = Permiso::orWhere('usuario_id',$input[0]['usuario_id'])->orWhere('rol_id', $input[0]['usuario_id'])->delete();

            foreach ($input as $key => $value) {
                # code...

                unset($value['menu']);
                unset($value['rol']);
                unset($value['usuario']);

                
                $permiso = Permiso::create([
                    'rol_id' => isset($value['rol_id'])?$value['rol_id']:null,
                    'usuario_id' => isset($value['usuario_id'])?$value['usuario_id']:null,
                    'menu_id'   => $value['menu_id'],
                    'usuario_ingresa_id' => $value['usuario_ingresa_id'],
                    'ver' => isset($value['ver'])?$value['ver']:null,
                    'crear' => isset($value['crear'])?$value['crear']:null,
                    'editar' => isset($value['editar'])?$value['editar']:null,
                    'eliminar' => isset($value['eliminar'])?$value['eliminar']:null,
                    
                ]);
                // echo json_encode($value); die();
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
