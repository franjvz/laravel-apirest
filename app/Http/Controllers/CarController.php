<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Helpers\JwtAuth;
use App\Car;

class CarController extends Controller
{
	public $jwtAuth;

	public function __construct(){
		$this->jwtAuth = new JwtAuth();
	}

    public function index(Request $request){

    	$checkToken = $this->jwtAuth->checkIfRequestValidated($request);
    	if ($checkToken){

    		return response()->json(array(
    			'cars' => Car::all()->load('user'),
    			'status' => 'success'
    		),200);
    	}else{

    		return response()->json(array(
    			'status' => 'Unauthorized',
    			'message' => 'No se pueden listar los vehículos'
    		),401);
    	}
    }

    public function show($id, Request $request){

    	$checkToken = $this->jwtAuth->checkIfRequestValidated($request);
    	if ($checkToken){
    		$car = Car::find($id)->load('user');
	    	return response()->json(array(
	    		'car' 	 => $car,
	    		'status' => 'success'
	    	),200);
    	}else{

    		return response()->json(array(
    			'status' => 'Unauthorized',
    			'message' => 'Sin acceso autorizado'
    		),401);
    	}
    }

    // Dar de alta un nuevo vehículo
    public function store(Request $request){

    	$checkToken = $this->jwtAuth->checkIfRequestValidated($request);
    	if ($checkToken){
    		// Recoger datos post
    		$json = $request->input('json', null);
    		$params_array = json_decode($json, true);

    		// Obtener usuario identificado
    		$user = $this->jwtAuth->checkToken($this->jwtAuth->hash, true);

    		// Validar datos parametros recibidos
			$validate = 
				\Validator::make($params_array, [
		    		"title" => "required|min:5",
		    		"description" => "required",
		    		"status" => "required",
		    		"price" => "required"
		    	]);

			if($validate->fails()){
				return response()->json($validate->errors(), 400);
			}

  			// Guardar el coche
  			$params = json_decode($json);
    		$car = new Car();

    		$car->user_id = $user->sub;
    		$car->title = $params->title;
    		$car->description = $params->description;
    		$car->status = $params->status;
    		$car->price = $params->price;

    		$car->save();

    		$data = array(
    			'car' => $car,
    			'status' => 'success',
    			'code' => 200
    		);

    	}else{
    		// Devolver error
    		$data = array(
    			'message' => 'Login incorrecto',
    			'status' => 'error',
    			'code' => 400
    		);
    	}

    	// Devolver el json
    	return response()->json($data, 200);
    }
}
