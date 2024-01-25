<?php

namespace App\Controllers;

use App\Helpers\Response;
use App\Models\CategoriaProductoModel;
use CodeIgniter\RESTful\ResourceController;

class CategoriaProductoController extends ResourceController
{

	// Controlador para obtener todas las categorias de productos activas
	public function index()
	{
		try {
			$categoriaProductoModel = new CategoriaProductoModel();
			$data = $categoriaProductoModel->categoriasActivas();
			return Response::SuccessRequest($data, $this->response);
		} catch (\Exception $e) {
			return Response::BadRequest($e, $this->response);
		}
	}

	// Controlador para consultar una categoria de prodcuto por id
	public function show($id = null)
	{
		try {
			$categoriaProductoModel = new CategoriaProductoModel();
			$categoria = $categoriaProductoModel->categoriaProductoByID($id);

			if ($categoria === null) {
				throw new \Exception('No se encontró la categoría para el ID: $id', 404);
			}

			return Response::SuccessRequest($categoria, $this->response);
		} catch (\Exception $e) {
			return Response::BadRequest($e, $this->response);
		}
	}


	// Controlador para crear una nueva categoria de producto
	public function create()
	{
		try {
			$body = $this->request->getJSON();
			$nombre = $body->nombre;
			$descripcion = $body->descripcion;

			$categoriaProductoModel = new CategoriaProductoModel();
			$validationResult = $categoriaProductoModel->validate(['nombre' => $nombre, 'descripcion' => $descripcion]);

			if (!$validationResult) {
				return Response::FieldsError($categoriaProductoModel->errors(), $this->response);
			}

			$nuevoId = $categoriaProductoModel->crearCategoria($nombre, $descripcion);
			return Response::SuccessRequest($nuevoId, $this->response, 201);
		} catch (\Exception $e) {
			return Response::BadRequest($e, $this->response);
		}
	}

	// Controlador para acualizar una categoria de producto
	public function update($id = null)
	{
		try {
			$categoriaProductoModel = new CategoriaProductoModel();

			$body = $this->request->getJSON();
			$nombre = $body->nombre;
			$descripcion = $body->descripcion;
			$estatus = $body->estatus;

			$categoria = $categoriaProductoModel->categoriaProductoByID($id);

			if ($categoria === null) {
				throw new \Exception('No se encontró la categoría para el ID: $id', 401);
			}

			$validationResult = $categoriaProductoModel->validate(['nombre' => $nombre, 'descripcion' => $descripcion]);

			if (!$validationResult) {
				return Response::FieldsError($categoriaProductoModel->errors(), $this->response);
			}

			$categoriaProductoModel->actualizarCategoria($nombre, $descripcion, $id, $estatus);
			return Response::SuccessRequest(['update' => true], $this->response, 200);
		} catch (\Exception $e) {
			return Response::BadRequest($e, $this->response);
		}
	}

	//Controlador para eliminar una categoria de producto
	public function destroy($id = null)
	{
		try {
			$categoriaProductoModel = new CategoriaProductoModel();
			$categoria = $categoriaProductoModel->categoriaProductoByID($id);

			if ($categoria === null) {
				throw new \Exception('No se encontró la categoría para el ID: $id', 401);
			}

			$categoriaProductoModel->eliminarCategoria($id);
			return Response::SuccessRequest(['deleted' => true], $this->response, 200);
		} catch (\Exception $e) {
			return Response::BadRequest($e, $this->response);
		}
	}

}

