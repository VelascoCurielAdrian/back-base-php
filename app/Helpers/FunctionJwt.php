<?php
namespace App\Helpers;

use Firebase\JWT\JWT;

class FunctionJwt
{

  //Función que se encarga de generar un token
  public static function generate($payload)
  {
    $key = getenv('JWT_SECRET');
    $iat = time();
    $exp = $iat + 3600;

    $payload = array(
      "iss" => "Issuer of the JWT",
      "aud" => "Audience that the JWT",
      "sub" => "Subject of the JWT",
      "iat" => $iat,
      "exp" => $exp,
      "payload" => $payload
    );

    $token = JWT::encode($payload, $key, 'HS256');

    return $token;
  }
}
