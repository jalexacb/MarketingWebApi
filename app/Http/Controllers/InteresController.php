<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Interes;
use App\Models\Registro;
use App\Models\ContactoInteres;

use Illuminate\Support\Facades\DB;
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

    public function getInteresesPorcentaje(){ //Porcentaje de intereses x usuarios
        try {
            DB::beginTransaction();
            $data = [
                'intereses' => [],
                'total'     => 0,
            ];
            $contactosIntereses = ContactoInteres::where('contacto_interes.status',"A")->join('interes', function ($join) {
                $join->on('contacto_interes.interes_id', '=', 'interes.id');
                $join->where('interes.status',"A");
            });
            $contactosIntereses->groupBy('contacto_interes.interes_id');
            $contactosIntereses =$contactosIntereses->select([
                // DB::raw('COUNT(seguimiento_campania.id) as seguimientos_totales'),
                DB::raw('COUNT(contacto_interes.interes_id) as cantidad'),
                'interes.nombre'
              
            ])->take(5)                           // Take the first 5
            ->get();
                $total = 0;
            foreach ($contactosIntereses as $key => $value) {
                # code...
                $total += $value['cantidad'];
            }

            $data = [
                'intereses' => $contactosIntereses,
                'total'     => $total,
            ];
            DB::commit();
            return response()->json($data, 200); 
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(["message"=>$e->getMessage()]); 
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
            $tipo = "";
            $nombre = "";
            if($interes->status == 'A'){
                $interes->status = 'I';
                $tipo = "E";
                $nombre = "Se ha eliminado un interés.";
            }else if($interes->status == 'I'){
                $interes->status = 'A';
                $tipo = "A";
                $nombre = "Se ha activado un interés.";
            }
                

            $interes->update();
            Registro::create([
                'tipo'  => $tipo,
                'nombre'   => $nombre,
                'menu_id'       => '20',
                'usuario_ingresa_id'    => $interes->usuario_ingresa_id
            ]);
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
            Registro::create([
                'tipo'  => 'G',
                'nombre'   => 'Se ha guardado un interés.',
                'menu_id'       => '20',
                'usuario_ingresa_id'    => $interes->usuario_ingresa_id
            ]);
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
            Registro::create([
                'tipo'  => 'M',
                'nombre'   => 'Se ha modificado un interes.',
                'menu_id'       => '20',
                'usuario_ingresa_id'    => $interes->usuario_ingresa_id
            ]);
            return response()->json($interes, 200); 
        } catch (\Exception $e) {
            return response()->json(["message"=>$e->getMessage()]); 
        }
    }
    
}
