<?php
namespace App\Helpers;

use Firebase\JWT\JWT;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\User;

class JwtAuth{
	public $key;
	public $hash;

	public function __construct(){
		$this->key = 'esta-es-mi-clave-secreta-3155326236??!!';
		$this->hash = null;
	}

	public function signup($email, $password, $getToken=null){

		// Comprobando si el usuario existe con los parametros dados
		$user = User::where(
					array(
						'email'	=> $email,
						'password' => $password
					)
				)->first();

		// Si no existe, devolvemos error
		if(!is_object($user))
			return array('status' => 'error', 'message' => 'Login ha fallado');

		// Generar el token y devolverlo
		$token = array(
			'sub'	=>	$user->id,
			'email'	=>	$user->email,
			'name'	=>	$user->name,
			'surname'	=>	$user->surname,
			'iat'	=>	time(),
			'exp'	=>	time() + (7*24*60*60),
		);

		// Cifrando el token con mi clave secreta
		$jwt = JWT::encode($token, $this->key, 'HS256');
		$decoded = JWT::decode($jwt, $this->key, array('HS256'));

		if(is_null($getToken))
			return $jwt;
		else
			return $decoded;
	}

	// Funcion que comprueba el token
	public function checkToken($jwt, $getIdentity = false){

		// Decodificar el token con la key establecida
		try{
			$decoded = JWT::decode($jwt, $this->key, array('HS256'));
		}catch(\UnexpectedValueException $e){
			$auth = false;
		}catch(\DomainException $e){
			$auth = false;
		}

		// Comprobación para ver si se ha descodificado o no
		if(isset($decoded) && is_object($decoded) && isset($decoded->sub))
			$auth = true;
		else
			$auth = false;
		
		// Flag identity que llega de parametro
		if($getIdentity)
			return $decoded;

		// Devolver resultado
		return $auth;
	}

	// Funcion que comprueba si la Request va validada o no
	public function checkIfRequestValidated(Request $request){

		$this->hash = $request->header('Authorization', null);
    	return $this->checkToken($this->hash);

	}
	
}
