<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Status;
use App\Models\User;
//use App\Status;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;


class AuthController extends Controller
{
    public function register(Request $request)
    {

        $input = $request->all();
        $validator = Validator::make($input, [
            'email' => ['required', 'string', 'email', 'max:50', 'unique:users'],
            'password' => ['required', 'string', 'min:2','max:30'],
        ]);

        if($validator ->fails()){
            $failed = $validator->messages();
            return response([$failed, 'status' => 'fail',],200);
        }
        else
        {
            $token=random_int( 100, 99999999999 ) ;
            $user = new User;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->save();

            Status::create([
                'user_id' => $user['id'],
                'status'=>'0'
            ]);

            $client = DB::table('oauth_clients')
                ->where('password_client', true)
                ->first();
            if (!$client) {
                return response()->json([
                    'message' => 'Laravel Passport is not setup properly.',
                    'status' => 500
                ], 500);
            }
            $data = [
                'grant_type' => 'password',
                'client_id' => $client->id,
                'client_secret' => $client->secret,
                'username' => request('email'),
                'password' => request('password'),
            ];

            $request = Request::create('/oauth/token', 'POST', $data);

            $response = app()->handle($request);
            // Проверяем был ли внутренний запрос успешным
            if ($response->getStatusCode() != 200) {
                return response()->json([
                    'message' => 'Не верный логин или пароль',
                    'status' => 422
                ], 422);
            }
            // Вытаскиваем данные из ответа
            $data = json_decode($response->getContent());
            // Формируем окончательный ответ в нужном формате
            return response()->json([
                'token' => $data->access_token,
                'user' => $user,
                'status' => 'success'
            ]);
        }

    }

    public function login(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'email' => ['required', 'string', 'email', 'max:50'],
            'password' => ['required', 'string', 'min:2','max:30'],
        ]);
        if($validator ->fails()){
            $failed = $validator->messages();
            return response([$failed, 'status' => 'fail',],200);
        }

        $email= request('email');
        $user = User::where('email','=',$email)->first();

        if (!$user) {
            return response()->json([
                'message' => 'Не верный логин или пароль',
                'status' => 422
            ], 422);
        }

        // Если пользователь с таким email адресом найден - проверим совпадает
        // ли его пароль с указанным
        if (!Hash::check(request('password'), $user->password)) {
            return response()->json([
                'message' => 'Не верный логин или пароль',
                'status' => 422
            ], 422);
        }

//        $user_valid = Status::where('user_id', '=',  $user['id'])->first();
//        if($user_valid['banned']==1) {
//            return response()->json([
//                'message' => 'Пользователь забанен',
//                'status' => 422
//            ], 422);
//        }

        // Внутренний API запрос для получения токенов
        $client = DB::table('oauth_clients')
            ->where('password_client', true)
            ->first();

        if (!$client) {
            return response()->json([
                'message' => 'Laravel Passport is not setup properly.',
                'status' => 500
            ], 500);
        }

        $data = [
            'grant_type' => 'password',
            'client_id' => $client->id,
            'client_secret' => $client->secret,
            'username' => request('email'),
            'password' => request('password'),
        ];

        $request = Request::create('/oauth/token', 'POST', $data);

        $response = app()->handle($request);

        // Проверяем был ли внутренний запрос успешным
        if ($response->getStatusCode() != 200) {
            return response()->json([
                'message' => 'Не верный логин или пароль',
                'status' => 422
            ], 422);
        }

        // Вытаскиваем данные из ответа
        $data = json_decode($response->getContent());

        // Формируем окончательный ответ в нужном формате
        return response()->json([
            'token' => $data->access_token,
            'user' => $user,
            'status' => 200
        ]);
    }

    public function logout()
    {
        return (Auth::user());

    }
    public function getUser()
    {
        return Auth::user();
    }

    public function is_admin(Request $request)
    {
        $status = Status::where('user_id', '=', $request->user_id)->get();
        return $status;
    }

    public function is_ban()
    {
        $user_valid = Status::where('user_id', '=',  auth()->guard('api')->user()->id)->first();
        if($user_valid['banned']==1) {
            return response()->json([
                'message' => 'Пользователь забанен',
                'status' => 'fail'
            ], 200);
        }
        else
        {
            return response([
                'status' => 'success',
            ], 200);
        }
    }


}
