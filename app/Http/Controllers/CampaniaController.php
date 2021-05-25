<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Campania;
use App\Models\SeguimientoCampania;
use App\Models\SeguimientoCampaniaDetalle;
use App\Models\EventoCampania;
use App\Models\Contacto;
use App\Models\Canal;
use App\Models\CampaniaCanal;
use App\Models\CampaniaContacto;
use App\Models\CampaniaInteres;
use App\Models\CampaniaObjetivo;
use App\Models\ContactoInteres;
use App\Models\Registro;
use App\Models\Parametro;
use App\Jobs\EjecutarCampaniaJob;
use Phpml\Regression\LeastSquares;

use Twilio\Rest\Client;
use Twilio\Jwt\ClientToken;
use Illuminate\Support\Facades\DB;
use Queue;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Redis;

//use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
// use File;
// define ('SITE_ROOT', realpath(dirname(__FILE__)));

class CampaniaController extends Controller {

    public function index() {
        try {
            //code...
            $campanias = null;
            // Usuario::?:
            if(isset($_GET['status'])){
                // 
                $campanias = Campania::where('status',$_GET['status']);
            }else{
                // echo json_encode($_GET['status']); die();
                $campanias = Campania::whereIn('status',['A','I']);
            }
            if(isset($_GET['tipo'])){
                
                $tipo = $_GET['tipo'];
                
                $campanias->where(function ($query) use ($tipo) {
                    $query->where('tipo', $tipo );
                        //   ->orWhere('rol.nombre','like', '%' . $busqueda . '%');
                });
            }
            $campanias->with(['campania_contactos' => function ($q){
                $q->where('campania_contacto.status', 'A');
            }])->with(['campania_intereses' => function ($q){
                $q->where('campania_interes.status', 'A');
            }])->with(['campania_canales' => function ($q){
                $q->where('campania_canal.status', 'A');
            }])->with(['campania_objetivos' => function ($q){
                $q->where('campania_objetivo.status', 'A');
            }]);
            if(isset($_GET['busqueda'])){
                $busqueda = $_GET['busqueda'];
                $campanias->where(function ($query) use ($busqueda) {
                    $query->orWhere('nombre', 'like', '%' . $busqueda . '%');
                        //   ->orWhere('rol.nombre','like', '%' . $busqueda . '%');
                });
            }
            
            
            if(isset($_GET['per_page'])) {
                $campanias = $campanias->paginate($_GET['per_page']);
            }else {
                $campanias = $campanias->get();
            }
            
          
          
            return response()->json($campanias, 200); 
        } catch (\Exception $e) {
            //throw $th;
            return response()->json(["message"=>$e->getMessage()]); 
        }
        
    }

    public function getById($id){
        try {
            //code...
           $campania = Campania::where('campania.id',$id)->where('campania.status',"A");
           $campania->with(['campania_contactos' => function ($q){
            $q->where('campania_contacto.status', 'A');
            }])->with(['campania_intereses' => function ($q){
                $q->where('campania_interes.status', 'A');
            }])->with(['campania_canales' => function ($q){
                $q->where('campania_canal.status', 'A');
            }])->with(['campania_objetivos' => function ($q){
                $q->where('campania_objetivo.status', 'A');
            }]);
            $campania = $campania->first();
            return response()->json($campania, 200); 
        } catch (\Exception $e) {
            //throw $th;
            return response()->json(["message"=>$e->getMessage()]); 
        }
    }

    public function indexSeguimiento() {
        try {
            //code...
            $campanias = null;
            // Usuario::?:
            if(isset($_GET['status'])){
                // 
                $segCampanias = Campania::where('campania.status',$_GET['status'])->where('tipo','E');
            }else{
                // echo json_encode($_GET['status']); die();
                $segCampanias = Campania::whereIn('campania.status',['A','I']);
            }

            $segCampanias->join('seguimiento_campania', function ($join) {
                $join->on('campania.id', '=', 'seguimiento_campania.campania_id');
                $join->where('seguimiento_campania.status','A');
            });
            
            $segCampanias->join('seguimiento_campania_detalle', function ($join) {
                $join->on('seguimiento_campania.id', '=', 'seguimiento_campania_detalle.seguimiento_campania_id');
                $join->where('seguimiento_campania_detalle.status','A');
            });

            $segCampanias->join('campania_canal', function ($join) {
                $join->on('seguimiento_campania_detalle.campania_canal_id', '=', 'campania_canal.id');
                
               
            });
            if(isset($_GET['busqueda'])){
                $busqueda = $_GET['busqueda'];
                $segCampanias->where(function ($query) use ($busqueda) {
                    $query->orWhere('campania.nombre', 'like', '%' . $busqueda . '%');
                        //   ->orWhere('rol.nombre','like', '%' . $busqueda . '%');
                });
            }
            
            $segCampanias->select([
                'campania.nombre',
                'seguimiento_campania.id',
                'seguimiento_campania.fecha_inicio_seguimiento',
                'seguimiento_campania.fecha_fin_seguimiento',
                'campania.nombre as campania',
                // 'mensajes_leidos',
                // 'mensajes_enviados',
                // 'mensajes_entregados',
                // 'mensajes_respondidos',
                // 'usuarios_interesados',
                // 'mensajes_rebotados',

                DB::raw('SUM(CASE WHEN campania_canal.canal_id = 2 THEN seguimiento_campania_detalle.is_entregado END) as mensajes_entregados_ws'),
                DB::raw('SUM(IFNULL(seguimiento_campania_detalle.is_respondido,0)) as mensajes_respondidos'),
                DB::raw('SUM(IFNULL(seguimiento_campania_detalle.is_leido,0)) as mensajes_leidos'),
                DB::raw('SUM(IFNULL(seguimiento_campania_detalle.is_rebotado,0)) as mensajes_rebotados'),
                DB::raw('SUM(IFNULL(seguimiento_campania_detalle.is_entregado,0)) as mensajes_entregados'),
                DB::raw('SUM(IFNULL(seguimiento_campania_detalle.is_enviado,0)) as mensajes_enviados'), 
                DB::raw('SUM(IFNULL(seguimiento_campania_detalle.is_interesado,0)) as usuarios_interesados'),     

            ]);
            
            if(isset($_GET['per_page'])) {
                $segCampanias = $segCampanias->paginate($_GET['per_page']);
            }else {
                $segCampanias = $segCampanias->get();
            }
            

          
            return response()->json($segCampanias, 200); 
        } catch (\Exception $e) {
            //throw $th;
            return response()->json(["message"=>$e->getMessage()]); 
        }
        
    }

    public function delete($id){
        try {
            $campania = Campania::find($id);
            $campania->status = 'I';
            $campania->update();

            return response()->json($campania, 200); 
        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    public function store(Request $request){
        try {
            DB::beginTransaction();
            $input = $request->all();
            $contactos = $input['contactos'];
            $intereses = $input['intereses'];
            $canales = $input['canales'];
            $objetivos = $input['objetivos'];
            $input['tipo'] = 'P';
            if(!isset($input['descripcion']) || $input['descripcion'] == ''){
                $input['descripcion'] = null;
                
            }
            // echo json_encode($input); die();
            $campania = Campania::create($input);
           
            foreach ($objetivos as $key => $value) {
                # code...
                CampaniaObjetivo::create([
                    'campania_id'   => $campania->id,
                    'objetivo_id'   => $value['id'],
                    'usuario_ingresa_id'=>    $campania->usuario_ingresa_id,
                ]);
            }

            foreach ($contactos as $key => $value) {
                # code...
                CampaniaContacto::create([
                    'campania_id'   =>      $campania->id,
                    'contacto_id'   =>      $value['id'],
                    'usuario_ingresa_id'=>  $campania->usuario_ingresa_id,
                    
                ]);
            }

            foreach ($intereses as $key => $value) {
                # code...
                CampaniaInteres::create([
                    'campania_id'   => $campania->id,
                    'interes_id'   => $value['id'],
                    'usuario_ingresa_id'=>  $campania->usuario_ingresa_id,
                ]);
            }

            foreach ($canales as $key => $value) {
                # code...
                CampaniaCanal::create([
                    'campania_id'   => $campania->id,
                    'canal_id'   => $value['id'],
                    'usuario_ingresa_id'=>    $campania->usuario_ingresa_id,
                ]);
            }

            Registro::create([
                'tipo'  => 'G',
                'nombre'   => 'Se ha guardado una campaña.',
                'menu_id'       => '14',
                'usuario_ingresa_id'    => $campania->usuario_ingresa_id
            ]);

            $data = [
                'status'    => 'success',
                'code'      => 201,
                'message'   => 'campania creado correctamente.',  
                'usuario'   => $campania,   
            ];
            DB::commit();
            return response()->json($campania, 200); 
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(["message"=>$e->getMessage()]); 
        }
    }

   

   

    public function ejecutar(Request $request, $id){
        try {
            DB::beginTransaction();
            $sid    = env( 'TWILIO_ACCOUNT_SID' );
            $token  = env( 'TWILIO_AUTH_TOKEN' );
            $client = new Client( $sid, $token );
            
            $campania = Campania::where('id',$id)->with(['campania_contactos' => function ($q){
                $q->where('campania_contacto.status', 'A');
            }])->with(['campania_canales' => function ($q){
                $q->where('campania_canal.status', 'A');
            }])->first();
            $campania_contactos = $campania->campania_contactos;
            $campania_canales = $campania->campania_canales;
            $seguimientoCampania = SeguimientoCampania::create([
                'fecha_inicio_seguimiento'  => date("Y-m-d"),
                'campania_id'               => $campania->id,
                'usuario_ingresa_id'        => $campania->usuario_ingresa_id,
            ]);
            Registro::create([
                'tipo'  => 'G',
                'nombre'   => 'Se ha guardado un seguimiento de campaña',
                'menu_id'       => '14',
                'usuario_ingresa_id'    => $campania->usuario_ingresa_id
            ]);
           foreach ($campania_canales as $key => $campania_canal) {
                foreach ($campania_contactos as $key => $campania_contacto) {
                    $message = null;
                    if(strtolower($campania_canal->canal->nombre)  == strtolower('SMS')){
                        $message = $this->sendSms($client, $campania, $campania_contacto->contacto->celular);
                        // echo json_encode($message); die();
                    }else if(strtolower($campania_canal->canal->nombre) == strtolower('Whatsapp')){
                        
                        $message = $this->sendWhatsAppInicio($client, $campania, $campania_contacto->contacto);
                    }

                    SeguimientoCampaniaDetalle::create([
                        'seguimiento_campania_id'       => $seguimientoCampania->id,
                        'campania_contacto_id'          => $campania_contacto['id'],
                        'campania_canal_id'                => $campania_canal['id'],
                        'message_id'                    => $message->sid,
                        'usuario_ingresa_id'            => $campania->usuario_ingresa_id,
                        // 'estado_mensaje'                => $campania_canal['canal_id']=="2"?'enviado':null,
                        // 'is_enviado'                    =>  $campania_canal['canal_id']==2?1:null,
                    ]);
                    // SeguimientoCampaniaDetalle::create([
                    //     'seguimiento_campania_id'       => $seguimientoCampania->id,
                    //     'campania_contacto_id'          => $campania_contacto['id'],
                    //     'campania_canal_id'                => $campania_canal['id'],
                    //     'message_id'                    => $message?$message->sid:null,
                    //     'usuario_ingresa_id'            => $campania->usuario_ingresa_id,
                    //     'estado_mensaje'                => $campania_canal['canal_id']=="2"?'enviado':null,
                    //     'is_enviado'                    =>  $campania_canal['canal_id']==2?1:null,
                    // ]);
                }
            }
            $campania = Campania::find($id);
            $campania->tipo = "E";
            $campania->update();

            Registro::create([
                'tipo'  => 'M',
                'nombre'   => 'Se ha ejecutado una campaña.',
                'menu_id'       => '14',
                'usuario_ingresa_id'    => $campania->usuario_ingresa_id
            ]);
            DB::commit();
            return response()->json($campania, 200); 
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(["message"=>$e->getMessage()]); 
        }
    }

   
    // public function getPrediccionCampania(Request $request){
    //     try {
    //         DB::beginTransaction();
    //         $campanias = Campania::where('tipo','A')->where('status','A')->get();

    //         foreach ($campanias as $key => $campania) {
    //             # code...
    //             $seguimientosCampania = SeguimientoCampania::where('campania_id', $campania['id'])->get();
    //             foreach ($seguimientosCampania as $key => $seguimiento) {
    //                 # code...
    //                 $demanda = $mensajes
    //             }
    //         }
            
    //         return response()->json($campania, 200); 
    //     } catch (\Exception $e) {
    //         DB::rollback();
    //         return response()->json(["message"=>$e->getMessage()]); 
    //     }
    // }

    


    

    

    private function sendSms($client, $campania, $celular){
        // $mensajeDefecto = " ¿Le pareció interesante la campaña? Responda sí o no. ¡Muchas gracias!";
        // $body = $campania->mensaje . " " . $campania->url . " " .$mensajeDefecto;
        $celularTwilio = Parametro::find(3);

        if($campania->url_media){
            $arreglo_body = [
                'from' => $celularTwilio->valor,
                // 'body' => ($campania->mensaje. ' '. $campania->url.''.$mensajeDefecto),
                'body' => ($campania->mensaje. ' '. $campania->url),
                 //Aquí iría la url del backend y el endpoint
                'statusCallback' => "https://4a111335c34a.ngrok.io/api/campania/recieve-sms",
                'mediaUrl' => "https://4a111335c34a.ngrok.io/api/campania/get-image?imagen=".$campania->url_media,
            ];
            // $arreglo_body['mediaUrl'] = "https://www.google.com/imgres?imgurl=https%3A%2F%2Fkchcomunicacion.com%2Fwp-content%2Fuploads%2F2020%2F06%2Fgoogle-trends-1280x720-1.png&imgrefurl=https%3A%2F%2Fkchcomunicacion.com%2Fgoogle-comenzara-a-pagar-a-los-medios-por-su-contenido-de-noticias%2F&tbnid=0ARXCGj8bbiDSM&vet=12ahUKEwiF0IbB9JTvAhVos1kKHcWrBy0QMygDegUIARDKAQ..i&docid=7_28lP7QSyTnnM&w=1280&h=720&q=imagenes%20google&client=firefox-b-d&ved=2ahUKEwiF0IbB9JTvAhVos1kKHcWrBy0QMygDegUIARDKAQ";
            // echo json_encode($campania->url_media); die();
        }else{
            $arreglo_body = [
                'from' => $celularTwilio->valor,
                // 'body' => ($campania->mensaje. ' '. $campania->url.''.$mensajeDefecto),
                'body' => ($campania->mensaje. ' '. $campania->url),
                 //Aquí iría la url del backend y el endpoint
                'statusCallback' => "https://4a111335c34a.ngrok.io/api/campania/recieve-sms"
            ]; 
        }
        return $client->messages->create(
            $celular,
            $arreglo_body
        );
    }
    
    private function sendWhatsAppInicio($client, $campania, $contacto){
        // $mensajeDefecto = " ¿Le pareció interesante la campaña? Responda sí o no. ¡Muchas gracias!";
        // $celularTwilio = Parametro::find(3);
        $nombres = explode(" ", $contacto->nombres);
        $nombre = "";
        if(sizeof($nombres)> 0){
            $nombre = $nombres[0];
        }
        $arreglo_body = [
            "from" => 'whatsapp:' . env('TWILIO_FROM_WHATSAPP'),
            'body' => "Hola, ".$nombre.", si quiere recibir información, responda este mensaje con un ok. Gracias",
            'statusCallback' => "https://4a111335c34a.ngrok.io/api/campania/recieve-sms"
        ];
        return $client->messages->create('whatsapp:' . $contacto->celular, 
            $arreglo_body
        );
    }
    

    private function sendWhatsApp($client, $campania, $celular){
        $mensajeDefecto = " ¿Le pareció interesante la campaña? Responda sí o no. ¡Muchas gracias!";
        // $celularTwilio = Parametro::find(3);
        if($campania->url_media){
            $arreglo_body = [
                "from" => 'whatsapp:' . env('TWILIO_FROM_WHATSAPP'),
                'body' => $campania->mensaje. ' '. $campania->url.' '.$mensajeDefecto,
                //Aquí iría la url del backend y el endpoint
                'statusCallback' => "https://4a111335c34a.ngrok.io/api/campania/recieve-sms",
                'mediaUrl' => "https://4a111335c34a.ngrok.io/api/campania/get-image?imagen=".$campania->url_media,
            ];
            // $arreglo_body['mediaUrl'] = 'https://4a111335c34a.ngrok.io/api/campania/get-image?imagen='.$campania->url_media;
        }else{
            $arreglo_body = [
                "from" => 'whatsapp:' . env('TWILIO_FROM_WHATSAPP'),
                'body' => $campania->mensaje. ' '. $campania->url.' '.$mensajeDefecto,
                // 'statusCallback' => "https://4a111335c34a.ngrok.io/api/campania/recieve-sms"
            ];
        }

        return $client->messages->create('whatsapp:' . $celular, 
            $arreglo_body
        );
    }

    // private function sendWhatsApp($client, $campania, $celular){
    //     $mensajeDefecto = " ¿Le pareció interesante la campaña? Responda sí o no. ¡Muchas gracias!";
    //     // $celularTwilio = Parametro::find(3);

    //     if($campania->url_media){
    //         $arreglo_body = [
    //             "from" => 'whatsapp:' . env('TWILIO_FROM_WHATSAPP'),
    //             'body' => $campania->mensaje. ' '. $campania->url.$mensajeDefecto,
    //             //Aquí iría la url del backend y el endpoint
    //             'statusCallback' => "https://4a111335c34a.ngrok.io/api/campania/recieve-sms",
    //             'mediaUrl' => "https://4a111335c34a.ngrok.io/api/campania/get-image?imagen=".$campania->url_media,
    //         ];
    //         // $arreglo_body['mediaUrl'] = 'https://4a111335c34a.ngrok.io/api/campania/get-image?imagen='.$campania->url_media;
    //     }else{
    //         $arreglo_body = [
    //             "from" => 'whatsapp:' . env('TWILIO_FROM_WHATSAPP'),
    //             'body' => $campania->mensaje. ' '. $campania->url.$mensajeDefecto,
    //             'statusCallback' => "https://4a111335c34a.ngrok.io/api/campania/recieve-sms"
    //         ];
    //     }

    //     return $client->messages->create('whatsapp:' . $celular, 
    //         $arreglo_body
    //     );
    // }

    public function recieveStatusCallback(Request $request){
        $sid = $_REQUEST['MessageSid'];
        $status = $_REQUEST['MessageStatus'];
        $seguimientoCampaniaDetalle = SeguimientoCampaniaDetalle::where('message_id',$sid)->first();
        $seguimientoCampania = SeguimientoCampania::where('id',$seguimientoCampaniaDetalle->seguimiento_campania_id)->first();
        $campania = Campania::where('id', $seguimientoCampania->campania_id)->where('status','A')->where('tipo','E')->first();
        $estado = $this->getNombreStatus($status);
        if($campania){
            $seguimientoCampaniaDetalle = SeguimientoCampaniaDetalle::find($seguimientoCampaniaDetalle->id);
            $seguimientoCampaniaDetalle->estado_mensaje = $estado;
            
            if($estado == 'enviado'){
                $seguimientoCampania = SeguimientoCampania::find($seguimientoCampania->id)->increment('mensajes_enviados');
                $seguimientoCampaniaDetalle->is_enviado = 1;
            }  if($estado == 'entregado'){
                $seguimientoCampania = SeguimientoCampania::find($seguimientoCampania->id)->increment('mensajes_entregados');
                $seguimientoCampaniaDetalle->is_entregado = 1;
            }  if($estado ==  'fallado'){
                $seguimientoCampania = SeguimientoCampania::find($seguimientoCampania->id)->increment('mensajes_rebotados');
                $seguimientoCampaniaDetalle->is_rebotado = 1;
            }
             if($estado ==  'leido'){
                $seguimientoCampaniaDetalle->is_leido = 1;
                $seguimientoCampania = SeguimientoCampania::find($seguimientoCampania->id)->increment('mensajes_leidos');
            }

            $seguimientoCampaniaDetalle->update();
        }
        // $status = "recibido";
        // event(new \App\Events\CampaniaCreadaEvent($status));
        $out = new \Symfony\Component\Console\Output\ConsoleOutput();
        $out->writeln("SID: $sid, Status: $status, ESTADO: $seguimientoCampania");
    }

    public function recieveRespuesta(Request $request){
        $sid = "hola";
        $body = $_REQUEST['Body'];
        $from = $_REQUEST['From'];
        $celular = substr($from, 9);
        $respuesta = strlen($body)>2?substr($body,0,2):$body;
        $contacto = Contacto::where('celular',$celular)->where('status','A')->first();
        $canal = Canal::where('nombre',strtolower('whatsapp'))->where('status','A')->first();
       
       
        
        $campaniaContacto = CampaniaContacto::where('contacto_id',$contacto->id)->where('status','A')->latest('id')->first();
        $campania = Campania::where('id', $campaniaContacto->campania_id)->where('status','A')->where('tipo','E')->first();
        $campaniaCanal = CampaniaCanal::where('status','A')->where('canal_id',$canal->id)->where('campania_id',$campania->id)->latest('id')->first();
        // $out = new \Symfony\Component\Console\Output\ConsoleOutput();
        // $out->writeln("SID: $sid, Status: $campania, ESTADO: $body");
        // die();
        if($campania){
            $seguimientoCampaniaDetalle = SeguimientoCampaniaDetalle::where('campania_contacto_id',$campaniaContacto->id)->where('campania_canal_id',$campaniaCanal->id)->where('is_respondido',1)->first();
            if(!$seguimientoCampaniaDetalle){
                if(strtoupper($body) == "OK"){
                    // $body="no me interesa";
                    $sid    = env( 'TWILIO_ACCOUNT_SID' );
                    $token  = env( 'TWILIO_AUTH_TOKEN' );
                    $client = new Client( $sid, $token );
                    $message = $this->sendWhatsApp($client, $campania, $campaniaContacto->contacto->celular);

                    $seguimientoCampaniaDetalle = SeguimientoCampaniaDetalle::where('campania_contacto_id',$campaniaContacto->id)->where('campania_canal_id',$campaniaCanal->id);
                    $seguimientoCampaniaDetalle->update([
                        'message_id'  => $message->sid,
                        'estado_mensaje' => "respondido",
                        'is_respondido'  => 1,
                        'is_leido'       => 1,
                    ]);

                    // $out = new \Symfony\Component\Console\Output\ConsoleOutput();
                    // $out->writeln("SID: $sid, Status: $campaniaCanal, ESTADO: $body");

                    // die();
                }
                
                
            }else if($seguimientoCampaniaDetalle){
                // else{

                  
                    $seguimientoCampaniaDetalle = SeguimientoCampaniaDetalle::where('campania_contacto_id',$campaniaContacto->id)->where('campania_canal_id',$campaniaCanal->id);
                    $seguimientoCampaniaDetalle->update([
                        'estado_mensaje' => "respondido",
                        'is_respondido'  => 1,
                        'is_leido'      => 1,
                        // 'is_enviado'    => 1,
                    ]);
    
                   
               
                    
                    
                    
                    $campaniaIntereses = CampaniaInteres::where('campania_id', $campania->id)->where('status','A')->get();
                    // $out = new \Symfony\Component\Console\Output\ConsoleOutput();
                    // $out->writeln("SID: $sid, Status: $campaniaIntereses, ESTADO: $body");
                    // die();
                    // $seguimientoCampania = SeguimientoCampania::where('campania_id',$campania->id)->where('status','A')->first();   
                    // SeguimientoCampania::find($seguimientoCampania->id)->increment('mensajes_respondidos');
                    // $out = new \Symfony\Component\Console\Output\ConsoleOutput();
                    // $out->writeln("SID: $sid, Status: $body, ESTADO: $body");
                    // die();
                    // if(strtoupper($body) == "S" || strtoupper($body) == "SI"  || strtoupper($body) == "SÍ"){
                    $body = $this->eliminar_acentos($body);
                   
                    if( (strtoupper($body) == "S") ||  (strtoupper($body) == "SI") ){
                        // $body="recibido";
                        // $out = new \Symfony\Component\Console\Output\ConsoleOutput();
                        // $out->writeln("SID: $sid, Status: $body, ESTADO: $body");
                        // die();
                        foreach ($campaniaIntereses as $key => $campaniaInteres) {
                            # code...
                            // $interes = Interes::find($value['interes_id']);
                            $contactoIntereses = ContactoInteres::where('contacto_id',$contacto->id)->where('interes_id',$campaniaInteres['interes_id'])->where('status','A')->get();
                            // $isExiste = true;
                            // if(sizeof($contactoIntereses)>0){
                            //     foreach ($contactoIntereses as $key => $contactoInteres) {
                            //         # code...
                            //         if($campaniaInteres['interes_id'] != $contactoInteres['interes_id']){
                            //             $isExiste = false;
                            //         }
                                
                            //     }
                            // }
                        
                            if(sizeof($contactoIntereses) == 0){
                                ContactoInteres::create([
                                    'contacto_id' => $contacto->id,
                                    'interes_id'  => $campaniaInteres['interes_id'],
                                    'usuario_ingresa_id' => $campania->usuario_ingresa_id,
                                ]);
                                
                            
                            }
        
                           
                        
                            
                            // if(!$existeInteres){
                            //    
                            // }
                        
                        }
                        $seguimientoCampaniaDetalle = SeguimientoCampaniaDetalle::where('campania_contacto_id',$campaniaContacto->id)->where('campania_canal_id',$campaniaCanal->id)->first();
                        $seguimientoCampaniaDetalle->update([
                            'is_interesado'  => 1,
                        ]);

                        
                        $seguimientoCampania = SeguimientoCampania::find($seguimientoCampania->id)->increment('usuarios_interesados');
                    }else if(strtoupper($body) == "N" || strtoupper($body) == "NO"){
                        $body="no me interesa";
                        foreach ($campaniaIntereses as $key => $campaniaInteres) {
                            # code...
                            // $interes = Interes::find($value['interes_id']);
                            
                            ContactoInteres::where('contacto_id',$contacto->id)->where('interes_id',$campaniaInteres['interes_id'])->where('status','A')
                            ->update([
                                'status'    => "I",
                                'fecha_elimina' => Carbon::now(),
                            ]);
                        }

                        $seguimientoCampaniaDetalle = SeguimientoCampaniaDetalle::where('campania_contacto_id',$campaniaContacto->id)->where('campania_canal_id',$campaniaCanal->id);
                            $seguimientoCampaniaDetalle->update([
                                'is_interesado'  => 0,
                                
                            ]);
                    }
                // }
            }
           
            
        }
        $out = new \Symfony\Component\Console\Output\ConsoleOutput();
        $out->writeln("SID: $sid, Status: $contacto, ESTADO: $body");
        // $status = "recibido";
        // event(new \App\Events\CampaniaCreadaEvent($status));
        
    }

    private function eliminar_acentos($cadena){
		
		//Reemplazamos la A y a
		$cadena = str_replace(
		array('Á', 'À', 'Â', 'Ä', 'á', 'à', 'ä', 'â', 'ª'),
		array('A', 'A', 'A', 'A', 'a', 'a', 'a', 'a', 'a'),
		$cadena
		);
 
		//Reemplazamos la E y e
		$cadena = str_replace(
		array('É', 'È', 'Ê', 'Ë', 'é', 'è', 'ë', 'ê'),
		array('E', 'E', 'E', 'E', 'e', 'e', 'e', 'e'),
		$cadena );
 
		//Reemplazamos la I y i
		$cadena = str_replace(
		array('Í', 'Ì', 'Ï', 'Î', 'í', 'ì', 'ï', 'î'),
		array('I', 'I', 'I', 'I', 'i', 'i', 'i', 'i'),
		$cadena );
 
		//Reemplazamos la O y o
		$cadena = str_replace(
		array('Ó', 'Ò', 'Ö', 'Ô', 'ó', 'ò', 'ö', 'ô'),
		array('O', 'O', 'O', 'O', 'o', 'o', 'o', 'o'),
		$cadena );
 
		//Reemplazamos la U y u
		$cadena = str_replace(
		array('Ú', 'Ù', 'Û', 'Ü', 'ú', 'ù', 'ü', 'û'),
		array('U', 'U', 'U', 'U', 'u', 'u', 'u', 'u'),
		$cadena );
 
		//Reemplazamos la N, n, C y c
		$cadena = str_replace(
		array('Ñ', 'ñ', 'Ç', 'ç'),
		array('N', 'n', 'C', 'c'),
		$cadena
		);
		
		return $cadena;
	}

    private function normalizarTexto($input) {
        $input = iconv('UTF-8', 'ASCII//TRANSLIT', $input);
        $input = preg_replace('/[^a-zA-Z0-9]/', '_', $input);
        $input = strtolower($input);
        // echo $input;
        return $input;
      }

    private function getNombreStatus($status){
        $estado = "";
        switch ($status) {
            case 'accepted':
               $estado = 'aceptado';
                break;
            case 'queued':
               $estado = 'cola';
                break;
            case 'sending':
               $estado = 'enviando';
                break;
            case 'sent':
               $estado = 'enviado';
                break;
            case 'failed':
               $estado = 'fallado';
                break;

            case 'delivered':
               $estado = 'entregado';
                break;
            case 'undelivered':
               $estado = 'no_entregado';
                break;
            case 'receiving':
               $estado = 'recibiendo';
                break;
            case 'received':
               $estado = 'recibido';
                break;
            case 'read':
               $estado = 'leido';
                break;
            
            default:
               $estado = 'aceptado';
                break;
        }

        return $estado;
    }

    public function sendMessages($campania)
    {
        $sid    = env('TWILIO_ACCOUNT_SID' );
        $token  = env('TWILIO_AUTH_TOKEN' );
        $client = new Client( $sid, $token );
        try
        {
            $canales = $campania->campania_canales;

            foreach ($canales as $key => $value) {
                # code...
                if($value->canal->nombre == 'SMS'){
                    $this->sendSms($client,$campania);
                }else if ($value->canal->nombre == 'WhatsApp'){
                    $this->sendWhatsApp($client,$campania);
                }
            }

            // $this->sendWhatsApp($input);
        }
        catch (Exception $e)
        {
            echo "Error: " . $e->getMessage();
        }
    }

    public function getCampaniaRecomendacion(){
        try {
            $campanias = Campania::where('tipo','A')->where('status','A')->get();
            $campaniasRecomendadas = [];
            
            foreach ($campanias as $key => $campania) {
                # code...
                
                $seguimientosCampania = SeguimientoCampania::where('campania_id',$campania['id'])->where('status','I')->get();
                $x = [];
                $y = [];
               if(sizeof($seguimientosCampania) >= 3){
                foreach ($seguimientosCampania as $key => $value) {
                    # code...
                    $x[] = [$value['id']];
                    $rate = ($value['usuarios_interesados']/$value['mensajes_entregados']);
                    $y[] = $rate;
                }
                // echo json_encode($y); die();
                
                $idAPredecir = (end($seguimientosCampania)[0]->id + 1);
                // $x = [[1], [2], [3], [4], [5], [6]];
                
                // $y = [1, 3, 5, 6, 8, 10];

                $regression = new LeastSquares();
                $regression->train($x, $y);

               
                //  ;
                $ratePredecido = $regression->predict([$idAPredecir]);
                
                // echo json_encode($ratePredecido);
                if($ratePredecido > 0.19) {
                    
                    $campania = [
                        'id' => $campania['id'],
                        'prediccion' => $ratePredecido,
                    ];

                    array_push($campaniasRecomendadas,$campania);
                    
                }
               }
               
            }

            // echo json_encode($campaniasRecomendadas); die();
            // die();
            return response()->json($campaniasRecomendadas, 200); 
        } catch (\Exception $e) {
            return response()->json(["message"=>$e->getMessage()]); 
        }
            // echo json_encode($contactosRecomendados); die();
            // var_dump($engine->getRecomendacion());
        
    }

    public function update(Request $request, $id){
        try {
            DB::beginTransaction();
            $input = $request->all();
            $campania = Campania::find($id);
            $campania->update($input);
            $input = $request->all();
            $contactos = $input['contactos'];
            $intereses = $input['intereses'];
            $canales = $input['canales'];
            $objetivos = $input['objetivos'];
            $campania = Campania::find($id);

            $campania_contacto = CampaniaContacto::where('campania_id', $campania->id)->update([
                'status'    => 'I',
                'usuario_modifica_id' => $input['usuario_ingresa_id'],
                'fecha_modifica'    => Carbon::now(),
            ]);
            foreach ($contactos as $key => $value) {
                CampaniaContacto::create([
                    'campania_id'   => $campania->id,
                    'contacto_id'   => $value['id'],
                    'usuario_ingresa_id' => $campania->usuario_ingresa_id,
                ]);
            }

            $campania_interes = CampaniaInteres::where('campania_id', $campania->id)->update([
                'status'    => 'I',
                'usuario_modifica_id' => $input['usuario_ingresa_id'],
                'fecha_modifica'    => Carbon::now(),
            ]);

            foreach ($intereses as $key => $value) {
                CampaniaInteres::create([
                    'campania_id'   => $campania->id,
                    'interes_id'   => $value['id'],
                    'usuario_ingresa_id' => $input['usuario_ingresa_id'],
                ]);
                
            }

            $campania_canal = CampaniaCanal::where('campania_id', $campania->id)->update([
                'status'    => 'I',
                'usuario_modifica_id' => $input['usuario_ingresa_id'],
                'fecha_modifica'    => Carbon::now(),
            ]);

            foreach ($canales as $key => $value) {
             
                CampaniaCanal::create([
                    'campania_id'   => $campania->id,
                    'canal_id'   => $value['id'],
                    'usuario_ingresa_id' => $input['usuario_ingresa_id'],
                    
                ]);
                
                
            }

            $campania_obejtivo = CampaniaObjetivo::where('campania_id', $campania->id)->update([
                'status'    => 'I',
                'usuario_modifica_id' => $input['usuario_ingresa_id'],
                'fecha_modifica'    => Carbon::now(),
            ]);

            foreach ($objetivos as $key => $value) {
             
                CampaniaObjetivo::create([
                    'campania_id'   => $campania->id,
                    'objetivo_id'   => $value['id'],
                    'usuario_ingresa_id' => $input['usuario_ingresa_id'],
                ]);
                
                
            }

            $data = [
                'status'    => 'success',
                'code'      => 201,
                'message'   => 'campania modificada correctamente.',  
                'usuario'   => $campania,   
            ];

            Registro::create([
                'tipo'  => 'M',
                'nombre'   => 'Se ha editado una campaña.',
                'menu_id'       => '14',
                'usuario_ingresa_id'    => $input['usuario_ingresa_id'],
            ]);
            DB::commit();
            return response()->json($campania, 200); 
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(["message"=>$e->getMessage()]); 
        }
    }

    // public function addImage(Request $request){
    //     $input = $request->all();
    //     $image = $_FILES['image'];
    //     if($image){
    //         $name = $_FILES['image']['name'];
    //         $guardado = $_FILES['image']['tmp_name'];
    //         $destinationPath = base_path() . '/images/campanias/' . $name.'.jpg';
    //         move_uploaded_file($_FILES['image']['tmp_name'], SITE_ROOT.'/images/campanias/'.$name);
    //     }
    //     return response()->json($image,200);
    // }

    public function addImage(Request $request){
        $input = $request->all();
        $image = $_FILES['image'];
        if($image){
            $name = $_FILES['image']['name'];
            $guardado = $_FILES['image']['tmp_name'];
            // $destinationPath = base_path() . '/images/campanias/' . $name.'.jpg';
            Storage::disk('campanias')->put($name,  file_get_contents($guardado));
            // move_uploaded_file($_FILES['image']['tmp_name'], SITE_ROOT.'/images/campanias/'.$name);
        }
        return response()->json($image,200);
    }

    public function getImage(){
        // $_GET['imagen'] = 'aplicacionesweb1.jpg';
        // $filename = basename($file);
        $file_extension = strtolower(substr(strrchr($_GET['imagen'],"."),1));

        switch( $file_extension ) {
            case "gif": $ctype="image/gif"; break;
            case "png": $ctype="image/png"; break;
            case "jpeg":
            case "jpg": $ctype="image/jpeg"; break;
            case "svg": $ctype="image/svg+xml"; break;
            default:
        }
        $file = Storage::disk('campanias')->get($_GET['imagen']);
        return response($file, 200)->header('Content-Type', $ctype);
    }

    public function getTotales(){
        try {
            DB::beginTransaction();
            $totales = [];
            $campaniasEjecutadas = Campania::where('tipo','E')->where('status','A')->get()->toArray();
            $campaniasProgramadas = EventoCampania::where('status','A')->get()->toArray();
            $contactos = Contacto::where('status','A')->get()->toArray();
            $seguimientoCampaniaDetalle = \DB::table('seguimiento_campania_detalle')->select([
                DB::raw('SUM(is_respondido) as mensajes_respondidos'),
                DB::raw('SUM(is_rebotado) as mensajes_rebotados'),
                DB::raw('SUM(is_enviado) as mensajes_enviados'), 
                DB::raw('SUM(is_entregado) as mensajes_entregados'),   
            ])->get();

            // echo json_encode($seguimientoCampania); die();
            // $seguimientoCampania-
            $totales = [
                'campanias_ejecutadas' => sizeof($campaniasEjecutadas),
                'campanias_programadas' => sizeof($campaniasProgramadas),
                'contactos'             => sizeof($contactos),
                'mensajes_respondidos'  => $seguimientoCampaniaDetalle[0]->mensajes_respondidos,
                'mensajes_rebotados'  => $seguimientoCampaniaDetalle[0]->mensajes_rebotados,
                'mensajes_enviados'  => $seguimientoCampaniaDetalle[0]->mensajes_enviados,
                'mensajes_entregados'  => $seguimientoCampaniaDetalle[0]->mensajes_entregados,

            ];
            DB::commit();
            return response()->json($totales, 200); 
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(["message"=>$e->getMessage()]); 
        }
    }

    public function getUltimaCampania(){
        try {
            DB::beginTransaction();
            $campania = Campania::where('campania.status','A')->where('campania.tipo','E');
        
            $campania->join('seguimiento_campania', function ($join) {
                $join->on('campania.id', '=', 'seguimiento_campania.campania_id');
                $join->where('seguimiento_campania.status','A');
                // $join->where('campania.tipo', "E");
            });
            $campania->leftJoin('seguimiento_campania_detalle', function ($join) {
                $join->on('seguimiento_campania.id', '=', 'seguimiento_campania_detalle.seguimiento_campania_id');
            });
            $campania->join('campania_canal', function ($join) {
                $join->on('seguimiento_campania_detalle.campania_canal_id', '=', 'campania_canal.id');
                
              
            });
            $campania->groupBy('seguimiento_campania.id');
            $campania->select([
                'seguimiento_campania.id',
                'campania.nombre',
                // 'seguimiento_campania_detalle.is_respondido',
                DB::raw('SUM(CASE WHEN campania_canal.canal_id = 2 THEN seguimiento_campania_detalle.is_entregado END) as mensajes_entregados_ws'),
                // DB::raw('IFNULL(SUM(seguimiento_campania_detalle.is_respondido),0) as mensajes_respondidos'),
                DB::raw('SUM(IFNULL(seguimiento_campania_detalle.is_respondido,0)) as mensajes_respondidos'),
                DB::raw('SUM(IFNULL(seguimiento_campania_detalle.is_leido,0)) as mensajes_leidos'),
                DB::raw('SUM(IFNULL(seguimiento_campania_detalle.is_rebotado,0)) as mensajes_rebotados'),
                DB::raw('SUM(IFNULL(seguimiento_campania_detalle.is_entregado,0)) as mensajes_entregados'),
                DB::raw('SUM(IFNULL(seguimiento_campania_detalle.is_enviado,0)) as mensajes_enviados'), 
                DB::raw('SUM(IFNULL(seguimiento_campania_detalle.is_interesado,0)) as usuarios_interesados'),     
                // 'campania_interes.interes_id as interes',
                // 'seguimiento_campania.id as seg',
                'campania.id as campania_id',
                'campania.tipo',
            ]);

            $campania = $campania->latest('seguimiento_campania.id')->first();
            

            $campaniaCanales = CampaniaCanal::where('campania_canal.campania_id',$campania->campania_id)->where('campania_canal.status','A')->get()->toArray();
        
            $totales = [
                'campania' => [],
                'canales' => [],
            
            ];

            for ($i = 0; $i < sizeof($campaniaCanales); $i++) {
                # code...
                
                $seguimientoCampania = SeguimientoCampaniaDetalle::where('campania_canal_id',$campaniaCanales[$i]['id'])->where('seguimiento_campania_detalle.status','A');
                $seguimientoCampania->join('campania_canal', function ($join) {
                    $join->on('campania_canal.id', '=', 'seguimiento_campania_detalle.campania_canal_id');
                    $join->where('campania_canal.status','A');
                    // $join->where('campania.tipo', "E");
                });
                $seguimientoCampania->join('canal', function ($join) {
                    $join->on('canal.id', '=', 'campania_canal.canal_id');
                    $join->where('canal.status','A');
                    // $join->where('campania.tipo', "E");
                });
                $seguimientoCampania->select([
                    // 'seguimiento_campania.id',
                    // 'campania.nombre',
                    'seguimiento_campania_detalle.campania_canal_id',
                    'canal.nombre',
                    // DB::raw('IFNULL(SUM(seguimiento_campania_detalle.is_respondido),0) as mensajes_respondidos'),
                    DB::raw('SUM(CASE WHEN campania_canal.canal_id = 2 THEN seguimiento_campania_detalle.is_entregado END) as mensajes_entregados_ws'),
                    DB::raw('SUM(IFNULL(seguimiento_campania_detalle.is_respondido,0)) as mensajes_respondidos'),
                    DB::raw('SUM(IFNULL(seguimiento_campania_detalle.is_leido,0)) as mensajes_leidos'),
                    DB::raw('SUM(IFNULL(seguimiento_campania_detalle.is_rebotado,0)) as mensajes_rebotados'),
                    DB::raw('SUM(IFNULL(seguimiento_campania_detalle.is_entregado,0)) as mensajes_entregados'),
                    DB::raw('SUM(IFNULL(seguimiento_campania_detalle.is_enviado,0)) as mensajes_enviados'), 
                    DB::raw('SUM(IFNULL(seguimiento_campania_detalle.is_interesado,0)) as usuarios_interesados'),     
                    // 'campania_interes.interes_id as interes',
                    // 'seguimiento_campania.id as seg',
                    // 'campania.tipo',
                ]);
                $seguimientoCampania = $seguimientoCampania->first();
                
                $data['canales'][] = $seguimientoCampania;
                
            }
            $data['campania'] = $campania;
            DB::commit();
            return response()->json($data, 200); 
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(["message"=>$e->getMessage()]); 
        }
            // echo json_encode($data); die();
    }

    public function getReporteSeguimiento(){
        try {
            DB::beginTransaction();
            $totales = [];
            $seguimientosCampanias = SeguimientoCampania::join('campania', function ($join) {
                $join->on('campania.id', '=', 'seguimiento_campania.campania_id');
                
                
            });

            $seguimientosCampanias->join('seguimiento_campania_detalle', function ($join) {
                $join->on('seguimiento_campania.id', '=', 'seguimiento_campania_detalle.seguimiento_campania_id');
                
                
            });
            if(isset($_GET['tipo'])){
                $seguimientosCampanias->where('campania.tipo',$_GET['tipo']);
            }else if(!isset($_GET['tipo'])){
                $seguimientosCampanias->whereIn('campania.tipo',['A','E']);
            }
            if(isset($_GET['busqueda'])){
                $busqueda = $_GET['busqueda'];
                $seguimientosCampanias->where(function ($query) use ($busqueda) {
                    $query->orWhere('campania.nombre', 'like', '%' . $busqueda . '%');
                        //   ->orWhere('rol.nombre','like', '%' . $busqueda . '%');
                });
            }
            // echo json_encode($_GET['canales']);die();

            if(isset($_GET['fecha_inicio']) && isset($_GET['fecha_fin'])){
                $seguimientosCampanias->whereBetween('seguimiento_campania.fecha_inicio_seguimiento', [$_GET['fecha_inicio'], $_GET['fecha_fin']]);
            }
            
            $seguimientosCampanias->join('campania_canal', function ($join) {
                $join->on('seguimiento_campania_detalle.campania_canal_id', '=', 'campania_canal.id');
                
                if(isset($_GET['canales'])){
                    $canales = explode(",", $_GET['canales']);
                    $join->whereIn('campania_canal.canal_id', $canales);
                }
            });
            $seguimientosCampanias->join('campania_interes', function ($join) {
                $join->on('campania.id', '=', 'campania_interes.campania_id');
                
                if(isset($_GET['intereses'])){
                    $intereses = explode(",", $_GET['intereses']);
                    $join->whereIn('interes_id', $intereses);
                }
            });
            
            // $contactos = Contacto::where('status','A')->get()->toArray();
            // $seguimientoCampania = \DB::table('seguimiento_campania')->select([
            //     DB::raw('SUM(mensajes_respondidos) as mensajes_respondidos'),
            //     DB::raw('SUM(mensajes_rebotados) as mensajes_rebotados'),
            //     DB::raw('SUM(mensajes_enviados) as mensajes_enviados'),   
            // ])->get();
            $seguimientosCampanias->orderBy('campania.fecha_ingresa','DESC');
                $seguimientosCampanias->groupBy('seguimiento_campania.id');
                $seguimientosCampanias =$seguimientosCampanias->select([
                    'seguimiento_campania.id as id',
                    'campania.nombre',
                    'seguimiento_campania.fecha_inicio_seguimiento',
                    'seguimiento_campania.fecha_fin_seguimiento',
                    DB::raw('SUM(CASE WHEN campania_canal.canal_id = 2 THEN seguimiento_campania_detalle.is_entregado END) as mensajes_entregados_ws'),
                    DB::raw('SUM(IFNULL(seguimiento_campania_detalle.is_respondido,0)) as mensajes_respondidos'),
                    DB::raw('SUM(IFNULL(seguimiento_campania_detalle.is_leido,0)) as mensajes_leidos'),
                    DB::raw('SUM(IFNULL(seguimiento_campania_detalle.is_rebotado,0)) as mensajes_rebotados'),
                    DB::raw('SUM(IFNULL(seguimiento_campania_detalle.is_entregado,0)) as mensajes_entregados'),
                    DB::raw('SUM(IFNULL(seguimiento_campania_detalle.is_enviado,0)) as mensajes_enviados'), 
                    DB::raw('SUM(IFNULL(seguimiento_campania_detalle.is_interesado,0)) as usuarios_interesados'),     
                    // 'campania_interes.interes_id as interes',
                    // 'seguimiento_campania.id as seg',
                    'campania.tipo',
                    // 'campania_canal.canal_id as canal',
                    'seguimiento_campania.campania_id'
                ]);
                if(isset($_GET['per_page'])) {
                    $seguimientosCampanias = $seguimientosCampanias->paginate($_GET['per_page']);
                }else {
                    $seguimientosCampanias = $seguimientosCampanias->get();
                }
            // echo json_encode($seguimientoCampania); die();
            // $seguimientoCampania-
          
            DB::commit();
            return response()->json($seguimientosCampanias, 200); 
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(["message"=>$e->getMessage()]); 
        }
    }

    public function archivar(Request $request, $id){
        try {
            DB::beginTransaction();
            $sid    = env( 'TWILIO_ACCOUNT_SID' );
            $token  = env( 'TWILIO_AUTH_TOKEN' );
            $client = new Client( $sid, $token );
            
            $campania = Campania::where('id',$id)->with(['campania_contactos' => function ($q){
                $q->where('campania_contacto.status', 'A');
            }])->with(['campania_canales' => function ($q){
                $q->where('campania_canal.status', 'A');
            }])->first();
            $campania_contactos = $campania->campania_contactos;
            $campania_canales = $campania->campania_canales;
            $seguimientoCampania = SeguimientoCampania::where('campania_id',$campania->id)->where('status','A')->first();
            SeguimientoCampania::where('id',$seguimientoCampania->id)->update(['status'=>"I"]);
            $campania_canal = CampaniaCanal::where('campania_id', $campania->id)->where('status',"A")->update(['status'=>"I"]);
            $campania_contacto = CampaniaContacto::where('campania_id', $campania->id)->where('status',"A")->update(['status'=>"I"]);
            $campania_interes = CampaniaInteres::where('campania_id', $campania->id)->where('status',"A")->update(['status'=>"I"]);
            
           SeguimientoCampaniaDetalle::where('seguimiento_campania_id',$seguimientoCampania->id)->where('status','A')->update(['status'=>"I"]);
            $campania = Campania::find($id);
            $campania->tipo = "A";
            $campania->update();
            DB::commit();
            Registro::create([
                'tipo'  => 'M',
                'nombre'   => 'Se ha archivado una campaña.',
                'menu_id'       => '14',
                'usuario_ingresa_id'    => $campania->usuario_ingresa_id
            ]);
            return response()->json($campania, 200); 
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(["message"=>$e->getMessage()]); 
        }
    }

    public function activar(Request $request, $id){
        try {
            DB::beginTransaction();
            $campania = Campania::find($id);
            $campania->tipo = "P";
            $campania->update();
            DB::commit();
            Registro::create([
                'tipo'  => 'M',
                'nombre'   => 'Se ha activado una campaña.',
                'menu_id'       => '14',
                'usuario_ingresa_id'    => $campania->usuario_ingresa_id
            ]);
            return response()->json($campania, 200); 
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(["message"=>$e->getMessage()]); 
        }
    }

    

    public static function deleteDelayedJob($job_id, $queue = 'default') {
		$redis_queue_instance = Queue::getRedis();
		// use redis ZSCAN with MATCH to find by pattern, its similar to SQL LIKE %jobid%
		$res = $redis_queue_instance->zscan('queues:'.$queue.':delayed', 0, 'MATCH', "*$job_id*");
		if ($res) { // make sure result is found
			if (isset($res[1])) { // first element is cursor, second is array with result
				$job_arr = $res[1];
				if (isset($job_arr[0])) { // make sure second element is array and has index 0
					$job = $job_arr[0]; // get the job id
					// remove the job which is literally removing element from Sorted Set
					return $redis_queue_instance->zrem('queues:'.$queue.':delayed', $job);
				}
			}
		}
		return 0; // not removed
//		throw new RuntimeException("Job id: $job_id not found for queue: $queue");
	}
    

   
    
}
