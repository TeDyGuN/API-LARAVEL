<?php

namespace App\Helpers;

use Illuminate\Http\Request;
use App\User;
use Firebase\JWT\JWT;

class JwtAuth
{
  private $key;

  public function __construct()
  {
    $this->key = 'ASOPOTAMADRE2504XDXDXDXDXD:v';
  }
  public function signup($email, $password, $getToken = null){
      $user = User::where(
        array(
            'email'     => $email,
            'password'  => $password
        ))->first();

        $signup = false;
        if(is_object($user)){
          $signup = true;
        }

        if($signup){
          //Generar el Token y devolverlo
          $token = array(
              'sub'     => $user->id,
              'email'   => $user->email,
              'name'    => $user->name,
              'surname' => $user->surname,
              'iat'     => time(),
              'exp'     => time() + (7 * 24 * 60 * 60)
          );

          $jwt = JWT::encode($token, $this->key, 'HS256');
          $decoded = JWT::decode($jwt, $this->key, array('HS256'));
          if(is_null($getToken)){
            return $jwt;
          }
          else{
            return $decoded;
          }
        }
        else {
          return array('status' => 'error',
                       'message' => 'Login Fallido');
        }

  }

  public function checkToken($jwt, $getIdentity = false){
    $auth = false;
    try {
      $decoded = JWT::decode($jwt, $this->key, array('HS256'));
    } catch (\UnexpectedValueException $e) {
      $auth = false;
    } catch (\DomainException $e){
      $auth = false;
    }

    if(isset($decoded) && is_object($decoded) && isset($decoded->sub)){
      $auth = true;
    }
    else {
      $auth = false;
    }

    if($getIdentity){
      return $decoded;
    }
    return $auth;

  }
  public function user($token){
    $decoded = JWT::decode($token, $this->key, array('HS256'));
    return $decoded;
  }
}
