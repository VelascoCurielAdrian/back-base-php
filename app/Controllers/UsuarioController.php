<?php

namespace App\Controllers;

use App\Helpers\FunctionJwt;
use App\Helpers\Response;
use App\Models\UsuarioModel;
use CodeIgniter\RESTful\ResourceController;

class UsuarioController extends ResourceController
{


	// Controlador para registrar un usuario
	public function register()
	{
		try {
			$body = $this->request->getJSON();
			$nombre_completo = $body->nombre_completo;
			$usuario = $body->usuario;
			$password = $body->password;

			$registroModel = new UsuarioModel();
			$validationResult = $registroModel->validate(['nombre_completo' => $nombre_completo, 'usuario' => $usuario, 'password' => $password]);

			if (!$validationResult) {
				return Response::FieldsError($registroModel->errors(), $this->response);
			}

			$usuarioBD = $registroModel->consultarUsuario($usuario);

			if ($usuarioBD) {
				throw new \Exception('El usuario ya existe', 409);
			}

			$nuevoId = $registroModel->registroUsuario(
				$nombre_completo,
				$usuario,
				password_hash($password, PASSWORD_DEFAULT)
			);

			return Response::SuccessRequest($nuevoId, $this->response, 201);
		} catch (\Exception $e) {
			return Response::BadRequest($e, $this->response);
		}
	}

	// Controlador par loguear un usuario
	public function login()
	{
		try {
			$body = $this->request->getJSON();
			$usuario = $body->usuario;
			$password = $body->password;

			$loginModel = new UsuarioModel();
			$validationResult = $loginModel->validate(['usuario' => $usuario, 'password' => $password]);

			if (!$validationResult) {
				return Response::FieldsError($loginModel->errors(), $this->response);
			}

			$usuarioBD = $loginModel->consultarUsuario($usuario);

			if (!$usuarioBD) {
				throw new \Exception('El usuario no existe', 401);
			}

			$verifcar_pasword = password_verify($password, $usuarioBD['password']);

			if (!$verifcar_pasword) {
				throw new \Exception('Contraseña incorrecta', 401);
			}

			$token = FunctionJwt::generate($usuarioBD);

			$data = [
				'usuarioBD' => [
					'usuario' => $usuarioBD['usuario'],
					'nombre_completo' => $usuarioBD['nombre_completo'],
				],
				'token' => $token
			];

			return Response::SuccessRequest($data, $this->response);
		} catch (\Exception $e) {
			return Response::BadRequest($e, $this->response);
		}
	}
}
