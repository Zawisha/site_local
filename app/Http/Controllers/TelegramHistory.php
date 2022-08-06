<?php

namespace App\Http\Controllers;

use App\Models\Customers;
use App\Models\GeneralDev;
use App\Models\OldClients;
use App\Models\TelegramHistoryChannels;
use App\Models\TelegramHistoryModel;
use App\Models\TelegramPhones;
use App\Traits\AuthMaddellineTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TelegramHistory extends Controller
{
    use AuthMaddellineTrait;

    public function add_old_clients(Request $request)
    {
        $old_clients = $request->input('old_clients');
        preg_match_all('#[0-9]*#', $old_clients, $res);
        foreach ($res[0] as $re)
        {
            if(($re !='')&&(is_numeric($re)))
            {
                    for ($i = 0; $i <= 2; $i++) {
                        OldClients::create([
                            'user_id' => $re,
                        ]);
                    }
            }
        }
    }
    public function get_ids_users(Request $request)
    {
        $phone = $request->input('phone');
        $username = $request->input('username_to_id');
        if ($phone == '') {
            return 'пустой телефон';
        }
        if ($username == '') {
            return 'пустой username';
        }
        $MadelineProto = $this->madAuth($phone);
        $messages_Messages = $MadelineProto->getFullInfo($username);
        return $messages_Messages["User"]["id"];
    }
    public function get_separately_messages(Request $request)
    {

        $phone = $request->input('phone');
        $in_work = $request->input('in_work');
        if($phone=='')
        {
            return 'пустой телефон';
        }
        $telegram_channels=TelegramHistoryModel::where('in_work','=',$in_work)->get();
        if($in_work == '1')
        {
            $telegram_words = ['вакансия','junior','part','проектная','частичная','не полная','фриланс','неполная','подработк','почасов','по часов'];
        }
        else
        {
            $telegram_words=TelegramHistoryChannels::get();
        }
        $MadelineProto = $this->madAuth($phone);
        $all_messages = [];
        $cur_time = time();
        foreach ($telegram_channels as $tel_channel) {
            if($tel_channel['last_id_revert']!='final')
            {
                for ($i = 0; $i < 3; $i++) {
                    $offset = ($tel_channel['last_id_revert']) + (100 * $i);
                    $messages_Messages = $MadelineProto->messages->getHistory(['peer' => $tel_channel['channel'], 'offset_id' => 0, 'offset_date' => 0, 'add_offset' => $offset, 'limit' => 100, 'max_id' => 0, 'min_id' => 0, 'hash' => [0]]);
                    if ($messages_Messages['count'] < $offset) {
                        TelegramHistoryModel::where('channel', '=', $tel_channel['channel'])->update
                        ([
                            'last_id_revert' => 'final'
                        ]);
                    } else {
                        foreach ($messages_Messages['messages'] as $one_message) {
                            $post_time = $one_message['date'] + (3600 * 3);
                            $date = date('d-m-Y H:i:s', $post_time);
                            try {
                                $user_id = $one_message['from_id']['user_id'];
                            } catch (\Throwable $e1) {
                                $user_id = 'undefined';
                            }


                            //перебираем слова поиска
                            $couner_coincide=0;
                            foreach ($telegram_words as $word) {
                                try {
                                    //  всё в нижний регистр
                                    $one_message = mb_strtolower($one_message['message']);
                                } catch (\Throwable $e) {
                                    try {
                                        $one_message = mb_strtolower($one_message);
                                    } catch (\Throwable $e1) {
                                        $one_message = '';
                                    }
                                }
                                if($in_work == '1')
                                {
                                    $reg_result = preg_match('#' . $word . '#', $one_message);
                                }
                                else
                                {
                                    $reg_result = preg_match('#' . $word['search_word'] . '#', $one_message);
                                }
                                if($reg_result==1)
                                        {
                                            $couner_coincide++;
                                        }
                            }

                            $reg_result_to_del = preg_match('#' . '\#резюме' . '#', $one_message);

                            //если хоть одно слово то добавляешь
                            if($in_work == '1')
                            {
                                //количество совпадений первой группы
                                $coins_count=1;
                            }
                            else
                            {
                                //количество совпадений второй
                                $coins_count=2;
                            }
                                if (($couner_coincide >$coins_count)&&($reg_result_to_del!=1)) {
                                    $user_in_db=OldClients::where('user_id','=',$user_id)->count();
                                    if($user_id == 'undefined')
                                    {
                                        $user_in_db=0;
                                    }
                                    $old_text=0;
                                    if(mb_strlen($one_message)>100)
                                    {
                                      $old_text=Customers::where('text_sep','=',$one_message)->count();
                                    }
                                    if(($user_in_db < 3) &&($old_text < 1)) {
                                        //путь к файлу storage app
                                        $response = $user_id . ' ' . $one_message;
                                        //удалить дубли
                                        $flag = '0';
                                        foreach ($all_messages as $mes) {
                                            if ($mes == $response) {
                                                $flag = '1';
                                            }
                                        }
                                        if ($flag == '0') {
                                            $all_messages[] =$user_id . ' ' . $one_message;
                                            $save_to_doc=$tel_channel['channel'] . ' ' . $user_id . ' ' . ' ' . $date . ' ' . $one_message;
                                            if($in_work == '1')
                                            {
                                                if($user_id=='undefined')
                                                {
                                                    $user_id=0;
                                                }
                                                Customers::create([
                                                    'text' => $save_to_doc,
                                                    'cust_id' => $user_id,
                                                    'text_sep' => $one_message,
                                                ]);
                                                Storage::append('search/' . $cur_time . '.txt', $save_to_doc);
                                                Storage::append('search/' . $cur_time . '.txt', '======================================================================================');
                                            }
                                            else
                                            {
                                                if($user_id=='undefined')
                                                {
                                                    $user_id=0;
                                                }
                                                Customers::create([
                                                    'text' => $save_to_doc,
                                                    'cust_id' => $user_id,
                                                    'text_sep' => $one_message,
                                                ]);
                                                Storage::append('search2/' . $cur_time . '.txt', $save_to_doc);
                                                Storage::append('search2/' . $cur_time . '.txt', '======================================================================================');
                                            }
                                            OldClients::create([
                                                'user_id' => $user_id,
                                            ]);
                                        }
                                    }

                                }


                        }
                        TelegramHistoryModel::where('channel', '=', $tel_channel['channel'])->update
                        ([
                            'last_id_revert' => $offset
                        ]);
                    }

                }
            }

        }

    }
    public function get_telegram_messages_new_separately(Request $request)
    {
//путь к файлу storage app

        $phone = $request->input('phone');
        $in_work = $request->input('in_work');
        if($phone=='')
        {
            return 'пустой телефон';
        }
        $telegram_channels=TelegramHistoryModel::where('in_work','=',$in_work)->get();
        if($in_work == '1')
        {
            $telegram_words = ['вакансия','junior','part','проектная','частичная','не полная','фриланс','неполная','подработк','почасов','по часов'];
        }
        else
        {
            $telegram_words=TelegramHistoryChannels::get();
        }

        $MadelineProto=$this->madAuth($phone);
//если в сообщении пустой месседж
        $all_messages=[];
        $cur_time=time();
        foreach ($telegram_channels as $tel_channel)
        {

//            если новый канал
            if($tel_channel['last_id']==0)
            {
                for($i = 0; $i < 3; $i++)
                {
                    $offset=100*$i;
                    try
                    {
                    $messages_Messages = $MadelineProto->messages->getHistory(['peer' => $tel_channel['channel'], 'offset_id' => 0, 'offset_date' => 0, 'add_offset' => $offset, 'limit' => 100, 'max_id' => 0, 'min_id' => 0, 'hash' => [0] ]);
                    }
                    catch (\Throwable $e1)
                    {
                        dd($tel_channel);
                    }
                    foreach ($messages_Messages['messages'] as $one_message)
                    {
                        $post_time=$one_message['date']+(3600*3);
                        $date=date('d-m-Y H:i:s', $post_time);
                        try {
                            $user_id=$one_message['from_id']['user_id'];
                        }
                        catch (\Throwable $e1)
                        {
                            $user_id='undefined';
                        }
                        //перебираем слова поиска
                        $couner_coincide=0;
                        foreach ($telegram_words as $word) {
                            try {
                                //  всё в нижний регистр
                                $one_message = mb_strtolower($one_message['message']);
                            } catch (\Throwable $e) {
                                try {
                                    $one_message = mb_strtolower($one_message);
                                } catch (\Throwable $e1) {
                                    $one_message = '';
                                }
                            }
                            if($in_work == '1')
                            {
                                $reg_result = preg_match('#' . $word . '#', $one_message);
                            }
                            else
                            {
                                $reg_result = preg_match('#' . $word['search_word'] . '#', $one_message);
                            }
                            if($reg_result==1)
                            {
                                $couner_coincide++;
                            }
                        }

                        $reg_result_to_del = preg_match('#' . '\#резюме' . '#', $one_message);

                            //если хоть одно слово то добавляешь
                        if($in_work == '1')
                        {
                            //количество совпадений первой группы
                            $coins_count=1;
                        }
                        else
                        {
                            //количество совпадений второй
                            $coins_count=2;
                        }
                        if (($couner_coincide >$coins_count)&&($reg_result_to_del!=1)) {
                                $user_in_db=OldClients::where('user_id','=',$user_id)->count();
                                if($user_id == 'undefined')
                                {
                                    $user_in_db=0;
                                }
                                $old_text=0;
                                if(mb_strlen($one_message)>100)
                                {
                                    $old_text=Customers::where('text_sep','=',$one_message)->count();
                                }
                                if(($user_in_db < 3) &&($old_text < 1)) {
                                    //путь к файлу storage app
                                    $response = $user_id . ' ' . $one_message;
                                    //удалить дубли
                                    $flag = '0';
                                    foreach ($all_messages as $mes) {
                                        if ($mes == $response) {
                                            $flag = '1';
                                        }
                                    }
                                    if ($flag == '0') {
                                        $all_messages[] =$user_id . ' ' . $one_message;
                                        $save_to_doc=$tel_channel['channel'] . ' ' . $user_id . ' ' . ' ' . $date . ' ' . $one_message;
                                        if($in_work == '1')
                                        {
                                            if($user_id=='undefined')
                                            {
                                                $user_id=0;
                                            }
                                            Customers::create([
                                                'text' => $save_to_doc,
                                                'cust_id' => $user_id,
                                                'text_sep' => $one_message,
                                            ]);
                                            Storage::append('search/' . $cur_time . '.txt', $save_to_doc);
                                            Storage::append('search/' . $cur_time . '.txt', '======================================================================================');
                                        }
                                        else
                                        {
                                            if($user_id=='undefined')
                                            {
                                                $user_id=0;
                                            }
                                            Customers::create([
                                                'text' => $save_to_doc,
                                                'cust_id' => $user_id,
                                                'text_sep' => $one_message,
                                            ]);
                                            Storage::append('search2/' . $cur_time . '.txt', $save_to_doc);
                                            Storage::append('search2/' . $cur_time . '.txt', '======================================================================================');
                                        }
                                        OldClients::create([
                                            'user_id' => $user_id,
                                        ]);
                                    }
                                }
                            }
                    }

                }
                TelegramHistoryModel::where('channel', '=', $tel_channel['channel'])->update([
                    'last_id' => $messages_Messages['messages'][(count($messages_Messages['messages'])-1)]['id']
                ]);
            }
            //конец если новый канал
            //если не новый канал
            else
            {
                //получаю одно сообщение, чтобы узнать id последнего сообщения в канале
                $messages_Messages_old = $MadelineProto->messages->getHistory(['peer' => $tel_channel['channel'], 'offset_id' => 0, 'offset_date' => 0, 'add_offset' => 0, 'limit' => 1, 'max_id' => 0, 'min_id' => 0, 'hash' => [0] ]);
                $count_of_unread_messages=(($messages_Messages_old['messages']['0']['id'])-($tel_channel['last_id']))/100;
                //округляем вверх
                $count_of_unread_messages=(ceil($count_of_unread_messages));
                if($count_of_unread_messages !=0)
                {
                    for($j = 0; $j < $count_of_unread_messages; $j++)
                    {
                        $offset=100*$j;
                        $messages_Messages = $MadelineProto->messages->getHistory(['peer' => $tel_channel['channel'], 'offset_id' => 0, 'offset_date' => 0, 'add_offset' => $offset, 'limit' => 100, 'max_id' => 0, 'min_id' => $tel_channel['last_id'], 'hash' => [0] ]);

                        foreach ($messages_Messages['messages'] as $one_message)
                        {
                            $post_time=$one_message['date']+(3600*3);
                            $date=date('d-m-Y H:i:s', $post_time);
                            try {
                                $user_id=$one_message['from_id']['user_id'];
                            }
                            catch (\Throwable $e1)
                            {
                                $user_id='undefined';
                            }
                            //перебираем слова поиска
                            $couner_coincide=0;
                            foreach ($telegram_words as $word) {
                                try {
                                    //  всё в нижний регистр
                                    $one_message = mb_strtolower($one_message['message']);
                                } catch (\Throwable $e) {
                                    try {
                                        $one_message = mb_strtolower($one_message);
                                    } catch (\Throwable $e1) {
                                        $one_message = '';
                                    }
                                }
                                if($in_work == '1')
                                {
                                    $reg_result = preg_match('#' . $word . '#', $one_message);
                                }
                                else
                                {
                                    $reg_result = preg_match('#' . $word['search_word'] . '#', $one_message);
                                }
                                if($reg_result==1)
                                {
                                    $couner_coincide++;
                                }
                            }

                            $reg_result_to_del = preg_match('#' . '\#резюме' . '#', $one_message);

                            //если хоть одно слово то добавляешь
                            if($in_work == '1')
                            {
                                //количество совпадений первой группы
                                $coins_count=1;
                            }
                            else
                            {
                                //количество совпадений второй
                                $coins_count=2;
                            }
                            if (($couner_coincide >$coins_count)&&($reg_result_to_del!=1)) {
                                $user_in_db=OldClients::where('user_id','=',$user_id)->count();
                                if($user_id == 'undefined')
                                {
                                    $user_in_db=0;
                                }
                                $old_text=0;
                                if(mb_strlen($one_message)>100)
                                {
                                    $old_text=Customers::where('text_sep','=',$one_message)->count();
                                }
                                if(($user_in_db < 3) &&($old_text < 1)) {
                                    //путь к файлу storage app
                                    $response = $user_id . ' ' . $one_message;
                                    //удалить дубли
                                    $flag = '0';
                                    foreach ($all_messages as $mes) {
                                        if ($mes == $response) {
                                            $flag = '1';
                                        }
                                    }
                                    if ($flag == '0') {
                                        $all_messages[] =$user_id . ' ' . $one_message;
                                        $save_to_doc=$tel_channel['channel'] . ' ' . $user_id . ' ' . ' ' . $date . ' ' . $one_message;
                                        if($in_work == '1')
                                        {
                                            if($user_id=='undefined')
                                            {
                                                $user_id=0;
                                            }
                                            Customers::create([
                                                'text' => $save_to_doc,
                                                'cust_id' => $user_id,
                                                'text_sep' => $one_message,
                                            ]);
                                            Storage::append('search/' . $cur_time . '.txt', $save_to_doc);
                                            Storage::append('search/' . $cur_time . '.txt', '======================================================================================');
                                        }
                                        else
                                        {
                                            if($user_id=='undefined')
                                            {
                                                $user_id=0;
                                            }
                                            Customers::create([
                                                'text' => $save_to_doc,
                                                'cust_id' => $user_id,
                                                'text_sep' => $one_message,
                                            ]);
                                            Storage::append('search2/' . $cur_time . '.txt', $save_to_doc);
                                            Storage::append('search2/' . $cur_time . '.txt', '======================================================================================');
                                        }
                                        OldClients::create([
                                            'user_id' => $user_id,
                                        ]);
                                    }
                                }
                            }
                        }

                    }
                    TelegramHistoryModel::where('channel', '=', $tel_channel['channel'])->update([
                        'last_id' =>$messages_Messages_old['messages']['0']['id']
                    ]);
                }
            }

        }
        return ('готово');

    }
    public function get_oldest(Request $request)
    {

        $phone = $request->input('phone');
        $flag_to_find=$request->input('flag_to_find');
        if ($phone == '') {
            return 'пустой телефон';
        }
        $telegram_channels=TelegramHistoryModel::where('in_work','=','1')->get();
        $telegram_words = TelegramHistoryChannels::get();
        $MadelineProto = $this->madAuth($phone);
        $all_messages = [];
        $cur_time = time();
        foreach ($telegram_channels as $tel_channel) {
            if($tel_channel['last_id_revert']!='final')
                {
                    for ($i = 0; $i < 3; $i++) {
                        $offset = ($tel_channel['last_id_revert']) + (100 * $i);
                        $messages_Messages = $MadelineProto->messages->getHistory(['peer' => $tel_channel['channel'], 'offset_id' => 0, 'offset_date' => 0, 'add_offset' => $offset, 'limit' => 100, 'max_id' => 0, 'min_id' => 0, 'hash' => [0]]);
                        if ($messages_Messages['count'] < $offset) {
                            TelegramHistoryModel::where('channel', '=', $tel_channel['channel'])->update
                            ([
                                'last_id_revert' => 'final'
                            ]);
                        } else {
                            foreach ($messages_Messages['messages'] as $one_message) {
                                $post_time = $one_message['date'] + (3600 * 3);
                                $date = date('d-m-Y H:i:s', $post_time);
                                try {
                                    $user_id = $one_message['from_id']['user_id'];
                                } catch (\Throwable $e1) {
                                    $user_id = 'undefined';
                                }
                                //  всё в нижний регистр
                                foreach ($telegram_words as $word) {
                                    try {
                                        $one_message = mb_strtolower($one_message['message']);
                                    } catch (\Throwable $e) {
                                        try {
                                            $one_message = mb_strtolower($one_message);
                                        } catch (\Throwable $e1) {
                                            $one_message = '';
                                        }
                                    }
                                    if($flag_to_find!='1') {
                                        $reg_result = preg_match('#' . $word['search_word'] . '#', $one_message);
                                    }
                                    else
                                    {
                                        $reg_result=1;
                                    }
                                    if ($reg_result == 1) {
                                        $user_in_db=OldClients::where('user_id','=',$user_id)->get();
                                        if($user_in_db->isEmpty()) {
                                            //путь к файлу storage app
                                            $response = $user_id . ' ' . $one_message;
                                            //удалить дубли
                                            $flag = '0';
                                            foreach ($all_messages as $mes) {
                                                if ($mes == $response) {
                                                    $flag = '1';
                                                }
                                            }
                                            if ($flag == '0') {
                                                $all_messages[] =$user_id . ' ' . $one_message;
                                                $save_to_doc=$tel_channel['channel'] . ' ' . $user_id . ' ' . ' ' . $date . ' ' . $one_message;
                                                Storage::append('search/' . $cur_time . '.txt', $save_to_doc);
                                                Storage::append('search/' . $cur_time . '.txt', '======================================================================================');
                                            }
                                        }
                                    }
                                }

                            }
                            TelegramHistoryModel::where('channel', '=', $tel_channel['channel'])->update
                            ([
                                'last_id_revert' => $offset
                            ]);
                        }

                    }
                }

        }

    }


    public function clear_directory()
    {

         $files = Storage::allFiles('search/');
        foreach ($files as $file)
        {
            Storage::delete($file);
        }
    }

    public function get_telegram_messages(Request $request)
    {
//путь к файлу storage app

        $phone = $request->input('phone');
        if($phone=='')
        {
            return 'пустой телефон';
        }
        $telegram_channels=TelegramHistoryModel::where('in_work','=','1')->get();
        $telegram_words=TelegramHistoryChannels::get();
        $MadelineProto=$this->madAuth($phone);
//если в сообщении пустой месседж
        $all_messages=[];
        $cur_time=time();
        foreach ($telegram_channels as $tel_channel)
        {

//            если новый канал
            if($tel_channel['last_id']==0)
            {
                for($i = 0; $i < 3; $i++)
                {
                    $offset=100*$i;
                $messages_Messages = $MadelineProto->messages->getHistory(['peer' => $tel_channel['channel'], 'offset_id' => 0, 'offset_date' => 0, 'add_offset' => $offset, 'limit' => 100, 'max_id' => 0, 'min_id' => 0, 'hash' => [0] ]);
                    foreach ($messages_Messages['messages'] as $one_message)
                    {
                        $post_time=$one_message['date']+(3600*3);
                        $date=date('d-m-Y H:i:s', $post_time);
                        try {
                            $user_id=$one_message['from_id']['user_id'];
                        }
                        catch (\Throwable $e1)
                        {
                            $user_id='undefined';
                        }
                        //  всё в нижний регистр
                        foreach ($telegram_words as $word)
                        {
                            try {
                                $one_message = mb_strtolower($one_message['message']);
                            }
                            catch (\Throwable $e)
                            {
                                try {
                                    $one_message = mb_strtolower($one_message);
                                }
                                catch (\Throwable $e1)
                                {
                                    $one_message='';
                                }
                            }
                            $reg_result=preg_match('#'.$word['search_word'].'#', $one_message);
                            if($reg_result==1) {
                                $user_in_db=OldClients::where('user_id','=',$user_id)->get();
                                if ($user_in_db->isEmpty()) {
                                    //путь к файлу storage app
                                    $response = $user_id . ' ' . $one_message;
                                    //удалить дубли
                                    $flag = '0';
                                    foreach ($all_messages as $mes) {
                                        if ($mes == $response) {
                                            $flag = '1';
                                        }
                                    }
                                    if ($flag == '0') {
                                        $all_messages[] =$user_id . ' ' . $one_message;
                                        $save_to_doc=$tel_channel['channel'] . ' ' . $user_id . ' ' . ' ' . $date . ' ' . $one_message;
                                        Storage::append('search/' . $cur_time . '.txt', $save_to_doc);
                                        Storage::append('search/' . $cur_time . '.txt', '======================================================================================');
                                    }
                                }
                            }
                        }

                    }
                }
                TelegramHistoryModel::where('channel', '=', $tel_channel['channel'])->update([
                    'last_id' => $messages_Messages['messages'][(count($messages_Messages['messages'])-1)]['id']
                ]);
           }
            //конец если новый канал
            //если не новый канал
            else
            {
                //получаю одно сообщение, чтобы узнать id последнего сообщения в канале
                $messages_Messages_old = $MadelineProto->messages->getHistory(['peer' => $tel_channel['channel'], 'offset_id' => 0, 'offset_date' => 0, 'add_offset' => 0, 'limit' => 1, 'max_id' => 0, 'min_id' => 0, 'hash' => [0] ]);
                $count_of_unread_messages=(($messages_Messages_old['messages']['0']['id'])-($tel_channel['last_id']))/100;
                //округляем вверх
                $count_of_unread_messages=(ceil($count_of_unread_messages));
                if($count_of_unread_messages !=0)
                {
            for($j = 0; $j < $count_of_unread_messages; $j++)
            {
                $offset=100*$j;
                $messages_Messages = $MadelineProto->messages->getHistory(['peer' => $tel_channel['channel'], 'offset_id' => 0, 'offset_date' => 0, 'add_offset' => $offset, 'limit' => 100, 'max_id' => 0, 'min_id' => $tel_channel['last_id'], 'hash' => [0] ]);
                foreach ($messages_Messages['messages'] as $one_message)
                {
                    $post_time=$one_message['date']+(3600*3);
                    $date=date('d-m-Y H:i:s', $post_time);
                    try {
                        $user_id=$one_message['from_id']['user_id'];
                    }
                    catch (\Throwable $e1)
                    {
                        $user_id='undefined';
                    }
                  //  всё в нижний регистр
                   foreach ($telegram_words as $word)
                   {
                       try {
                           $one_message = mb_strtolower($one_message['message']);
                       }
                       catch (\Throwable $e)
                       {
                           try {
                               $one_message = mb_strtolower($one_message);
                           }
                           catch (\Throwable $e1)
                           {
                               $one_message='';
                           }
                       }
                       $reg_result=preg_match('#'.$word['search_word'].'#', $one_message);
                       if($reg_result==1)
                       {
                           $user_in_db=OldClients::where('user_id','=',$user_id)->get();
                           if ($user_in_db->isEmpty()) {
                               //путь к файлу storage app
                               $response = $tel_channel['channel'] . ' ' . $user_id . ' ' . ' ' . $date . ' ' . $one_message;
                               //удалить дубли
                               $flag = '0';
                               foreach ($all_messages as $mes) {
                                   if ($mes == $response) {
                                       $flag = '1';
                                   }
                               }
                               if ($flag == '0') {
                                   $all_messages[] = $tel_channel['channel'] . ' ' . $user_id . ' ' . ' ' . $date . ' ' . $one_message;
                                   Storage::append('search/' . $cur_time . '.txt', $response);
                                   Storage::append('search/' . $cur_time . '.txt', '======================================================================================');
                               }
                           }
                       }
                   }

                }
            }
                TelegramHistoryModel::where('channel', '=', $tel_channel['channel'])->update([
                    'last_id' =>$messages_Messages_old['messages']['0']['id']
                ]);
            }
            }

        }
        return ('готово');

    }
    public function add_channel_tech_history(Request $request)
    {
        $channel = $request->input('channel');
        if($channel=='')
        {
            return 'пустой канал';
        }
        $channel_exist=TelegramHistoryModel::where('channel','=',$channel)->get();

        if($channel_exist->isEmpty())
        {
                TelegramHistoryModel::create([
                'channel' => $channel,
           ]);
                return 'канал добавлен в базу данных';
        }
        else
        {
            return 'канал уже есть в списке';
        }
    }

    public function add_word_tech_history(Request $request)
    {
        $word = $request->input('word');
        if($word=='')
        {
            return 'пустое словосочетание';
        }
        else
        {
            $word=mb_strtolower($word);
            $word=trim($word);
            TelegramHistoryChannels::create([
                'search_word' => $word,
            ]);
            return 'добавлено';
        }
    }

}
