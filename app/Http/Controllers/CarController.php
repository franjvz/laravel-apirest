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

	// Muestra todos los vehículos de la bd
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

    // Muestra info del vehículo con la id recibida
    public function show(Request $request, $id){

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
    		$params = json_decode($json);
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
    			'code' => 300
    		);
    	}

    	// Devolver el json
    	return response()->json($data, 200);
    }

    // Actualizar registro del vehículo con id
    public function update($id, Request $request){

    	$checkToken = $this->jwtAuth->checkIfRequestValidated($request);
    	if ($checkToken){
    		// Recoger datos post
    		$json = $request->input('json', null);
    		$params = json_decode($json);
    		$params_array = json_decode($json, true);

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

    		// Actualizar el coche
    		$car = Car::where('id', $id)
    				->update($params_array);

    		$data = array(
    			'car' => $params,
    			'status' => 'success',    		
    			'code' => 200
    		);		

    	}else{
    		// Devolver error
    		$data = array(
    			'message' => 'Login incorrecto',
    			'status' => 'error',
    			'code' => 300
    		);
    	}

    	// Devolver el json
    	return response()->json($data, 200);
    }

    // Eliminar un registro de la bd
    public function destroy($id, Request $request){

    	$checkToken = $this->jwtAuth->checkIfRequestValidated($request);
    	if ($checkToken){
    		$car = Car::find($id);
    		if(is_object($car)){
    			$car->delete();
    			$feedback = array(
		    		'message' 	 => 'Coche con el identificador '.$id.' eliminado del sistema',
		    		'status' => 'success',
		    		'code' => 200
		    	);
		 
    		}else{
    			$feedback = array(
		    		'message' 	 => 'No se encuentra el coche con el identificador '.$id,
		    		'status' => 'error',
		    		'code' => 300
		    	);
    		}

	    	
    	}else{
    		$feedback = array(
    			'status' => 'Unauthorized',
    			'message' => 'Sin acceso autorizado',
    			'code' => 401
    		);
    	}

    	return response()->json($feedback, $feedback['code']);
    }
}
