<?php

namespace App\Jobs;

use Mail;
use App\User;
use App\Jobs\Job;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
// use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\EventoCampania;

use App\Models\Campania;
use App\Models\SeguimientoCampania;
use App\Models\SeguimientoCampaniaDetalle;
use App\Jobs\EjecutarCampaniaJob;

use Illuminate\Support\Facades\DB;
use Twilio\Rest\Client;
use Twilio\Jwt\ClientToken;
use Illuminate\Support\Carbon;

class EjecutarCampaniaJob extends Job implements ShouldQueue
{
    use SerializesModels;

    protected $eventoCampania;

    /**
     * Create a new job instance.
     *
     * @param  User  $user
     * @return void
     */
    public function __construct(EventoCampania $eventoCampania)
    {
        $this->eventoCampania = $eventoCampania;
    }

    /**
     * Execute the job.
     *
     * @param  Mailer  $mailer
     * @return void
     */
    public function handle()
    {
        try {
            // $this->release(Carbon::now()->addMinutes(1));
            DB::beginTransaction();
            $sid    = env( 'TWILIO_ACCOUNT_SID' );
            $token  = env( 'TWILIO_AUTH_TOKEN' );
            $client = new Client( $sid, $token );
            
            $campania = Campania::where('id',$this->eventoCampania->campania_id)->with(['campania_contactos' => function ($q){
                $q->where('campania_contacto.status', 'A');
            }])->with(['campania_canales' => function ($q){
                $q->where('campania_canal.status', 'A');
            }])->first();

            
            
            // $campania_contactos = CampaniaContactos::where('campania_id',$campania->id)->with('contacto')->get()->toArray();
            // $campania_canales = CampaniaCanales::where('campania_id',$campania->id)->with('canal')->get()->toArray();
            $campania_contactos = $campania->campania_contactos;
            $campania_canales = $campania->campania_canales;

            
            $seguimientoCampania = SeguimientoCampania::create([
                'fecha_inicio_seguimiento'  => $this->eventoCampania->fecha_inicio,
                'fecha_fin_seguimiento'  => $this->eventoCampania->fecha_fin,
                'campania_id'               => $campania->id,
                'usuario_ingresa_id'        => $campania->usuario_ingresa_id,
            ]);

            
           foreach ($campania_canales as $key => $campania_canal) {
                foreach ($campania_contactos as $key => $campania_contacto) {
                    # code...
                        
                            # code...
                    $segui = SeguimientoCampaniaDetalle::create([
                        'seguimiento_campania_id'       => $seguimientoCampania->id,
                        'campania_contacto_id'          => $campania_contacto->id,
                        'canal_canal_id'                => $campania_canal->id,
                        'usuario_ingresa_id'            => $campania->usuario_ingresa_id,
                    ]);
                    
                    if(strtolower($campania_canal->canal->nombre)  == strtolower('SMS')){
                        
                        
                        $this->sendSms($client, $campania, $campania_contacto->contacto->celular);
                    }else if(strtolower($campania_canal->canal->nombre) == strtolower('Whatsapp')){
                        
                        $this->sendWhatsApp($client, $campania, $campania_contacto->contacto->celular);
                        
                    }

                    // echo json_encode($segui); 

                }
                    
                

            }
            // die();
            
            $campania = Campania::find($this->eventoCampania->campania_id);
            $campania->tipo = 'E';
            
            $campania->update();
            DB::commit();
            
            // return response()->json($campania, 200); 
        } catch (\Exception $e) {
            DB::rollback();
            // return response()->json(["message"=>$e->getMessage()]); 
        }
    }

    private function sendSms($client, $campania, $celular){
       
            $arreglo_body = [
                'from' => env('TWILIO_FROM_SMS'),
                'body' => $campania->mensaje,
                'statusCallback' => "https://22e36b6c653c.ngrok.io/api/campania/recieve-sms"
            ];
            echo json_encode($campania); die();
            if($campania->mediar_url){
                $arreglo_body['mediaUrl'] = $campania->media_url;
            }
            
            $client->messages->create(
                $celular,
                $arreglo_body
            );
        // }
    }
    

    private function sendWhatsApp($client, $campania, $celular){
            $arreglo_body = [
                "from" => 'whatsapp:' . env('TWILIO_FROM_WHATSAPP'),
                    // the body of the text message you'd like to send
                'body' => $campania->mensaje,
                'statusCallback' => "https://22e36b6c653c.ngrok.io/api/campania/recieve-sms"
            ];
            
            if($campania->mediar_url){
                $arreglo_body['mediaUrl'] = $campania->media_url;
            }

            // echo json_encode($arreglo_body); die();

            $client->messages->create('whatsapp:' . $celular, 
                $arreglo_body
         
            );
       
    }
}