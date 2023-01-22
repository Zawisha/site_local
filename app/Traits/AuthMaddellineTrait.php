<?php
namespace App\Traits;

use App\Models\TelegramPhones;
trait AuthMaddellineTrait
{
    public function madAuth($phone)
    {
            $phones = TelegramPhones::where('phone', '=', $phone)->get();
            if($phones[0]['proxy_adres']=='')
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
                                'address' => $phones[0]['proxy_adres'],
                                'port' => $phones[0]['proxy_port'],
                                'username' => $phones[0]['proxy_username'],//Можно удалить если логина нет
                                'password' => $phones[0]['proxy_password'],//Можно удалить если пароля нет
                            ],
                        ],
                    ],
                ];

            }

        //try блок добавить
        try {
        $MadelineProto = new \danog\MadelineProto\API(public_path().'/my_mad_sessions/'.$phone.'/session.madeline', $settings);
        }
        catch (\Throwable $e)
        {
            return response()->json([
                'status' => 'not!!!',
                'todo'    => 'auth maddeline error',
                'message'    => $e,
            ], 200);
        }
        return $MadelineProto;
    }
}
