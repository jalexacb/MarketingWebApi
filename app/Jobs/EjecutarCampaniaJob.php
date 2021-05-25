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
use App\Models\Parametro;
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

            // echo json_encode($eventoCampania); die();
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
                    
                    $message = null;
                    if(strtolower($campania_canal->canal->nombre)  == strtolower('SMS')){
                        
                        
                        $message = $this->sendSms($client, $campania, $campania_contacto->contacto->celular);
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

                    // echo json_encode($segui); 

                }
                    
                

            }
            // die();
            
            $campania = Campania::find($this->eventoCampania->campania_id);
            $campania->tipo = "E";
            
            $campania->update();
            DB::commit();
            
            // return response()->json($campania, 200); 
        } catch (\Exception $e) {
            DB::rollback();
            // return response()->json(["message"=>$e->getMessage()]); 
        }
    }

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

    // private function sendSms($client, $campania, $celular){
    //     $mensajeDefecto = " ¿Le pareció interesante la campaña? Responda sí o no. ¡Muchas gracias!";
    //     // $body = $campania->mensaje . " " . $campania->url . " " .$mensajeDefecto;
    //     $celularTwilio = Parametro::find(3);
        
    //     if($campania->url_media){
    //         $arreglo_body = [
    //             'from' => $celularTwilio->valor,
    //             'body' => ($campania->mensaje. ' '. $campania->url.''.$mensajeDefecto),
    //             'statusCallback' => "https://4a111335c34a.ngrok.io/api/campania/recieve-sms",
    //             'mediaUrl' => "https://4a111335c34a.ngrok.io/api/campania/get-image?imagen=".$campania->url_media,
    //         ];
    //         // $arreglo_body['mediaUrl'] = "https://www.google.com/imgres?imgurl=https%3A%2F%2Fkchcomunicacion.com%2Fwp-content%2Fuploads%2F2020%2F06%2Fgoogle-trends-1280x720-1.png&imgrefurl=https%3A%2F%2Fkchcomunicacion.com%2Fgoogle-comenzara-a-pagar-a-los-medios-por-su-contenido-de-noticias%2F&tbnid=0ARXCGj8bbiDSM&vet=12ahUKEwiF0IbB9JTvAhVos1kKHcWrBy0QMygDegUIARDKAQ..i&docid=7_28lP7QSyTnnM&w=1280&h=720&q=imagenes%20google&client=firefox-b-d&ved=2ahUKEwiF0IbB9JTvAhVos1kKHcWrBy0QMygDegUIARDKAQ";
    //         // echo json_encode($campania->url_media); die();
    //     }else{
    //         $arreglo_body = [
    //             'from' => $celularTwilio->valor,
    //             'body' => ($campania->mensaje. ' '. $campania->url.''.$mensajeDefecto),
    //             'statusCallback' => "https://4a111335c34a.ngrok.io/api/campania/recieve-sms"
    //         ]; 
    //     }
    //     return $client->messages->create(
    //         $celular,
    //         $arreglo_body
    //     );
    // }

    // private function sendWhatsAppInicio($client, $campania, $contacto){
    //     // $mensajeDefecto = " ¿Le pareció interesante la campaña? Responda sí o no. ¡Muchas gracias!";
    //     // $celularTwilio = Parametro::find(3);
    //     $nombres = explode(" ", $contacto->nombres);
    //     $nombre = "";
    //     if(sizeof($nombres)> 0){
    //         $nombre = $nombres[0];
    //     }
    //     $arreglo_body = [
    //         "from" => 'whatsapp:' . env('TWILIO_FROM_WHATSAPP'),
    //         'body' => "Hola, ".$nombre.", si quiere recibir información, responda este mensaje con un ok. Gracias",
    //         // 'statusCallback' => "https://4a111335c34a.ngrok.io/api/campania/recieve-sms"
    //     ];
    //     return $client->messages->create('whatsapp:' . $contacto->celular, 
    //         $arreglo_body
    //     );
    // }
    

    // private function sendWhatsApp($client, $campania, $celular){
    //     $mensajeDefecto = " ¿Le pareció interesante la campaña? Responda sí o no. ¡Muchas gracias!";
        

    //     if($campania->url_media){
    //         $arreglo_body = [
    //             "from" => 'whatsapp:' . env('TWILIO_FROM_WHATSAPP'),
    //             'body' => $campania->mensaje. ' '. $campania->url.$mensajeDefecto,
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
}