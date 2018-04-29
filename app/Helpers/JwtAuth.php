<?php
namespace App\Helpers;

use Firebase\JWT\JWT;
use Illuminate\Support\Facades\DB;
use App\User;

class JwtAuth{
	public $key;

	public function __construct(){
		$this->key = 'esta-es-mi-clave-secreta-3155326236??!!';
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

	public function checkToken($jwt, $getIdentity = false){

		try{
			$decoded = JWT::decode($jwt, $this->key, array('HS256'));
		}catch(\UnexpectedValueException $e){
			$auth = false;
		}catch(\DomainException $e){
			$auth = false;
		}

		if(is_object($decoded) && isset($decoded->sub)){
			$auth = true;
		}else{
			$auth = false;
		}

		if($getIdentity){
			return $decoded;
		}

		return $auth;
	}
}
