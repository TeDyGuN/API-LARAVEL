<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\JwtAuth;
use App\Car;
use App\User;
class CarController extends Controller
{
  private $jwt;
  public function __construct()
  {
    // $this->middleware('cors');
    //$this->middleware('jwt.auth');
    $this->middleware('jwt.auth');
    $this->jwt = new JwtAuth();
  }
  public function index(){
    $cars = Car::all()->load('user');
    return response()->json(array(
        'cars' => $cars,
        'status' => 'success'
    ), 200);
  }
  public function show($id){
    $car = Car::find($id);
    if(is_object($car)){
      $car = Car::find($id)->load('user');
      return response()->json(array(
          'car' => $car,
          'status' => 'success'
      ), 200);
    }
    return response()->json(array(
        'status' => 'error',
        'message' => 'El coche no existe'
    ), 200);
  }

  public function update(Request $request, $id){

      $user = $this->jwt->user($request->header('Authorization'));
    $json = $request->input('json', null);
    $params = json_decode($json);
    $params_array = json_decode($json, true);

    var_dump($request);
    die();
    $validate = \Validator::make($params_array, [
      'title' => 'required|min:5',
      'description' => 'required',
      'price' => 'required',
      'status' => 'required'
    ]);
    if($validate->fails()){
      return response()->json($validate->errors(), 400);
    }
    unset($params_array['id']);
    unset($params_array['user_id']);
    unset($params_array['created_at']);
    unset($params_array['user']);
    $car = Car::where('id', $id)->update($params_array);

    $data = array(
      'car' => $params,
      'status' => 'success',
      'code' => 200
    );
    return response()->json($data, 200);
  }
  public function destroy($id){
      $car = Car::find($id);
      $car->delete();
      $data = array(
        'car' => $car,
        'status' => 'success',
        'code' => 200
      );
      return response()->json($data, 200);

  }

  //Creacion
  public function store(Request $request){
    $user = $this->jwt->user($request->header('Authorization'));

    $json = $request->input('json', null);
    $params = json_decode($json);
    $params_array = json_decode($json, true);

    $validate = \Validator::make($params_array, [
      'title' => 'required|min:5',
      'description' => 'required',
      'price' => 'required',
      'status' => 'required'
    ]);
    if($validate->fails()){
      $data = array(
        'status' => 'error',
        'message' => $validate->errors()
      );
      return response()->json($data, 200);
    }

    $car = new Car();
    $car->id_user = $user->sub;
    $car->title = $params->title;
    $car->description = $params->description;
    $car->price = $params->price;
    $car->status = $params->status;
    $car->save();

    $data = array(
      'car' => $car,
      'message' => 'Automovil Creado Correctamente',
      'status' => 'success',
      'code' => 200
    );
    return response()->json($data, 200);
  }
}
