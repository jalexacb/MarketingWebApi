<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Canal;

class CanalController extends Controller {

    public function index() {
        try {
            //code...
            // $roles = Rol::whereIn('status',['A','I']);

            $canales = null;
            // Usuario::?:
            if(isset($_GET['status'])){
                // 
                $canales = Canal::where('status',$_GET['status']);
            }else{
                // echo json_encode($_GET['status']); die();
                $canales = Canal::whereIn('status',['A','I']);
            }
            if(isset($_GET['busqueda'])){
                $busqueda = $_GET['busqueda'];
                $canales->where(function ($query) use ($busqueda) {
                    $query->orWhere('nombre', 'like', '%' . $busqueda . '%');
                        //   ->orWhere('rol.nombre','like', '%' . $busqueda . '%');
                });     
            }
            $canales->orderBy('nombre');
            if(isset($_GET['per_page'])) {
                $canales = $canales->paginate($_GET['per_page']);
            }else {
                $canales = $canales->get();
            }
            

          
            return response()->json($canales, 200); 
        } catch (\Exception $e) {
            //throw $th;
        }
        
    }

    public function getById($id){
        try {
            //code...
            $canal = Canal::find($id);
            

          
            return response()->json($canal, 200); 
        } catch (\Exception $e) {
            //throw $th;
        }
    }

    public function delete($id){
        try {
            $canal = Canal::find($id);
            if($canal->status == 'A')
                $canal->status = 'I';
            else if($canal->status == 'I')
                $canal->status = 'A';

            $canal->update();

            return response()->json($canal, 200); 
        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    public function store(Request $request){
        try {
            $input = $request->all();
            $canal = Canal::create($input);
            $data = [
                'status'    => 'success',
                'code'      => 201,
                'message'   => 'Canal creado correctamente.',  
                'usuario'   => $canal,   
            ];

            return response()->json($canal, 200); 
        } catch (\Exception $e) {
            return response()->json(["message"=>$e->getMessage()]); 
        }
    }

    public function update(Request $request, $id){
        try {
            $input = $request->all();
            $canal = Canal::find($id);
            $canal->nombre = $input['nombre'];
            $canal->update();

            return response()->json($canal, 200); 
        } catch (\Exception $e) {
            return response()->json(["message"=>$e->getMessage()]); 
        }
    }
    
}
