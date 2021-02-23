<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Interes;

class InteresController extends Controller {

    public function index() {
        try {
            //code...
            // $roles = Rol::whereIn('status',['A','I']);

            $interes = null;
            // Usuario::?:
            if(isset($_GET['status'])){
                // 
                $interes = Interes::where('status',$_GET['status']);
            }else{
                // echo json_encode($_GET['status']); die();
                $interes = Interes::whereIn('status',['A','I']);
            }
            if(isset($_GET['busqueda'])){
                $busqueda = $_GET['busqueda'];
                $interes->where(function ($query) use ($busqueda) {
                    $query->orWhere('nombre', 'like', '%' . $busqueda . '%');
                        //   ->orWhere('rol.nombre','like', '%' . $busqueda . '%');
                });     
            }
            $interes->orderBy('nombre');
            if(isset($_GET['per_page'])) {
                $interes = $interes->paginate($_GET['per_page']);
            }else {
                $interes = $interes->get();
            }
            

          
            return response()->json($interes, 200); 
        } catch (\Exception $e) {
            //throw $th;
        }
        
    }

    public function getById($id){
        try {
            //code...
            $interes = Interes::find($id);
            

          
            return response()->json($interes, 200); 
        } catch (\Exception $e) {
            //throw $th;
        }
    }

    public function delete($id){
        try {
            $interes = Interes::find($id);
            if($interes->status == 'A')
                $interes->status = 'I';
            else if($interes->status == 'I')
                $interes->status = 'A';

            $interes->update();

            return response()->json($interes, 200); 
        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    public function store(Request $request){
        try {
            $input = $request->all();
            $interes = Interes::create($input);
            $data = [
                'status'    => 'success',
                'code'      => 201,
                'message'   => 'interes creado correctamente.',  
                'usuario'   => $interes,   
            ];

            return response()->json($interes, 200); 
        } catch (\Exception $e) {
            return response()->json(["message"=>$e->getMessage()]); 
        }
    }

    public function update(Request $request, $id){
        try {
            $input = $request->all();
            $interes = Interes::find($id);
            $interes->nombre = $input['nombre'];
            $interes->update();

            return response()->json($interes, 200); 
        } catch (\Exception $e) {
            return response()->json(["message"=>$e->getMessage()]); 
        }
    }
    
}
