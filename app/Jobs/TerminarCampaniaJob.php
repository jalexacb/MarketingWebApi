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

use Illuminate\Support\Facades\DB;

class TerminarCampaniaJob extends Job implements ShouldQueue
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
            
            $eventoCampania = EventoCampania::find($this->eventoCampania->id);
            $eventoCampania->status = "I";
            
            $eventoCampania->update();
            // EventoCampania::where('id',$this->$eventoCampania->id)->update(['status'=>'I']);
            // $evento->status = 'I';
            // $evento->update();
            // echo json_encode($evento); die();
            // Campania::where('id',$this->eventoCampania->campania_id)->update(['tipo'=>'A']);

            $campania = Campania::find($this->eventoCampania->campania_id);
            $campania->tipo = "A";
            
            $campania->update();
            // $campania->tipo = 'A';
            
            // $campania->update();
           
            DB::commit();
            
            // return response()->json($campania, 200); 
        } catch (\Exception $e) {
            DB::rollback();
            // return response()->json(["message"=>$e->getMessage()]); 
        }
    }
   
}