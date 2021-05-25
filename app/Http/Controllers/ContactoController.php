<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Contacto;
use App\Models\RecomendacionBasadaItem;
use App\Models\Registro;
use Illuminate\Support\Facades\DB;
class ContactoController extends Controller {

    public function index() {
        try {
            //code...
            $contactos = null;
            if(isset($_GET['status'])){
                $contactos = Contacto::where('status',$_GET['status']);
            }else{
                $contactos = Contacto::whereIn('status',['A','I']);
            }
            
            if(isset($_GET['busqueda'])){
                $busqueda = $_GET['busqueda'];
                $contactos->where(function ($query) use ($busqueda) {
                    $query->orWhere('nombres', 'like', '%' . $busqueda . '%');
                    $query->orWhere('apellidos', 'like', '%' . $busqueda . '%');
                        //   ->orWhere('rol.nombre','like', '%' . $busqueda . '%');
                });
            }
            $contactos->orderBy('contacto.apellidos');
            if(isset($_GET['per_page'])) {
                $contactos = $contactos->paginate($_GET['per_page']);
            }else {
                $contactos = $contactos->get();
            }
            

          
            return response()->json($contactos, 200); 
        } catch (\Exception $e) {
            //throw $th;
        }
        
    }

    public function getReporteComportamiento(){
        try {
            DB::beginTransaction();
            // $totales = [];
            $comportamientoClientes = Contacto::where('contacto.status','A');
            // $comportamientoClientes->with(['contactointereses' => function ($q){
            //     // if(isset($_GET['intereses'])){
            //     //     $intereses = explode(",", $_GET['intereses']);
            //     //     $q->whereIn('contacto_interes.interes_id', $intereses);
            //     // }
            // }]);
            $comportamientoClientes->join('campania_contacto', function ($join) {
                $join->on('contacto.id', '=', 'campania_contacto.contacto_id');
                $join->where('campania_contacto.status','A');
                
            });
            // $comportamientoClientes->join('campania', function ($join) {
            //     $join->on('campania_contacto.campania_id', '=', 'campania.id');
                
                
            // });


            $comportamientoClientes->join('seguimiento_campania_detalle', function ($join) {
                $join->on('seguimiento_campania_detalle.campania_contacto_id', '=', 'campania_contacto.id');
                // $join->where('seguimiento_campania_detalle.status','A');
            });
            $comportamientoClientes->join('seguimiento_campania', function ($join) {
                $join->on('seguimiento_campania_detalle.seguimiento_campania_id', '=', 'seguimiento_campania.id');
                // $join->where('seguimiento_campania.status','A');
                if(isset($_GET['fecha_inicio']) && isset($_GET['fecha_fin'])){
                    $join->whereBetween('seguimiento_campania.fecha_inicio_seguimiento', [$_GET['fecha_inicio'], $_GET['fecha_fin']]);
                }
            });
            // $seguimientosCampanias->leftJoin('seguimiento_campania_detalle', function ($join) {
            //     $join->on('seguimiento_campania.id', '=', 'seguimiento_campania_detalle.seguimiento_campania_id');
            // });

            if(isset($_GET['busqueda'])){
                $busqueda = $_GET['busqueda'];
                $comportamientoClientes->where(function ($query) use ($busqueda) {
                    $query->orWhere('contacto.nombres', 'like', '%' . $busqueda . '%');
                    $query->orWhere('contacto.apellidos', 'like', '%' . $busqueda . '%');
                        //   ->orWhere('rol.nombre','like', '%' . $busqueda . '%');
                });
            }
            // echo json_encode($_GET['canales']);die();

            
            
            $comportamientoClientes->join('campania_canal', function ($join) {
                $join->on('seguimiento_campania_detalle.campania_canal_id', '=', 'campania_canal.id');
                // $join->where('campania_canal.status','A');
                if(isset($_GET['canales'])){
                    $canales = explode(",", $_GET['canales']);
                    $join->whereIn('campania_canal.canal_id', $canales);
                }

                // if(isset($_GET['canal_id'])){
                //     $join->where('campania_canal.canal_id',$_GET['canal_id']);
                // }
            });
            $comportamientoClientes = $comportamientoClientes->whereHas('contactointereses', function($q){
                if(isset($_GET['intereses'])){
                    $intereses = explode(",", $_GET['intereses']);
                    $q->whereIn('contacto_interes.interes_id', $intereses);
                    
                }
            });
            // $comportamientoClientes->join('contacto_interes', function ($join) {
            //     $join->on('contacto_interes.contacto_id', '=', 'contacto.id');
            //     $join->where('contacto.status','A');
            //     if(isset($_GET['intereses'])){
            //         $intereses = explode(",", $_GET['intereses']);
            //         $join->whereIn('contacto_interes.interes_id', $intereses);
            //     }
            // });
            
            // $contactos = Contacto::where('status','A')->get()->toArray();
            // $seguimientoCampania = \DB::table('seguimiento_campania')->select([
            //     DB::raw('SUM(mensajes_respondidos) as mensajes_respondidos'),
            //     DB::raw('SUM(mensajes_rebotados) as mensajes_rebotados'),
            //     DB::raw('SUM(mensajes_enviados) as mensajes_enviados'),   
            // ])->get();
            // $comportamientoClientes->
            
                $comportamientoClientes->groupBy('contacto.id');
                $comportamientoClientes->select([
                    // 'seguimiento_campania.id as id',
                    // 'campania.nombre',
                    // 'seguimiento_campania.fecha_inicio_seguimiento',
                    // 'seguimiento_campania.fecha_fin_seguimiento',
                    'contacto.nombres',
                    'contacto.apellidos',
                    'contacto.celular',
                    'contacto.correo',
                    'contacto.id',
                    // 'contacto_interes.interes_id as interes_id',
                    // DB::raw('SUM(contacto_interes.interes_id) as interesesId'),
                    // DB::raw('SUM(IFNULL(seguimiento_campania_detalle.is_respondido,0)) as mensajes_respondidos'),
                    DB::raw('SUM(IFNULL(seguimiento_campania_detalle.is_respondido,0)) as mensajes_respondidos'),
                    DB::raw('SUM(IFNULL(seguimiento_campania_detalle.is_leido,0)) as mensajes_leidos'),
                    DB::raw('SUM(IFNULL(seguimiento_campania_detalle.is_rebotado,0)) as mensajes_rebotados'),
                    DB::raw('SUM(IFNULL(seguimiento_campania_detalle.is_entregado,0)) as mensajes_entregados'),
                    DB::raw('SUM(IFNULL(seguimiento_campania_detalle.is_enviado,0)) as mensajes_enviados'), 
                    DB::raw('SUM(IFNULL(seguimiento_campania_detalle.is_interesado,0)) as usuarios_interesados'),     
                    // 'campania_interes.interes_id as interes',
                    // 'seguimiento_campania.id as seg',
                    // 'campania.tipo',
                    // 'campania_canal.canal_id as canal',
                    // 'seguimiento_campania.campania_id'
                ]);
                if(isset($_GET['per_page'])) {
                    $comportamientoClientes = $comportamientoClientes->paginate($_GET['per_page']);
                }else {
                    $comportamientoClientes = $comportamientoClientes->get();
                }
            // echo json_encode($seguimientoCampania); die();
            // $seguimientoCampania-
                // foreach ($comportamientoClientes as $key => $value) {
                //     # code...
                //     echo 
                // }
               
                // ( as $key => $value) {
                //     # code...
                //     foreach ($contactos as $key => $contacto) {
                //         # code...
                //         if($contacto['id'] == $value['id']){
                //             $value['contactointereses'] = $contacto['contactointereses'];
                //         }
                //     }
                // }
                $contactos = Contacto::where('status','A')->with(['contactointereses' => function ($q){
                    if(isset($_GET['intereses'])){
                        $intereses = explode(",", $_GET['intereses']);
                        $q->whereIn('contacto_interes.interes_id', $intereses);
                        
                    }
                    $q->with('interes');
                }])->whereHas('contactointereses', function($q){
                    if(isset($_GET['intereses'])){
                        $intereses = explode(",", $_GET['intereses']);
                        $q->whereIn('contacto_interes.interes_id', $intereses);
                        
                    }
                    $q->with('interes');
                })->get();

                for ($i=0; $i < sizeof($comportamientoClientes); $i++) { 
                    for ($j=0; $j < sizeof($contactos); $j++) { 
                        # code...
                        if($contactos[$j]['id'] == $comportamientoClientes[$i]['id']){
                            $comportamientoClientes[$i]['contactointereses'] = $contactos[$j]['contactointereses'];
                        }
                    }
                    # code...
                } 

               
            DB::commit();
            return response()->json($comportamientoClientes, 200); 
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(["message"=>$e->getMessage()]); 
        }
    }

    public function delete($id){
        try {
            $contacto = Contacto::find($id);
            $tipo = "";
            $nombre = "";
            if($contacto->status == 'A'){
                $tipo = "E";
                $contacto->status = 'I';
                $nombre = "Se ha eliminado un contacto";
            } 
            else if($contacto->status == 'I'){
                $tipo = "A";
                $contacto->status = 'A';
                $nombre = "Se ha activado un contacto";
            }
            $contacto->update();
            Registro::create([
                'tipo'  => $tipo,
                'nombre'   => $nombre,
                'menu_id'       => '10',
                'usuario_ingresa_id'    => $contacto->usuario_ingresa_id
            ]);
            return response()->json($contacto, 200); 
        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    public function store(Request $request){
        try {
            $input = $request->all();
            $contacto = Contacto::create($input);
            $data = [
                'status'    => 'success',
                'code'      => 201,
                'message'   => 'contacto creado correctamente.',  
                'usuario'   => $contacto,   
            ];
            Registro::create([
                'tipo'  => 'G',
                'nombre'   => 'Se ha guardado un contacto.',
                'menu_id'       => '10',
                'usuario_ingresa_id'    => $contacto->usuario_ingresa_id
            ]);
            return response()->json($contacto, 200); 
        } catch (\Exception $e) {
            return response()->json(["message"=>$e->getMessage()]); 
        }
    }

    public function update(Request $request, $id){
        try {
            $input = $request->all();
            $contacto = Contacto::find($id);
            // $contacto->nombres = $input['nombres'];
            // $contacto->apellidos = $input['apellidos'];
            // $contacto->apellidos = $input['apellidos'];
            $contacto->update($input);
            Registro::create([
                'tipo'  => 'M',
                'nombre'   => 'Se ha modificado un contacto.',
                'menu_id'       => '10',
                'usuario_ingresa_id'    => $contacto->usuario_ingresa_id
            ]);
            return response()->json($contacto, 200); 
        } catch (\Exception $e) {
            return response()->json(["message"=>$e->getMessage()]); 
        }
    }

    public function getContactosRecomendados(Request $request){
        try {
            $input = $request->all();
            // echo json_encode($input); die();
            $contactos = [
                'Luis Ernesto' => ['Tecnología', 'Internet de las cosas'],
                'Carlos Sánchez' => ['E-commerce', 'Tecnología', 'Fantasy'],
                'Mariuxi Cabrera' => ['Computadoras', 'Tecnología', 'Maquillaje'],
                'William Banchón' => ['E-commerce'],
                'Johanna Cabrera' => ['Smartphones', 'Computadoras'],
                'Ketty Borbor' => ['Smartphones', 'Tecnología'],
            ];
            // var_dump($contactos); die();
            $contactos = Contacto::where('status', 'A');
            // $contactos = $contactos->get()->load('contacto');
            $contactos->with(['contactointereses' => function ($q){
                $q->where('contacto_interes.status', 'A');
                $q->with('interes');
                // if(isset($_GET['usuario_id'])){
                //     $q->where('permiso.usuario_id',$_GET['usuario_id']);//estoy probando con un valor estático temporalmente
                // }else if(isset($_GET['rol_id'])){
                //     $q->where('permiso.rol_id',$_GET['rol_id']);//estoy probando con un valor estático temporalmente
                // }
            }]);
            $contactos = $contactos->get();
            // echo json_encode($contactos); die();
            $contactosInteres = [];
            foreach ($contactos as $k => $contacto) {
                    $result =  [];
                    // array_push($result[$contacto->nombres], $this->similarityDistance($this->data, self::USER_ID, $k);
                foreach ($contacto->contactointereses as $key => $contacto_interes) {
                    # code...
                    array_push($result, $contacto_interes->interes->nombre);
                }
                // array_push($contactosInteres[], $result);
                $contactosInteres[$contacto->nombres] = $result;
            }
            // var_dump($contactosInteres); die();
            $campania = ['Computadores'];
            
            $engine = new RecomendacionBasadaItem($input, $contactosInteres);
            // $contactosRecomendados = [];
            $contactosSimilitud = $engine->getRecomendacion();
            $contactosRecomendados = [];
            foreach ($contactosSimilitud as $key => $contacto) {
                # code...
                // echo json_encode($contacto->similitud); die();
                if($contacto->similitud > 0){
                    array_push($contactosRecomendados, $contacto);
                }
                // 
            }
            asort($contactosRecomendados);

            return response()->json($contactosRecomendados, 200); 
        } catch (\Exception $e) {
            return response()->json(["message"=>$e->getMessage()]); 
        }
            // echo json_encode($contactosRecomendados); die();
        // var_dump($engine->getRecomendacion());
    }
    
}
