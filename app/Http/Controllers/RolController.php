<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Rol;

class RolController extends Controller {

    public function index() {
        try {
            //code...
            // $roles = Rol::whereIn('status',['A','I']);

            $roles = null;
            // Usuario::?:
            if(isset($_GET['status'])){
                // 
                $roles = Rol::where('status',$_GET['status']);
            }else{
                // echo json_encode($_GET['status']); die();
                $roles = Rol::whereIn('status',['A','I']);
            }
            if(isset($_GET['busqueda'])){
                $busqueda = $_GET['busqueda'];
                $roles->where(function ($query) use ($busqueda) {
                    $query->orWhere('nombre', 'like', '%' . $busqueda . '%');
                        //   ->orWhere('rol.nombre','like', '%' . $busqueda . '%');
                });     
            }
            $roles->orderBy('nombre');
            if(isset($_GET['per_page'])) {
                $roles = $roles->paginate($_GET['per_page']);
            }else {
                $roles = $roles->get();
            }
            

          
            return response()->json($roles, 200); 
        } catch (\Exception $e) {
            //throw $th;
        }
        
    }

    public function getById($id){
        try {
            //code...
            $rol = Rol::find($id);
            

          
            return response()->json($rol, 200); 
        } catch (\Exception $e) {
            //throw $th;
        }
    }

    public function delete($id){
        try {
            $rol = Rol::find($id);
            if($rol->status == 'A')
                $rol->status = 'I';
            else if($rol->status == 'I')
                $rol->status = 'A';

            $rol->update();

            return response()->json($rol, 200); 
        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    public function store(Request $request){
        try {
            $input = $request->all();
            $rol = Rol::create($input);
            $data = [
                'status'    => 'success',
                'code'      => 201,
                'message'   => 'Rol creado correctamente.',  
                'usuario'   => $rol,   
            ];

            return response()->json($rol, 200); 
        } catch (\Exception $e) {
            return response()->json(["message"=>$e->getMessage()]); 
        }
    }

    public function update(Request $request, $id){
        try {
            $input = $request->all();
            $rol = Rol::find($id);
            $rol->nombre = $input['nombre'];
            $rol->update();

            return response()->json($rol, 200); 
        } catch (\Exception $e) {
            return response()->json(["message"=>$e->getMessage()]); 
        }
    }
    
}
