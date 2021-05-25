<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Registro;
use Illuminate\Support\Facades\DB;
use App\Models\Campania;
use App\Models\SeguimientoCampania;
use App\Models\SeguimientoCampaniaDetalle;
use App\Models\EventoCampania;
use App\Models\Contacto;
use App\Models\Canal;
use App\Models\CampaniaCanal;
use App\Models\CampaniaContacto;
use App\Models\CampaniaInteres;
use App\Models\ContactoInteres;
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
            $canales->orderBy('canal.nombre');
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

    public function getCanalesPorcentaje(){
        try {
            DB::beginTransaction();
            $data = [
                'canales' => [],
                'total'     => 0,
            ];
            $seguimientosCampanias = SeguimientoCampania::leftJoin('seguimiento_campania_detalle', function ($join) {
                $join->on('seguimiento_campania.id', '=', 'seguimiento_campania_detalle.seguimiento_campania_id');
                
                
            });
            $seguimientosCampanias->join('campania_canal', function ($join) {
                $join->on('seguimiento_campania_detalle.campania_canal_id', '=', 'campania_canal.id');
            });
            $seguimientosCampanias->join('canal', function ($join) {
                $join->on('campania_canal.canal_id', '=', 'canal.id');
            });
            $seguimientosCampanias->groupBy('campania_canal.canal_id');
            $seguimientosCampanias =$seguimientosCampanias->select([
                // DB::raw('COUNT(seguimiento_campania.id) as seguimientos_totales'),
                DB::raw('COUNT(campania_canal.canal_id) as cantidad'),
                'canal.nombre'
                // 'seguimiento_campania.id as id',
                // 'campania.nombre',
                // 'seguimiento_campania.fecha_inicio_seguimiento',
                // 'seguimiento_campania.fecha_fin_seguimiento',
                // DB::raw('SUM(IFNULL(seguimiento_campania_detalle.is_respondido,0)) as mensajes_respondidos'),
                // DB::raw('SUM(IFNULL(seguimiento_campania_detalle.is_respondido,0)) as mensajes_respondidos'),
                // DB::raw('SUM(IFNULL(seguimiento_campania_detalle.is_leido,0)) as mensajes_leidos'),
                // DB::raw('SUM(IFNULL(seguimiento_campania_detalle.is_rebotado,0)) as mensajes_rebotados'),
                // DB::raw('SUM(IFNULL(seguimiento_campania_detalle.is_entregado,0)) as mensajes_entregados'),
                // DB::raw('SUM(IFNULL(seguimiento_campania_detalle.is_enviado,0)) as mensajes_enviados'), 
                // DB::raw('SUM(IFNULL(seguimiento_campania_detalle.is_interesado,0)) as usuarios_interesados'),     
                // // 'campania_interes.interes_id as interes',
                // // 'seguimiento_campania.id as seg',
                // 'campania.tipo',
                // // 'campania_canal.canal_id as canal',
                // 'seguimiento_campania.campania_id'
            ])->get();
                $total = 0;
            foreach ($seguimientosCampanias as $key => $value) {
                # code...
                $total += $value['cantidad'];
            }

            $data = [
                'canales' => $seguimientosCampanias,
                'total'     => $total,
            ];
            DB::commit();
            return response()->json($data, 200); 
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(["message"=>$e->getMessage()]); 
        }
    }

    public function delete($id){
        try {
            $canal = Canal::find($id);
            $tipo = "";
            $nombre = "";
            if($canal->status == 'A'){
                $tipo = "E";
                $canal->status = 'I';
                $nombre = "Se ha eliminado un canal.";
            } 
            else if($canal->status == 'I'){
                $tipo = "A";
                $canal->status = 'A';
                $nombre = "Se ha activado un canal.";
            }
               

            $canal->update();
            Registro::create([
                'tipo'  => $tipo,
                'nombre'   => $nombre,
                'menu_id'       => '20',
                'usuario_ingresa_id'    => $_GET['usuario_id']
            ]);
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
            Registro::create([
                'tipo'  => 'G',
                'nombre'   => 'Se ha guardado un canal.',
                'menu_id'       => '20',
                'usuario_ingresa_id'    => $canal->usuario_ingresa_id
            ]);
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
            Registro::create([
                'tipo'  => 'E',
                'nombre'   => 'Se ha modificado un canal.',
                'menu_id'       => '20',
                'usuario_ingresa_id'    => $input['usuario_ingresa_id']
            ]);
            return response()->json($canal, 200); 
        } catch (\Exception $e) {
            return response()->json(["message"=>$e->getMessage()]); 
        }
    }
    
}
