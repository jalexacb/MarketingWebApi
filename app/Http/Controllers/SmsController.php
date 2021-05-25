<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Twilio\Rest\Client;
use Twilio\Jwt\ClientToken;

class SmsController extends Controller
{
    public function sendSms(Request $request)
    {
        $sid    = env( 'TWILIO_ACCOUNT_SID' );
        $token  = env( 'TWILIO_AUTH_TOKEN' );
        $client = new Client( $sid, $token );
        // echo json_encode($client); die();
        $input = $request->all();

        // echo json_encode($input); die();
        // $appSid     = config('app.twilio')['TWILIO_APP_SID'];
        // $client = new Client($accountSid, $authToken);
        try
        {
            // Use the client to do fun stuff like send text messages!
            $client->messages->create(
            // the number you'd like to send the message to
                $input['to'],
           array(
                 // A Twilio phone number you purchased at twilio.com/console
                 "mediaUrl" => ["https://images.unsplash.com/photo-1545093149-618ce3bcf49d?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=668&q=80"],
                 'from' => env('TWILIO_FROM_SMS'),
                 // the body of the text message you'd like to send
                 'body' => $input['body'],
                 'statusCallback' => "https://22e36b6c653c.ngrok.io/api/campania/recieve-sms"
                )
            );

            $this->sendWhatsApp($input);
        }
        catch (Exception $e)
        {
            echo "Error: " . $e->getMessage();
        }
    }

    public function sendBulkSms( Request $request )
    {
       // Your Account SID and Auth Token from twilio.com/console
       $sid    = env( 'TWILIO_SID' );
       $token  = env( 'TWILIO_TOKEN' );
       $client = new Client( $sid, $token );

       $validator = Validator::make($request->all(), [
           'numbers' => 'required',
           'message' => 'required'
       ]);

       if ( $validator->passes() ) {

           $numbers_in_arrays = explode( ',' , $request->input( 'numbers' ) );

           $message = $request->input( 'message' );
           $count = 0;

           foreach( $numbers_in_arrays as $number )
           {
               $count++;

               $client->messages->create(
                   $number,
                   [
                       'from' => env( 'TWILIO_FROM' ),
                       'body' => $message,
                       'statusCallback' => "https://22e36b6c653c.ngrok.io/api/campania/recieve-sms"
                   ]
               );
           }

           return back()->with( 'success', $count . " messages sent!" );
              
       } else {
           return back()->withErrors( $validator );
       }
   }
//    string $message, string $recipient
   public function sendWhatsApp($input)
   {
    //    $twilio_whatsapp_number = env('TWILIO_WHATSAPP_NUMBER');
       $sid    = env( 'TWILIO_ACCOUNT_SID' );
        $token  = env( 'TWILIO_AUTH_TOKEN' );

       $client = new Client($sid, $token);
    //    return $client->messages->create($recipient, array('from' => "whatsapp:$twilio_whatsapp_number", 'body' => $message));
    return $client->messages->create('whatsapp:' . $input['to'], [
        "mediaUrl" => ["https://images.unsplash.com/photo-1545093149-618ce3bcf49d?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=668&q=80"],
        "from" => 'whatsapp:' . env('TWILIO_FROM_WHATSAPP'),
        'body' => $input['body'],
        'statusCallback' => "https://22e36b6c653c.ngrok.io/api/campania/recieve-sms"
    ]);
   }

   public function recieveStatusCallback(Request $request){
        $sid = $_REQUEST['MessageSid'];
        $status = $_REQUEST['MessageStatus'];
        // $status = "recibido";
        event(new \App\Events\CampaniaCreadaEvent($status));
        $out = new \Symfony\Component\Console\Output\ConsoleOutput();
        $out->writeln("SID: $sid, Status: $status");
        // event(new \App\Events\CampaniaCreadaEvent($status));
   }

   public function receiveSMS(Request $request)
   {
       $messageBody = $request->input('Body');
       $phoneNumber = $request->input('From');

       // do something with the message
   }
}