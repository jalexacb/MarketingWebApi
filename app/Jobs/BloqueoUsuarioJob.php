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
// use App\Models\EventoCampania;

use App\Models\Usuario;
use App\Models\ConfigUsuario;
use App\Models\Parametro;
// use App\Models\SeguimientoCampania;
// use App\Models\SeguimientoCampaniaDetalle;
// use App\Jobs\EjecutarCampaniaJob;

use Illuminate\Support\Facades\DB;

use Illuminate\Support\Carbon;

class BloqueoUsuarioJob extends Job implements ShouldQueue
{
    use SerializesModels;

    protected $configUsuario;

    /**
     * Create a new job instance.
     *
     * @param  User  $user
     * @return void
     */
    public function __construct(ConfigUsuario $configUsuario)
    {
        $this->configUsuario = $configUsuario;
    }

    /**
     * Execute the job.
     *
     * @param  Mailer  $mailer
     * @return void
     */
    public function handle()
    {
       
        // $usuario = Usuario::where('status','A')->where('id', $id)->first();
        $configUsuario = ConfigUsuario::where('id',$this->configUsuario->id);
        
        // $configUsuario->intento_login = 0;
        // $configUsuario->fecha_bloqueado = null;
        // $configUsuario->update([
        //     'intento_login' => 0,
        //     'fecha_bloqueado'   => null
        // ]);
        $configUsuario->update([
            'intento_login' => 0,
            'fecha_bloqueado' => null
        ]);
        // echo json_encode($configUsuario); die();
        return response()->json($configUsuario, 200);
    }
}