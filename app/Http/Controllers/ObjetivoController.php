<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Objetivo;
use App\Models\Registro;
class ObjetivoController extends Controller {

    public function index() {
        try {
            //code...
            // $roles = Rol::whereIn('status',['A','I']);

            $objetivos = null;
            // Usuario::?:
            if(isset($_GET['status'])){
                // 
                $objetivos = Objetivo::where('status',$_GET['status']);
            }else{
                // echo json_encode($_GET['status']); die();
                $objetivos = Objetivo::whereIn('status',['A','I']);
            }
            if(isset($_GET['busqueda'])){
                $busqueda = $_GET['busqueda'];
                $objetivos->where(function ($query) use ($busqueda) {
                    $query->orWhere('nombre', 'like', '%' . $busqueda . '%');
                        //   ->orWhere('rol.nombre','like', '%' . $busqueda . '%');
                });     
            }
            $objetivos->orderBy('objetivo.nombre');

            if(isset($_GET['per_page'])) {
                $objetivos = $objetivos->paginate($_GET['per_page']);
            }else {
                $objetivos = $objetivos->get();
            }
            

          
            return response()->json($objetivos, 200); 
        } catch (\Exception $e) {
            //throw $th;
        }
        
    }

    public function getById($id){
        try {
            //code...
            $objetivo = Objetivo::find($id);
            

          
            return response()->json($objetivo, 200); 
        } catch (\Exception $e) {
            //throw $th;
        }
    }

    public function delete($id){
        try {
            $objetivo = Objetivo::find($id);
            $tipo = "";
            $nombre = "";
            if($objetivo->status == 'A'){
                $objetivo->status = 'I';
                $tipo = "E";
                $nombre = "Se ha eliminado un objetivo.";
            }
                
            else if($objetivo->status == 'I'){
                $tipo = "A";
                $objetivo->status = 'A';
                $nombre = "Se ha activado un objetivo.";
            }
               

            $objetivo->update();
            Registro::create([
                'tipo'  => $tipo,
                'nombre'   => $nombre,
                'menu_id'       => '20',
                'usuario_ingresa_id'    => $objetivo->usuario_ingresa_id
            ]);
            return response()->json($objetivo, 200); 
        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    public function store(Request $request){
        try {
            $input = $request->all();
            $objetivo = Objetivo::create($input);
            $data = [
                'status'    => 'success',
                'code'      => 201,
                'message'   => 'Objetivo creado correctamente.',  
                'usuario'   => $Objetivo,   
            ];
            Registro::create([
                'tipo'  => 'G',
                'nombre'   => 'Se ha guardado un objetivo.',
                'menu_id'       => '20',
                'usuario_ingresa_id'    => $objetivo->usuario_ingresa_id
            ]);
            return response()->json($objetivo, 200); 
        } catch (\Exception $e) {
            return response()->json(["message"=>$e->getMessage()]); 
        }
    }

    public function update(Request $request, $id){
        try {
            $input = $request->all();
            $objetivo = Objetivo::find($id);
            $objetivo->nombre = $input['nombre'];
            $objetivo->update();
            Registro::create([
                'tipo'  => 'M',
                'nombre'   => 'Se ha modificado un objetivo.',
                'menu_id'       => '20',
                'usuario_ingresa_id'    => $objetivo->usuario_ingresa_id
            ]);
            return response()->json($objetivo, 200); 
        } catch (\Exception $e) {
            return response()->json(["message"=>$e->getMessage()]); 
        }
    }
    
}
