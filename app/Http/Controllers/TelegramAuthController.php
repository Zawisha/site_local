<?php

namespace App\Http\Controllers;

use App\Models\TelegramPhones;
use danog\MadelineProto\Settings\Connection;
use danog\MadelineProto\Stream\Proxy\SocksProxy;
use Illuminate\Http\Request;
use App\Models\GeneralDev;
use App\Models\TechList;
use danog\MadelineProto\API;
use danog\Serializable;
use Illuminate\Support\Facades\DB;
//require_once 'madeline.php';

class TelegramAuthController extends Controller
{
    public function autorization(Request $request)
    {
//        if (!file_exists('madeline.php')) {
//            copy('https://phar.madelineproto.xyz/madeline.php', 'madeline.php');
//        }
        $phone = $request->input('phone');
        $proxy_adres = $request->input('proxy_adres');
        $proxy_port = $request->input('proxy_port');
        $proxy_username = $request->input('proxy_username');
        $proxy_password = $request->input('proxy_password');
        if($phone=='')
        {
            return 'Пустой телефон';
        }
        $phones = TelegramPhones::where('phone', '=',$phone) ->get();

  if(!(is_dir(public_path().'/my_mad_sessions/'.$phone)))
  {
      mkdir(public_path().'/my_mad_sessions/'.$phone);
  }
        if (file_exists(public_path().'/my_mad_sessions/'.$phone.'/session.madeline')) {
            unlink(public_path().'/my_mad_sessions/'.$phone.'/session.madeline');
        }
                if (file_exists(public_path().'/my_mad_sessions/'.$phone.'/session.madeline.callback.ipc')) {
            unlink(public_path().'/my_mad_sessions/'.$phone.'/session.madeline.callback.ipc');
        }
        if (file_exists(public_path().'/my_mad_sessions/'.$phone.'/session.madeline.ipcState.php')) {
            unlink(public_path().'/my_mad_sessions/'.$phone.'/session.madeline.ipcState.php');
        }
        if (file_exists(public_path().'/my_mad_sessions/'.$phone.'/session.madeline.ipcState.php.lock')) {
            unlink(public_path().'/my_mad_sessions/'.$phone.'/session.madeline.ipcState.php.lock');
        }
        if (file_exists(public_path().'/my_mad_sessions/'.$phone.'/session.madeline.lightState.php')) {
            unlink(public_path().'/my_mad_sessions/'.$phone.'/session.madeline.lightState.php');
        }
        if (file_exists(public_path().'/my_mad_sessions/'.$phone.'/session.madeline.lightState.php.lock')) {
            unlink(public_path().'/my_mad_sessions/'.$phone.'/session.madeline.lightState.php.lock');
        }
        if (file_exists(public_path().'/my_mad_sessions/'.$phone.'/session.madeline.lock')) {
            unlink(public_path().'/my_mad_sessions/'.$phone.'/session.madeline.lock');
        }
        if (file_exists(public_path().'/my_mad_sessions/'.$phone.'/session.madeline.safe.php')) {
            unlink(public_path().'/my_mad_sessions/'.$phone.'/session.madeline.safe.php');
        }
        if (file_exists(public_path().'/my_mad_sessions/'.$phone.'/session.madeline.safe.php.lock')) {
            unlink(public_path().'/my_mad_sessions/'.$phone.'/session.madeline.safe.php.lock');
        }

if($proxy_adres=='')
{
    $settings = [
        'app_info' => [ // Эти данные мы получили после регистрации приложения на https://my.telegram.org
            'api_id' => $phones[0]['api_id'],
            'api_hash' => $phones[0]['api_hash'],
            'device_model'=>'Desktop',
        ],
        'logger' => [ // Вывод сообщений и ошибок
            'logger' => 3, // выводим сообещения через echo
            'logger_level' => 4, // выводим только критические ошибки.
        ],
        'serialization' => [
            'serialization_interval' => 300,
            //Очищать файл сессии от некритичных данных.
            //Значительно снижает потребление памяти при интенсивном использовании, но может вызывать проблемы
            'cleanup_before_serialization' => true,
        ]

    ];
    TelegramPhones::where('phone', '=',$phone)->
    update([
        'proxy_adres' => '',
        'proxy_port' => '',
        'proxy_username' => '',
        'proxy_password' => '',
    ]);
}
else
{
    $settings = [
            'app_info' => [ // Эти данные мы получили после регистрации приложения на https://my.telegram.org
                'api_id' => $phones[0]['api_id'],
                'api_hash' => $phones[0]['api_hash'],
                'device_model'=>'Desktop',
            ],
            'logger' => [ // Вывод сообщений и ошибок
                'logger' => 3, // выводим сообещения через echo
                'logger_level' => 4, // выводим только критические ошибки.
            ],
            'serialization' => [
                'serialization_interval' => 300,
                //Очищать файл сессии от некритичных данных.
                //Значительно снижает потребление памяти при интенсивном использовании, но может вызывать проблемы
                'cleanup_before_serialization' => true,
            ],
            'connection_settings' => [
                'all' => [
                    'proxy' => '\SocksProxy',
                    'proxy_extra' => [
                        'address' => $proxy_adres,
                        'port' => $proxy_port,
                        'username' => $proxy_username,//Можно удалить если логина нет
                        'password' => $proxy_password,//Можно удалить если пароля нет
                    ],
                ],
            ],

        ];
    TelegramPhones::where('phone', '=',$phone)->
    update([
        'proxy_adres' => $proxy_adres,
        'proxy_port' => $proxy_port,
        'proxy_username' => $proxy_username,
        'proxy_password' => $proxy_password,
    ]);
}

        $MadelineProto = new \danog\MadelineProto\API(public_path().'/my_mad_sessions/'.$phone.'/session.madeline', $settings);
        //$MadelineProto = new \danog\MadelineProto\API('session.madeline', $settings);
        $MadelineProto->phone_login($phones[0]['phone']);
        return 'ok';
    }
    public function send_code(Request $request)
    {
        $auth_code = $request->input('auth_code');
        $phone = $request->input('phone');
        $proxy_adres = $request->input('proxy_adres');
        $proxy_port = $request->input('proxy_port');
        $proxy_username = $request->input('proxy_username');
        $proxy_password = $request->input('proxy_password');
        if($phone=='')
        {
            return 'пустой телефон';
        }
        if($auth_code=='')
        {
            return 'пустой код';
        }
        $phones = TelegramPhones::where('phone','=',$phone) ->get();
        if($proxy_adres=='')
        {
            $settings = [
                'app_info' => [ // Эти данные мы получили после регистрации приложения на https://my.telegram.org
                    'api_id' => $phones[0]['api_id'],
                    'api_hash' => $phones[0]['api_hash'],
                    'device_model'=>'Desktop',
                ],
                'logger' => [ // Вывод сообщений и ошибок
                    'logger' => 3, // выводим сообещения через echo
                    'logger_level' => 4, // выводим только критические ошибки.
                ],
                'serialization' => [
                    'serialization_interval' => 300,
                    //Очищать файл сессии от некритичных данных.
                    //Значительно снижает потребление памяти при интенсивном использовании, но может вызывать проблемы
                    'cleanup_before_serialization' => true,
                ]

            ];
        }
        else
        {
        $settings = [
            'app_info' => [ // Эти данные мы получили после регистрации приложения на https://my.telegram.org
                'api_id' => $phones[0]['api_id'],
                'api_hash' => $phones[0]['api_hash'],
                'device_model'=>'Desktop',
            ],
            'logger' => [ // Вывод сообщений и ошибок
                'logger' => 3, // выводим сообещения через echo
                'logger_level' => 4, // выводим только критические ошибки.
            ],
            'serialization' => [
                'serialization_interval' => 300,
                //Очищать файл сессии от некритичных данных.
                //Значительно снижает потребление памяти при интенсивном использовании, но может вызывать проблемы
                'cleanup_before_serialization' => true,
            ],
            'connection_settings' => [
                'all' => [
                    'proxy' => '\SocksProxy',
                    'proxy_extra' => [
                        'address' => $proxy_adres,
                        'port' => $proxy_port,
                        'username' => $proxy_username,//Можно удалить если логина нет
                        'password' => $proxy_password,//Можно удалить если пароля нет
                    ],
                ],
            ],

        ];
        }

        $MadelineProto = new \danog\MadelineProto\API(public_path().'/my_mad_sessions/'.$phone.'/session.madeline', $settings);
        //костыль
        $MadelineProto->phone_login($phones[0]['phone']);
        $MadelineProto->complete_phone_login($auth_code);
        return 'ok';
    }
}
