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
				return $this->respond(Response::error(400, $registroModel->errors()));
			}

			$usuarioBD = $registroModel->consultarUsuario($usuario);

			if ($usuarioBD) {
				return $this->response
					->setStatusCode(409)
					->setJSON(Response::error(409, 'El usuario ya existe'));
			}

			$nuevoId = $registroModel->registroUsuario(
				$nombre_completo,
				$usuario,
				password_hash($password, PASSWORD_DEFAULT)
			);

			return $this->response->setStatusCode(201)->setJSON(['data' => $nuevoId, 'message' => SUCCESS]);
		} catch (\Exception $e) {
			$errorMessage = $e->getMessage();
			return $this->response
				->setStatusCode(501)
				->setJSON(Response::error(501, $errorMessage));
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
				return $this->respond(Response::error(400, $loginModel->errors()));
			}

			$usuarioBD = $loginModel->consultarUsuario($usuario);

			if (!$usuarioBD) {
				return $this->response
					->setStatusCode(4041)
					->setJSON(Response::error(401, 'El usuario no existe'));
			}

			$verifcar_pasword = password_verify($password, $usuarioBD['password']);

			if (!$verifcar_pasword) {
				return $this->response
					->setStatusCode(401)
					->setJSON(Response::error(401, 'Contraseña incorrecta'));
			}

			$token = FunctionJwt::generate($usuarioBD);

			$response = [
				'usuarioBD' => [
					'usuario' => $usuarioBD['usuario'],
					'nombre_completo' => $usuarioBD['nombre_completo'],
				],
				'token' => $token
			];

			return $this->response->setStatusCode(200)->setJSON([$response, 'message' => SUCCESS]);
		} catch (\Exception $e) {
			$errorMessage = $e->getMessage();
			return $this->response
				->setStatusCode(501)
				->setJSON(Response::error(501, $errorMessage));
		}
	}
}
