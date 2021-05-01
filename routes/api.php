<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Models\Person;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('save', function(Request $request){
    $person = Person::create([
        "e-mail" => $request->email,
        "password" => Hash::make($request->password)]
    );
    $response = ['e-mail'=> $request->email];
    return $response;
});
Route::post('flight/auth', function(Request $request){
    $person = Person::where('e-mail', $request->email)->first();
    if($person){
        if(Hash::check($request->password, $person->password)){
            $token = Str::random(60);
            $person->token=$token;
            $person->save();
            $response = ['token' => $token];
            return $response;
        }
    }
    return 401;
});
Route::post('flight/search', function(Request $request){
        return DB::table('flights')
                            ->select('flight_number', 'departure_airport' , 'arrival_airport' , 'departure_date')
                            ->where('departure_airport', '=' , $request->departure_airport)
                            ->where( 'arrival_airport', '=', $request->arrival_airport)
                            ->where( 'departure_date', '>=', $request->departure_date)
                            ->where( 'available_seat', '>=' , $request->passenger_number)
                            ->get();

    });

Route::post('flight/book', function(Request $request){
    DB::table('bookings')->insert([
        'flight_number' =>$request->flight_number,
        'passenger_name'=>$request->passenger_name,
        'passenger_birth'=>$request->passenger_birth
    ]);
    $last_id=DB::getPdo()->lastInsertId();
    $created_time=Carbon\Carbon::now();
    $array=array();
    $array['booking_id']=$last_id;
    $array['created_time']=$created_time;
    $json=json_encode($array);
    return $json;
});