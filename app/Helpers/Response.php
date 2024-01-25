<?php
namespace App\Helpers;

use CodeIgniter\API\ResponseTrait;

class Response
{
  use ResponseTrait;

  public static function error($status, $errorMessage)
  {
    return [
      'status' => $status,
      'message' => $errorMessage,
      'title' => 'Error',
    ];
  }
}
