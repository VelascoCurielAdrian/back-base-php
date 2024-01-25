<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthFilter implements FilterInterface
{
  /**
   * Filtro que se encarga de verficar el token antes de acceder a la ruta
   * @param RequestInterface $request
   * @param array|null       $arguments
   *
   * @return RequestInterface|ResponseInterface|string|void
   */
  public function before(RequestInterface $request, $arguments = null)
  {
    $key = getenv('JWT_SECRET');
    $authHeader = apache_request_headers()['Authorization'];
    $authHeader = $authHeader->getValue();
    $token = $authHeader;

    if (is_null($token) || empty($token)) {

      $response = service('response');
      $response->setJSON([
        'status' => 'error',
        'message' => 'No se ha proporcionado un token.'
      ]);
      $response->setStatusCode(401);
      return $response;
    }

    try {
      // Funcion que se encarga de recibir el token y verificarlo
      JWT::decode($token, new Key($key, 'HS256'));
    } catch (\Exception $ex) {
      $response = service('response');
      $response->setJSON([
        'status' => 'error',
        'message' => 'Error al validar el token.',
        $ex->getMessage(),
      ]);
      $response->setStatusCode(401);
      return $response;
    }
  }

  /**
   * Allows After filters to inspect and modify the response
   * object as needed. This method does not allow any way
   * to stop execution of other after filters, short of
   * throwing an Exception or Error.
   *
   * @param RequestInterface  $request
   * @param ResponseInterface $response
   * @param array|null        $arguments
   *
   * @return ResponseInterface|void
   */
  public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
  {
    //
  }
}
