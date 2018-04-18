<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use Carbon\Carbon;
use Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\View;
use App\Helpers\JwtAuth;


class UserController extends Controller
{
    public function register(Request $request){

      $json = $request->json;
      $params = json_decode($json);

      $email = (!is_null($json) && isset($params->email)) ? $params->email : null;
      $name = (!is_null($json) && isset($params->name)) ? $params->name : null;
      $surname = (!is_null($json) && isset($params->surname)) ? $params->surname : null;
      $role = 'USER';
      $password = (!is_null($json) && isset($params->password)) ? $params->password : null;

      if(!is_null($email) && !is_null($name) && !is_null($surname)  && !is_null($password))
      {
        $user = new User();
        $user->email = $email;
        $user->name = $name;
        $user->surname = $surname;
        $user->role = $role;
        $pwd = hash('sha256', $password);
        $user->password = $pwd;

        $isset_user = User::where('email', '=', $email)->get();
        if(count($isset_user) == 0){
          $user->save();
          $data = array(
            'status' => 'success',
            'code'  => 200,
            'message' => 'Usuario Creado'
          );
        }
        else{
          $data = array(
            'status' => 'error',
            'code'  => 400,
            'message' => 'Usuario Duplicado'
          );
        }

      }
      else {
        $data = array(
          'status' => 'error',
          'code'  => 400,
          'message' => $json
        );
      }
      return response()->json($data, 200);
    }
    public function login(Request $request){

      $jwt = new JwtAuth();

      //RECIBIR post
      $json = $request->input('json', null);
      $params = json_decode($json);

      $email = (!is_null($json) && isset($params->email)) ? $params->email : null;
      $password = (!is_null($json) && isset($params->password)) ? $params->password : null;
      $getToken = (!is_null($json) && isset($params->getToken)) ? $params->getToken : null;

      $pwd = hash('sha256', $password);

      if(!is_null($email) && !is_null($password) && ($getToken == null || $getToken == "false"))
      {
        $signup = $jwt->signup($email, $pwd);

      }
      elseif ($getToken != null) {
        //dd($getToken);
        $signup = $jwt->signup($email, $pwd, $getToken);

      }
      else {
        $signup = array(
          'status' => 'error',
          'message' => 'Envia tus datos por Post'
        );
      }

      return response()->json($signup, 200);

    }

}
