<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Contacto;
use App\Models\RecomendacionBasadaItem;

class ContactoController extends Controller {

    public function index() {
        try {
            //code...
            $contactos = Contacto::whereIn('status',['A','I']);
            if(isset($_GET['busqueda'])){
                $busqueda = $_GET['busqueda'];
                $contactos->where(function ($query) use ($busqueda) {
                    $query->orWhere('nombre', 'like', '%' . $busqueda . '%');
                        //   ->orWhere('rol.nombre','like', '%' . $busqueda . '%');
                });
            }
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

    public function delete($id){
        try {
            $contacto = Contacto::find($id);
            $contacto->status = 'I';
            $contacto->update();

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
        $contactos->with(['contacto_intereses' => function ($q){
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
            foreach ($contacto->contacto_intereses as $key => $contacto_interes) {
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
