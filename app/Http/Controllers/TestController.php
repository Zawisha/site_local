<?php

namespace App\Http\Controllers;

use App\Models\OldClients;
use App\Models\TelegramHistoryChannels;
use App\Models\TelegramHistoryModel;
use App\Models\TelegramPhones;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Traits\AuthMaddellineTrait;


class TestController extends Controller
{
    use AuthMaddellineTrait;

    public function get_mad(Request $request)
    {
//путь к файлу storage app
        $phone = $request->input('phone');
        $in_work = $request->input('in_work');
        if ($phone == '') {
            return 'пустой телефон';
        }
        $telegram_channels = TelegramHistoryModel::where('in_work', '=', $in_work)->get();
        if ($in_work == '1') {
            $telegram_words = ['вакансия', 'junior', 'part', 'проектная', 'частичная', 'не полная', 'фриланс', 'неполная', 'подработк', 'почасов', 'по часов'];
        } else {
            $telegram_words = TelegramHistoryChannels::get();
        }
        $MadelineProto = $this->madAuth($phone);

        $all_messages = [];
        $cur_time = time();
        foreach ($telegram_channels as $tel_channel) {

//            если новый канал
            if ($tel_channel['last_id'] == 0) {
                for ($i = 0; $i < 3; $i++) {
                    $offset = 100 * $i;
                    $messages_Messages = $MadelineProto->messages->getHistory(['peer' => $tel_channel['channel'], 'offset_id' => 0, 'offset_date' => 0, 'add_offset' => $offset, 'limit' => 100, 'max_id' => 0, 'min_id' => 0, 'hash' => [0]]);


                    foreach ($messages_Messages['messages'] as $one_message) {
                        $post_time = $one_message['date'] + (3600 * 3);
                        $date = date('d-m-Y H:i:s', $post_time);
                        try {
                            $user_id = $one_message['from_id']['user_id'];
                        } catch (\Exception $e1) {
                            $user_id = 'undefined';
                        }
                        //перебираем слова поиска
                        $couner_coincide = 0;
                        foreach ($telegram_words as $word) {
                            try {
                                //  всё в нижний регистр
                                $one_message = mb_strtolower($one_message['message']);
                            } catch (\Exception $e) {
                                try {
                                    $one_message = mb_strtolower($one_message);
                                } catch (\Exception $e1) {
                                    $one_message = '';
                                }
                            }
                            if ($in_work == '1') {
                                $reg_result = preg_match('#' . $word . '#', $one_message);
                            } else {
                                $reg_result = preg_match('#' . $word['search_word'] . '#', $one_message);
                            }
                            if ($reg_result == 1) {
                                $couner_coincide++;
                            }
                        }

                        $reg_result_to_del = preg_match('#' . '\#резюме' . '#', $one_message);

                        //если хоть одно слово то добавляешь
                        if (($couner_coincide > 1) && ($reg_result_to_del != 1)) {
                            $user_in_db = OldClients::where('user_id', '=', $user_id)->get();
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
                                    $all_messages[] = $user_id . ' ' . $one_message;
                                    $save_to_doc = $tel_channel['channel'] . ' ' . $user_id . ' ' . ' ' . $date . ' ' . $one_message;
                                    if ($in_work == '1') {
                                        Storage::append('search/' . $cur_time . '.txt', $save_to_doc);
                                        Storage::append('search/' . $cur_time . '.txt', '======================================================================================');
                                    } else {
                                        Storage::append('search2/' . $cur_time . '.txt', $save_to_doc);
                                        Storage::append('search2/' . $cur_time . '.txt', '======================================================================================');
                                    }

                                }
                            }
                        }
                    }

                }
                TelegramHistoryModel::where('channel', '=', $tel_channel['channel'])->update([
                    'last_id' => $messages_Messages['messages'][(count($messages_Messages['messages']) - 1)]['id']
                ]);
            }
            //конец если новый канал
            //если не новый канал
            else {
                //получаю одно сообщение, чтобы узнать id последнего сообщения в канале
                $messages_Messages_old = $MadelineProto->messages->getHistory(['peer' => $tel_channel['channel'], 'offset_id' => 0, 'offset_date' => 0, 'add_offset' => 0, 'limit' => 1, 'max_id' => 0, 'min_id' => 0, 'hash' => [0]]);
                $count_of_unread_messages = (($messages_Messages_old['messages']['0']['id']) - ($tel_channel['last_id'])) / 100;
                //округляем вверх
                $count_of_unread_messages = (ceil($count_of_unread_messages));
                if ($count_of_unread_messages != 0) {
                    for ($j = 0; $j < $count_of_unread_messages; $j++) {
                        $offset = 100 * $j;
                        $messages_Messages = $MadelineProto->messages->getHistory(['peer' => $tel_channel['channel'], 'offset_id' => 0, 'offset_date' => 0, 'add_offset' => $offset, 'limit' => 100, 'max_id' => 0, 'min_id' => $tel_channel['last_id'], 'hash' => [0]]);

                        foreach ($messages_Messages['messages'] as $one_message) {
                            $post_time = $one_message['date'] + (3600 * 3);
                            $date = date('d-m-Y H:i:s', $post_time);
                            try {
                                $user_id = $one_message['from_id']['user_id'];
                            } catch (\Exception $e1) {
                                $user_id = 'undefined';
                            }
                            //перебираем слова поиска
                            $couner_coincide = 0;
                            foreach ($telegram_words as $word) {
                                try {
                                    //  всё в нижний регистр
                                    $one_message = mb_strtolower($one_message['message']);
                                } catch ( \Throwable $e) {
                                    try {
                                        $one_message = mb_strtolower($one_message);
                                    } catch (\Throwable $e1) {
                                        $one_message = '';
                                    }
                                }
                                if ($in_work == '1') {
                                    $reg_result = preg_match('#' . $word . '#', $one_message);
                                } else {
                                    $reg_result = preg_match('#' . $word['search_word'] . '#', $one_message);
                                }
                                if ($reg_result == 1) {
                                    $couner_coincide++;
                                }
                            }

                            $reg_result_to_del = preg_match('#' . '\#резюме' . '#', $one_message);

                            //если хоть одно слово то добавляешь
                            if (($couner_coincide > 1) && ($reg_result_to_del != 1)) {
                                $user_in_db = OldClients::where('user_id', '=', $user_id)->get();
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
                                        $all_messages[] = $user_id . ' ' . $one_message;
                                        $save_to_doc = $tel_channel['channel'] . ' ' . $user_id . ' ' . ' ' . $date . ' ' . $one_message;
                                        if ($in_work == '1') {
                                            Storage::append('search/' . $cur_time . '.txt', $save_to_doc);
                                            Storage::append('search/' . $cur_time . '.txt', '======================================================================================');
                                        } else {
                                            Storage::append('search2/' . $cur_time . '.txt', $save_to_doc);
                                            Storage::append('search2/' . $cur_time . '.txt', '======================================================================================');
                                        }

                                    }
                                }
                            }
                        }

                    }
                    TelegramHistoryModel::where('channel', '=', $tel_channel['channel'])->update([
                        'last_id' => $messages_Messages_old['messages']['0']['id']
                    ]);
                }
            }

        }
        return ('готово');

    }
}
