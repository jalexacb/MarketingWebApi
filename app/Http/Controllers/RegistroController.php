<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Registro;
// use App\Models\Registro;
class RegistroController extends Controller {

    public function index() {
        try {
            //code...
            // $registros = Rol::whereIn('status',['A','I']);

            $registros = null;
            // Usuario::?:
            if(isset($_GET['status'])){
                // 
                $registros = Registro::where('status',$_GET['status']);
            }else{
                // echo json_encode($_GET['status']); die();
                $registros = Registro::whereIn('status',['A','I']);
            }
            // if(isset($_GET['busqueda'])){
            //     $busqueda = $_GET['busqueda'];
            //     $registros->where(function ($query) use ($busqueda) {
            //         $query->orWhere('menu.nombre', 'like', '%' . $busqueda . '%');
            //             //   ->orWhere('Registro.nombre','like', '%' . $busqueda . '%');
            //     });     
            // }
            $registros->whereHas('menu', function($q){
                if(isset($_GET['busqueda'])){
                    $busqueda = $_GET['busqueda'];
                    $q->where(function ($query) use ($busqueda) {
                        $query->orWhere('menu.title', 'like', '%' . $busqueda . '%');
                            //   ->orWhere('Registro.nombre','like', '%' . $busqueda . '%');
                    });     
                }
            });
            $registros->whereHas('usuario', function($q){
                if(isset($_GET['busqueda'])){
                    $busqueda = $_GET['busqueda'];
                    $q->orWhere(function ($query) use ($busqueda) {
                        $query->orWhere('usuario.usuario', 'like', '%' . $busqueda . '%');
                            //   ->orWhere('Registro.nombre','like', '%' . $busqueda . '%');
                    });     
                }
            });
            
            $registros->with(['menu' => function ($q){
                if(isset($_GET['busqueda'])){
                    $busqueda = $_GET['busqueda'];
                    $q->where(function ($query) use ($busqueda) {
                        $query->orWhere('menu.title', 'like', '%' . $busqueda . '%');
                            //   ->orWhere('Registro.nombre','like', '%' . $busqueda . '%');
                    });     
                }
            }]);
            $registros->with(['usuario' => function ($q){
                if(isset($_GET['busqueda'])){
                    $busqueda = $_GET['busqueda'];
                    $q->orWhere(function ($query) use ($busqueda) {
                        $query->orWhere('usuario.usuario', 'like', '%' . $busqueda . '%');
                            //   ->orWhere('Registro.nombre','like', '%' . $busqueda . '%');
                    });     
                }
            }]);
            $registros->orderBy('registro.fecha_ingresa','DESC');
            // $registros->with('menu');
            // $registros->with('usuario');
            if(isset($_GET['per_page'])) {
                $registros = $registros->paginate($_GET['per_page']);
            }else {
                $registros = $registros->get();
            }
            

        //   echo json_encode($registros); die();
            return response()->json($registros, 200); 
        } catch (\Exception $e) {
            //throw $th;
        }
        
    }

   
    
}
