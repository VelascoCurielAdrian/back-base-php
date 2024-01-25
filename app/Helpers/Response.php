<?php
namespace App\Helpers;

use CodeIgniter\API\ResponseTrait;

class Response
{
  use ResponseTrait;

  public static function BadRequest(\Exception $e, $response)
  {

    $errorMessage = $e->getMessage();
    $statusCode = $e->getCode();

    if ($statusCode !== null && is_numeric($statusCode) && $statusCode >= 100 && $statusCode < 600) {
    } else {
      $statusCode = 501;
    }

    return $response
      ->setStatusCode($statusCode)
      ->setJSON([
        "message" => $errorMessage,
        "status" => $statusCode,
        "title" => 'Error'
      ]);
  }

  public static function FieldsError($errors, $response)
  {
    return $response
      ->setStatusCode(400)
      ->setJSON([
        'fields' => $errors,
        "status" => 400,
        "title" => 'error'
      ]);
  }
  public static function SuccessRequest($info, $response, $statusCode = 200)
  {
    return $response
      ->setStatusCode($statusCode)
      ->setJSON([
        "status" => $statusCode,
        "title" => 'success',
        "message" => SUCCESS,
        "data" => $info,
      ]);
  }
}
