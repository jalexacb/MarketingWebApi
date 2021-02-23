<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\eventoCampania;

class EventoCampaniaController extends Controller {

    public function index() {
        try {
            //code...
            // $roles = Rol::whereIn('status',['A','I']);

            $eventoCampania = null;
            // Usuario::?:
            if(isset($_GET['status'])){
                // 
                $eventoCampania = EventoCampania::where('status',$_GET['status']);
            }else{
                // echo json_encode($_GET['status']); die();
                $eventoCampania = EventoCampania::whereIn('status',['A','I']);
            }
            if(isset($_GET['busqueda'])){
                $busqueda = $_GET['busqueda'];
                $eventoCampanias->where(function ($query) use ($busqueda) {
                    $query->orWhere('nombre', 'like', '%' . $busqueda . '%');
                        //   ->orWhere('rol.nombre','like', '%' . $busqueda . '%');
                });     
            }
            $eventoCampanias->orderBy('nombre');
            if(isset($_GET['per_page'])) {
                $eventoCampanias = $eventoCampanias->paginate($_GET['per_page']);
            }else {
                $eventoCampanias = $eventoCampanias->get();
            }
            

          
            return response()->json($eventoCampanias, 200); 
        } catch (\Exception $e) {
            //throw $th;
        }
        
    }

    public function getById($id){
        try {
            //code...
            $eventoCampania = EventoCampania::find($id);
            

          
            return response()->json($eventoCampania, 200); 
        } catch (\Exception $e) {
            //throw $th;
        }
    }

    public function delete($id){
        try {
            $eventoCampania = EventoCampania::find($id);
            if($eventoCampania->status == 'A')
                $eventoCampania->status = 'I';
            else if($eventoCampania->status == 'I')
                $eventoCampania->status = 'A';

            $eventoCampania->update();

            return response()->json($eventoCampania, 200); 
        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    public function store(Request $request){
        try {
            $input = $request->all();
            $eventoCampania = EventoCampania::create($input);
            $data = [
                'status'    => 'success',
                'code'      => 201,
                'message'   => 'eventoCampania creado correctamente.',  
                'usuario'   => $eventoCampania,   
            ];

            return response()->json($eventoCampania, 200); 
        } catch (\Exception $e) {
            return response()->json(["message"=>$e->getMessage()]); 
        }
    }

    public function update(Request $request, $id){
        try {
            $input = $request->all();
            $eventoCampania = EventoCampania::find($id);
            $eventoCampania->nombre = $input['nombre'];
            $eventoCampania->update();

            return response()->json($eventoCampania, 200); 
        } catch (\Exception $e) {
            return response()->json(["message"=>$e->getMessage()]); 
        }
    }
    
}
