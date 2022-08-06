<?php

namespace App\Http\Controllers;

use App\Models\OldClients;
use App\Models\TechnologyText;
use App\Models\TelegramPhones;
use Illuminate\Http\Request;
use App\Models\GeneralDev;
use App\Models\TechList;
use danog\MadelineProto\API;
use danog\Serializable;
use Illuminate\Support\Facades\DB;
use League\Flysystem\Exception;
use App\Traits\AuthMaddellineTrait;

//require_once 'madeline.php';

class TelegramMessageController extends Controller
{
    use AuthMaddellineTrait;
    public function send_message(Request $request)
    {
        $phone = $request->input('phone');
        $technology = $request->input('technology');
        if(($phone=='')||($technology==''))
        {
            return 'выберите технологию или телефон';
        }
        $MadelineProto = $this->madAuth($phone);
        $text_technology = TechnologyText::where('technology_id','=',$technology) ->value('text_technology');
        $dev_id = GeneralDev::where('technology_id','=',$technology)->where('write','=','0') ->first();

        try {
            $result=$MadelineProto->messages->sendMessage(['peer' =>$dev_id['user_id'], 'message' => $text_technology]);
        }
        catch (\Throwable $e)
        {
            try {
                if($dev_id['username']!='NO')
                {
                    $MadelineProto->messages->sendMessage(['peer' =>$dev_id['username'], 'message' => $text_technology]);
                }
            }
            catch (\Throwable $e1)
            {
                GeneralDev::where('user_id','=',$dev_id['user_id'])->update([
                    'write' =>'1'
                ]);
                return $e1;
            }
            GeneralDev::where('user_id','=',$dev_id['user_id'])->update([
                'write' =>'1'
            ]);
            return 'ok1';
        }
        GeneralDev::where('user_id','=',$dev_id['user_id'])->update([
            'write' =>'1'
        ]);

        return 'ok';
    }

    public function send_message_to_group_to_find_cust(Request $request)
    {

        $phone = "+79961161738";
        $local = $request->input('local');
        $id_client = $request->input('id_client');
        $user_in_db=OldClients::where('user_id','=',$id_client)->get();
        if($user_in_db->isEmpty())
        {
            OldClients::create([
                'user_id' => $id_client,
            ]);
        }
        else
        {
            return 'клиент уже есть';
        }
        $MadelineProto = $this->madAuth($phone);

        if($local==1) {
            $message = 'Hello!

    My name is Andrey.  I represent the software development team from Belarus.
    We have experience with laravel / vue / react, but we have the opportunity to connect developers to other technologies.
    I would like to ask you, maybe you have any tasks or updates that we could work on?  I will be glad to help you!  Please let me know if you are interested.

    Thank you!

    Yours faithfully,
  Andrei';
        }
        else
        {
            $message = 'Здравствуйте.Меня зовут Андрей, я представляю небольшую команду разработчиков из Беларуси, наш основной стек laravel/vue/react, но также есть возможность подключить разработчиков и на другие технологии. Если у Вас есть задачи, возможно мы могли бы посотрудничать. Рассматриваем любые варианты.Спасибо.';
        }
        $result=$MadelineProto->messages->sendMessage(['peer' =>$id_client, 'message' => $message]);
        return $result;
    }

    public function send_message_to_group(Request $request)
    {
        $id_phone = $request->input('id_phone');
        $show_message_counter = $request->input('show_message_counter');
        $technology = $request->input('technology');
        $group_id = $request->input('group_id');

        if(($id_phone==='')||($show_message_counter==='')||($technology==='')||($group_id===''))
        {
            return 'выберите технологию или группу или счётчик сбился';
        }

        $phones = TelegramPhones::where('id','=',$id_phone) ->get();
        $phone=($phones[0]['phone']);
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
        $MadelineProto = new \danog\MadelineProto\API(public_path().'/my_mad_sessions/'.$phone.'/session.madeline', $settings);
        $text_technology = TechnologyText::where('technology_id','=',$technology) ->value('text_technology');
        $dev_id = GeneralDev::where('technology_id','=',$technology)->where('write','=','0') ->first();
       // return $dev_id;
        $user_in_db=OldClients::where('user_id','=',$dev_id['user_id'])->get();
        if($user_in_db->isEmpty()) {
        try {
            $result=$MadelineProto->messages->sendMessage(['peer' =>$dev_id['user_id'], 'message' => $text_technology]);
        }
        catch (\Throwable $e)
        {
            try {
                if($dev_id['username']!='NO')
                {
                    $MadelineProto->messages->sendMessage(['peer' =>$dev_id['username'], 'message' => $text_technology]);
                }
            }
            catch (\Throwable $e1)
            {
                GeneralDev::where('user_id','=',$dev_id['user_id'])->update([
                    'write' =>'1'
                ]);
                return $e1;
            }
            GeneralDev::where('user_id','=',$dev_id['user_id'])->update([
                'write' =>'1'
            ]);

            return $e;
        }
        GeneralDev::where('user_id','=',$dev_id['user_id'])->update([
            'write' =>'1'
        ]);

//        return $dev_id;
        return 'написано';
        }
        else
        {
            GeneralDev::where('user_id','=',$dev_id['user_id'])->update([
                'write' =>'1'
            ]);
        }


    }


}
