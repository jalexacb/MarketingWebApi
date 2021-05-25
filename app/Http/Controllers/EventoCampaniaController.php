<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\eventoCampania;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use App\Jobs\EjecutarCampaniaJob;
use App\Jobs\TerminarCampaniaJob;
use App\Models\Campania;
use App\Models\Registro;
class EventoCampaniaController extends Controller {

    public function index() {
        try {
            //code...
            // $roles = Rol::whereIn('status',['A','I']);

            $eventoCampanias = null;
            // Usuario::?:
            if(isset($_GET['status'])){
                // 
                $eventoCampanias = EventoCampania::where('status',$_GET['status']);
            }else{
                // echo json_encode($_GET['status']); die();
                $eventoCampanias = EventoCampania::whereIn('status',['A','I']);
            }
            if(isset($_GET['busqueda'])){
                $busqueda = $_GET['busqueda'];
                $eventoCampanias->whereHas('campania', function($q) use ($busqueda) {
                    $q->where('campania.status', 'A');
                    $q->where(function ($query) use ($busqueda) {
                        $query->orWhere('nombre', 'like', '%' . $busqueda . '%');
                            //   ->orWhere('rol.nombre','like', '%' . $busqueda . '%');
                    });
                    $q->orderBy('nombre');
                });
            }
            $eventoCampanias->with('campania');
            // $eventoCampanias->orderBy('nombre');
            if(isset($_GET['per_page'])) {
                $eventoCampanias = $eventoCampanias->paginate($_GET['per_page']);
            }else {
                $eventoCampanias = $eventoCampanias->get();
            }
            
           
          
            return response()->json($eventoCampanias, 200); 
        } catch (\Exception $e) {
            return response()->json(["message"=>$e->getMessage()]); 
        }
        
    }

    private function TerminarCampania($eventoCampania){
        EventoCampania::where('id',$eventoCampania->id)->update(['status'=>'I']);
        // $evento->status = 'I';
        // $evento->update();
        // echo json_encode($evento); die();
        Campania::where('id',$eventoCampania->campania_id)->update(['tipo'=>'A']);
    }

    public function getById($id){
        try {
            //code...
            $eventoCampania = EventoCampania::find($id);
            

          
            return response()->json($eventoCampania, 200); 
        } catch (\Exception $e) {
            //throw $th;
        }
    }

    public function delete($id){
        try {
            $eventoCampania = EventoCampania::find($id);
            if($eventoCampania->status == 'A')
                $eventoCampania->status = 'I';
            else if($eventoCampania->status == 'I')
                $eventoCampania->status = 'A';

            $eventoCampania->update();

            return response()->json($eventoCampania, 200); 
        } catch (\Throwable $th) {
            return response()->json(["message"=>$e->getMessage()]); 
        }
    }

    // public function store(Request $request){
    //     try {
    //         $input = $request->all();
    //         $eventoCampania = EventoCampania::create($input);
    //         $data = [
    //             'status'    => 'success',
    //             'code'      => 201,
    //             'message'   => 'eventoCampania creado correctamente.',  
    //             'usuario'   => $eventoCampania,   
    //         ];

    //         return response()->json($eventoCampania, 200); 
    //     } catch (\Exception $e) {
    //         return response()->json(["message"=>$e->getMessage()]); 
    //     }
    // }

    public function store(Request $request){
        try {
            DB::beginTransaction();
            $input = $request->all();
            // $input['fecha_inicio'] = new \DateTime($input['fecha_inicio'], new \DateTimeZone('America/Guayaquil'));
            // $input['fecha_inicio'] = $input['fecha_inicio']->format('Y-m-j H:i:s');
            // $input['fecha_fin'] = $input['fecha_fin']->format('Y-m-j H:i:s');

            // echo json_encode($input['fecha_inicio']); die();
            $input['status'] = 'A';
            $eventoCampania = EventoCampania::create($input);
            $tiempoInicioDelay = strtotime($eventoCampania->fecha_inicio) - strtotime(Carbon::now());
            $tiempoFinDelay = strtotime($eventoCampania->fecha_fin) - strtotime(Carbon::now());
            // echo json_encode($tiempoFinDelay); die();
            $this->realizarJob(new EjecutarCampaniaJob($eventoCampania), $tiempoInicioDelay);
            $this->realizarJob(new TerminarCampaniaJob($eventoCampania), $tiempoFinDelay);
            // $tiempoActual = 
            // echo json_encode($tiempoDelay); 
            // echo json_encode($tiempoActual); 
            // die();
            // $this->TerminarCampania($eventoCampania);
            Registro::create([
                'tipo'  => 'G',
                'nombre'   => 'Se ha creado una planificación de campaña.',
                'menu_id'       => '14',
                'usuario_ingresa_id'    => $eventoCampania->usuario_ingresa_id
            ]);
            DB::commit();
            return response()->json($eventoCampania, 200); 
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(["message"=>$e->getMessage()]); 
        }
        
    }
    private function realizarJob($campaniaJob, $tiempoDelay){
    
        // $job = ($campaniaJob);
        $date = Carbon::now()->addSeconds($tiempoDelay);
        \Queue::later($date,  $campaniaJob);
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

   
    public function eliminarJobEvento($id){
        // $job = \Queue::getPheanstalk()->useTube("evento_".$id)->peek($res);
        // //get the job from the que that you just pushed it to
        // $res = \Queue::getPheanstalk()->useTube("evento_".$id)->delete($job);
        $myqueue = 'add_something';
        \Queue::getRedis()->connection()->del('queues:'.$myqueue);
    }

    public function update(Request $request, $id){
        try {
            $input = $request->all();
            $eventoCampania = EventoCampania::find($id);
            $eventoCampania->nombre = $input['nombre'];
            $eventoCampania->update();

            return response()->json($eventoCampania, 200); 
        } catch (\Exception $e) {
            return response()->json(["message"=>$e->getMessage()]); 
        }
    }
    
}
