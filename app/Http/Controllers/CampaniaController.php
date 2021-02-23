<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Campania;
use App\Models\SeguimientoCampania;
use App\Models\SeguimientoCampaniaDetalle;
use App\Models\EventoCampania;
use App\Models\CampaniaCanal;
use App\Models\CampaniaContacto;
use App\Models\CampaniaInteres;
use App\Jobs\EjecutarCampaniaJob;


use Twilio\Rest\Client;
use Twilio\Jwt\ClientToken;
use Illuminate\Support\Facades\DB;
use Queue;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Redis;



class CampaniaController extends Controller {

    public function index() {
        try {
            //code...
            $campanias = Campania::whereIn('status',['A','I'])->with(['campania_contactos' => function ($q){
                $q->where('campania_contacto.status', 'A');
            }])->with(['campania_intereses' => function ($q){
                $q->where('campania_interes.status', 'A');
            }])->with(['campania_canales' => function ($q){
                $q->where('campania_canal.status', 'A');
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
            $input['tipo'] = 'P';

            // echo json_encode($input); die();
            $campania = Campania::create($input);
           

            foreach ($contactos as $key => $value) {
                # code...
                CampaniaContacto::create([
                    'campania_id'   => $campania->id,
                    'contacto_id'   => $value['id'],
                ]);
            }

            foreach ($intereses as $key => $value) {
                # code...
                CampaniaInteres::create([
                    'campania_id'   => $campania->id,
                    'interes_id'   => $value['id'],
                ]);
            }

            foreach ($canales as $key => $value) {
                # code...
                CampaniaCanal::create([
                    'campania_id'   => $campania->id,
                    'canal_id'   => $value['id'],
                ]);
            }

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

    public function ejecutarEvento(Request $request){
        $input = $request->all();
        $eventoCampania = EventoCampania::create($input);
        $job = (new EjecutarCampaniaJob($eventoCampania))->onQueue('add_something');

        // $job = (new ->delay(21321312315000);
        // $job = (new EjecutarCampaniaJob($eventoCampania))->delay(Carbon::now()->addSeconds(90));
        // $this->dispatch($job);

        // \Queue::later(delay,job)
        // $date = Carbon::now()->addMinutes(1);
        $date = Carbon::now()->addSeconds(15);
        \Queue::later($date,  $job);
        $this->eliminarJobEvento($eventoCampania->id);
    }

    public function eliminarJobEvento($id){
        // $job = \Queue::getPheanstalk()->useTube("evento_".$id)->peek($res);
        // //get the job from the que that you just pushed it to
        // $res = \Queue::getPheanstalk()->useTube("evento_".$id)->delete($job);
        $myqueue = 'add_something';
        \Queue::getRedis()->connection()->del('queues:'.$myqueue);
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

            
            // $campania_contactos = CampaniaContactos::where('campania_id',$campania->id)->with('contacto')->get()->toArray();
            // $campania_canales = CampaniaCanales::where('campania_id',$campania->id)->with('canal')->get()->toArray();
            $campania_contactos = $campania->campania_contactos;
            $campania_canales = $campania->campania_canales;

            // echo json_encode($campania_contactos); die();
            $seguimientoCampania = SeguimientoCampania::create([
                'fecha_inicio_seguimiento'  => date("Y-m-d"),
                'campania_id'               => $campania->id,
                'usuario_ingresa_id'        => $campania->usuario_ingresa_id,
            ]);
           foreach ($campania_canales as $key => $campania_canal) {
                foreach ($campania_contactos as $key => $campania_contacto) {
                    # code...
                        
                            # code...
                    SeguimientoCampaniaDetalle::create([
                        'seguimiento_campania_id'       => $seguimientoCampania->id,
                        'campania_contacto_id'          => $campania_contacto->id,
                        'canal_canal_id'                => $campania_canal->id,
                        'usuario_ingresa_id'            => $campania->usuario_ingresa_id,
                    ]);

                    if(strtolower($campania_canal->canal->nombre)  == strtolower('SMS')){
                        // echo json_encode($campania_canal->canal->nombre); 
                        $this->sendSms($client, $campania, $campania_contacto->contacto->celular);
                    }else if(strtolower($campania_canal->canal->nombre) == strtolower('Whatsapp')){
                        
                        $this->sendWhatsApp($client, $campania, $campania_contacto->contacto->celular);
                    }


                }
                    
                

            }

            // die();
            $campania = Campania::find($id);
            $campania->tipo = 'E';
            $campania->update();
            DB::commit();
            return response()->json($campania, 200); 
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(["message"=>$e->getMessage()]); 
        }
    }

    public function sendMessages($campania)
    {
        $sid    = env('TWILIO_ACCOUNT_SID' );
        $token  = env('TWILIO_AUTH_TOKEN' );
        $client = new Client( $sid, $token );
        // $input = $request->all();

        // echo json_encode($input); die();
        // $appSid     = config('app.twilio')['TWILIO_APP_SID'];
        // $client = new Client($accountSid, $authToken);
        try
        {
            // Use the client to do fun stuff like send text messages!
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

    private function sendSms($client, $campania, $celular){
        // $contactos = $campania->campania_contactos;
        // foreach ($contactos as $key => $value) {
            // echo json_encode($client); die();
            $arreglo_body = [
                'from' => env('TWILIO_FROM_SMS'),
                    // the body of the text message you'd like to send
                'body' => $campania->mensaje,
                'statusCallback' => "https://22e36b6c653c.ngrok.io/api/campania/recieve-sms"
            ];

            if($campania->mediar_url){
                $arreglo_body['mediaUrl'] = $campania->media_url;
            }
            
            // echo json_encode($arreglo_body); die();
            $client->messages->create(
                // the number you'd like to send the message to
                $celular,
                // array(
                // // A Twilio phone number you purchased at twilio.com/console
                //     // "mediaUrl" => ['https://www.google.com'],
                //     'from' => env('TWILIO_FROM_SMS'),
                //     // the body of the text message you'd like to send
                //     'body' => $campania->mensaje,
                //     'statusCallback' => "https://22e36b6c653c.ngrok.io/api/campania/recieve-sms"
                // )
                $arreglo_body
            );
        // }
    }
    

    private function sendWhatsApp($client, $campania, $celular){
    //    $twilio_whatsapp_number = env('TWILIO_WHATSAPP_NUMBER');
        // $sid    = env( 'TWILIO_ACCOUNT_SID' );
        // $token  = env( 'TWILIO_AUTH_TOKEN' );

        // $client = new Client($sid, $token);
        //    return $client->messages->create($recipient, array('from' => "whatsapp:$twilio_whatsapp_number", 'body' => $message));
        // $contactos = $campania->campania_contactos;
        // foreach ($contactos as $key => $value) {
            # code...
            $arreglo_body = [
                "from" => 'whatsapp:' . env('TWILIO_FROM_WHATSAPP'),
                    // the body of the text message you'd like to send
                'body' => $campania->mensaje,
                'statusCallback' => "https://22e36b6c653c.ngrok.io/api/campania/recieve-sms"
            ];

            if($campania->mediar_url){
                $arreglo_body['mediaUrl'] = $campania->media_url;
            }
            $client->messages->create('whatsapp:' . $celular, 
            $arreglo_body
            // [
            //     // "mediaUrl" => ['https://www.google.com'],
            //     "from" => 'whatsapp:' . env('TWILIO_FROM_WHATSAPP'),
            //     'body' => $campania->mensaje,
            //     'statusCallback' => "https://22e36b6c653c.ngrok.io/api/campania/recieve-sms"
            // ]
        );
        // }
       
    }

    // public function update(Request $request, $id){
    //     try {
    //         $input = $request->all();
    //         $campania = Campania::find($id);
    //         // $campania->nombres = $input['nombres'];
    //         // $campania->apellidos = $input['apellidos'];
    //         // $campania->apellidos = $input['apellidos'];
    //         $campania->update($input);

    //         return response()->json($campania, 200); 
    //     } catch (\Exception $e) {
    //         return response()->json(["message"=>$e->getMessage()]); 
    //     }
    // }

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
            // $input['tipo'] = 'P';

            
            // $campania = Campania::create($input);
            $campania = Campania::find($id);

            $campania_contacto = CampaniaContacto::where('campania_id', $campania->id)->update([
                'status'    => 'I'
            ]);
            foreach ($contactos as $key => $value) {
                
                # code...
                // $campania_contacto = CampaniaContacto::find($value['id']);
                
                // $campania_contacto->status = 'I';
                // $campania_contacto->update();
                

                // echo json_encode($campania_contacto); die();
                CampaniaContacto::create([
                    'campania_id'   => $campania->id,
                    'contacto_id'   => $value['id'],
                    'usuario_ingresa_id' => $campania->usuario_ingresa_id,
                ]);
                
               
            }

            $campania_interes = CampaniaInteres::where('campania_id', $campania->id)->update([
                'status'    => 'I'
            ]);

            foreach ($intereses as $key => $value) {
                # code...
                // $campania_interes = CampaniaInteres::find($value['id']);
                // $campania_interes->status = 'I';
                // $campania_interes->update();
                
                
                CampaniaInteres::create([
                    'campania_id'   => $campania->id,
                    'interes_id'   => $value['id'],
                    'usuario_ingresa_id' => $campania->usuario_ingresa_id,
                ]);
                
            }

            $campania_canal = CampaniaCanal::where('campania_id', $campania->id)->update([
                'status'    => 'I'
            ]);

            foreach ($canales as $key => $value) {
                # code...
                // $campania_canal = CampaniaCanal::find($value['id']);
                // $campania_canal->status = 'I';
                // $campania_canal->update();
                
                CampaniaCanal::create([
                    'campania_id'   => $campania->id,
                    'canal_id'   => $value['id'],
                    'usuario_ingresa_id' => $campania->usuario_ingresa_id,
                ]);
                
                
            }

            $data = [
                'status'    => 'success',
                'code'      => 201,
                'message'   => 'campania modificada correctamente.',  
                'usuario'   => $campania,   
            ];
            DB::commit();
            return response()->json($campania, 200); 
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(["message"=>$e->getMessage()]); 
        }
    }

   
    
}
