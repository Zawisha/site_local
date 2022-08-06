<?php

namespace App\Http\Controllers;

use App\Models\ChannelName;
use App\Models\GeneralDev;
use App\Models\GroupCounter;
use App\Models\GroupNumber;
use App\Models\LastUsers;
use App\Models\TechList;
use App\Models\TelegramPhones;
use danog\MadelineProto\API;
use danog\Serializable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
//require_once 'madeline.php';
use App\Traits\AuthMaddellineTrait;


class TelegramUsersController extends Controller
{
    use AuthMaddellineTrait;
    public function delete_NO_users(Request $request)
    {
        GeneralDev::where('username', '=', 'NO')->update(['write' => 1]);
        return 'ok';
    }
    public function save_new_tel_user(Request $request)
    {
        $new_user_telephone = $request->input('new_user_telephone');
        $api_id = $request->input('api_id');
        $api_hash = $request->input('api_hash');
        if(($new_user_telephone=='')||($api_id=='')||($api_hash==''))
        {
            return 'пустое поле';
        }
        TelegramPhones::create([
            'phone' => $new_user_telephone,
            'api_id' => $api_id,
            'api_hash' => $api_hash
        ]);
        return 'ok';
    }
    public function get_technology()
    {
        $tech_list = TechList::all();
        return $tech_list;
    }

    public function get_count_technology(Request $request)
    {
        $technology = $request->input('technology');
        $count = GeneralDev::where('technology_id','=',$technology)->where('write','=','0') ->count();
        return $count;
    }

    public function get_random_users(Request $request)
    {
        $phone = $request->input('phone');
        $channel = $request->input('channel');
        $technology = $request->input('technology');
        if($phone=='')
        {
            return 'пустой телефон';
        }
        if($channel=='')
        {
            return 'пустой канал';
        }
        if($technology=='')
        {
            return 'пустая технология';
        }
        $MadelineProto = $this->madAuth($phone);
        $added_users = 0;


        $queryKey = ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z'];
        $rand_arr=[];
        for ($j = 0; $j < 500; $j++) {
            $rand_string_f='';
            for ($i = 0; $i < 2; $i++) {
                $rand_string = array_rand($queryKey);
                $rand_string_f .= $queryKey[$rand_string];
            }
            $rand_arr[] = $rand_string_f;
        }

        foreach ($rand_arr as $q) {
            $channels_ChannelParticipants = $MadelineProto->channels->getParticipants(['channel' => $channel, 'filter' => ['_' => 'channelParticipantsSearch', 'q' => $q], 'offset' => 0, 'limit' => 1, 'hash' => 0,]);
            $num = ($channels_ChannelParticipants['count']) / 200;
            $num = ceil($num);

            for ($i = 0; $i < $num; $i++) {
                $channels_ChannelParticipants = $MadelineProto->channels->getParticipants(['channel' => $channel, 'filter' => ['_' => 'channelParticipantsSearch', 'q' => $q], 'offset' => $i, 'limit' => 200, 'hash' => 0,]);
                foreach ($channels_ChannelParticipants['users'] as $users) {
                    if (!isset($users['username'])) {
                        $users['username'] = 'NO';
                    }

                    $flag_exist=0;

                    if (GeneralDev::where('user_id', $users['id'])->exists()) {
                        $flag_exist=1;
                    }
                    if($users['username'] != 'NO')
                    {
                        if (GeneralDev::where('username', $users['username'])->exists()) {
                            $flag_exist=1;
                        }
                    }
                    if($flag_exist==0)
                    {
                         GeneralDev::create([
                        'user_id' => $users['id'],
                        'username' => $users['username'],
                        'technology_id' => $technology
                    ]);
                            $added_users++;
                    }
                }
            }
        }
        return ('всего '.$channels_ChannelParticipants['count'].' уникальных '.$added_users);
    }


    public function get_users(Request $request)
    {
        $phone = $request->input('phone');
        $channel = $request->input('channel');
        $technology = $request->input('technology');
        if($phone=='')
        {
            return 'пустой телефон';
        }
        if($channel=='')
        {
            return 'пустой канал';
        }
        if($technology=='')
        {
            return 'пустая технология';
        }
        $MadelineProto = $this->madAuth($phone);
        $added_users = 0;
        $queryKey = ['',' ','a','ab','an','al','ac','am','at','ap',
            'b','c','d','do','da','di','du','de',
            'e','eb','en','el','el','em','et','ae', 'f', 'g',
            'h',
            'i', 'ib','in','il','im','it','ie','ip',
            'j', 'k', 'l', 'm', 'n', 'o','ob','on','om','ot','oe','op',
            'p', 'q', 'r', 's', 't', 'u','ub','un','um','ut','ue','up',
            'v', 'w', 'x', 'y', 'z','zo','za','zi','zu','ze',
        ];
        foreach ($queryKey as $q) {
            $channels_ChannelParticipants = $MadelineProto->channels->getParticipants(['channel' => $channel, 'filter' => ['_' => 'channelParticipantsSearch', 'q' => $q], 'offset' => 0, 'limit' => 1, 'hash' => 0,]);
            //$channels_ChannelParticipants = $MadelineProto->channels->getParticipants(['channel' => $channel, 'filter' => ['_' => 'channelParticipantsSearch'], 'offset' => 0, 'limit' => 1, 'hash' => 0, ]);
            $num = ($channels_ChannelParticipants['count']) / 200;
            $num = ceil($num);

            for ($i = 0; $i < $num; $i++) {
                $channels_ChannelParticipants = $MadelineProto->channels->getParticipants(['channel' => $channel, 'filter' => ['_' => 'channelParticipantsSearch', 'q' => $q], 'offset' => $i, 'limit' => 200, 'hash' => 0,]);
                foreach ($channels_ChannelParticipants['users'] as $users) {
                    if (!isset($users['username'])) {
                        $users['username'] = 'NO';
                    }

                    $flag_exist=0;

                    if (GeneralDev::where('user_id', $users['id'])->exists()) {
                        $flag_exist=1;
                    }
                    if($users['username'] != 'NO')
                    {
                        if (GeneralDev::where('username', $users['username'])->exists()) {
                            $flag_exist=1;
                        }
                    }
                    if($flag_exist==0)
                    {
                        GeneralDev::create([
                            'user_id' => $users['id'],
                            'username' => $users['username'],
                            'technology_id' => $technology
                        ]);
                        $added_users++;
                    }

                }
            }
        }

        $channels_ChannelParticipants = $MadelineProto->channels->getParticipants(['channel' => $channel, 'filter' => ['_' => 'channelParticipantsSearch'], 'offset' => $i, 'limit' => 200, 'hash' => 0,]);
        $num = ($channels_ChannelParticipants['count']) / 200;
        $num = ceil($num);

        for ($i = 0; $i < $num; $i++) {
            $channels_ChannelParticipants = $MadelineProto->channels->getParticipants(['channel' => $channel, 'filter' => ['_' => 'channelParticipantsSearch'], 'offset' => $i, 'limit' => 200, 'hash' => 0,]);
            foreach ($channels_ChannelParticipants['users'] as $users) {
                if (!isset($users['username'])) {
                    $users['username'] = 'NO';
                }

                $flag_exist=0;

                if (GeneralDev::where('user_id', $users['id'])->exists()) {
                    $flag_exist=1;
                }
                if($users['username'] != 'NO')
                {
                    if (GeneralDev::where('username', $users['username'])->exists()) {
                        $flag_exist=1;
                    }
                }
                if($flag_exist==0)
                {
                    GeneralDev::create([
                        'user_id' => $users['id'],
                        'username' => $users['username'],
                        'technology_id' => $technology
                    ]);
                    $added_users++;
                }
            }}


        return ('всего '.$channels_ChannelParticipants['count'].' уникальных '.$added_users);

    }
    public function get_phones()
    {
        $phone_list = TelegramPhones::all();
        return $phone_list;
    }

    public function dotekanie(Request $request)
    {

        $phone = $request->input('phone');
        $counter = $request->input('counter');

        if($phone=='')
        {
            return 'пустой телефон';
        }
        $MadelineProto = $this->madAuth($phone);
        $added_users = 0;

        //return ($channel_all[$counter]);
        $channel_all = ChannelName::all();
        if($counter==count($channel_all))
        {
            return 'done';
        }
        $queryKey = ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z'];
        $rand_arr=[];
        for ($j = 0; $j < 100; $j++) {
            $rand_string_f='';
            for ($i = 0; $i < 2; $i++) {
                $rand_string = array_rand($queryKey);
                $rand_string_f .= $queryKey[$rand_string];
            }
            $rand_arr[] = $rand_string_f;
        }

        foreach ($rand_arr as $q) {
            $channels_ChannelParticipants = $MadelineProto->channels->getParticipants(['channel' => $channel_all[$counter]['channel_name'], 'filter' => ['_' => 'channelParticipantsSearch', 'q' => $q], 'offset' => 0, 'limit' => 1, 'hash' => 0,]);
            $num = ($channels_ChannelParticipants['count']) / 200;
            $num = ceil($num);

            for ($i = 0; $i < $num; $i++) {
                $channels_ChannelParticipants = $MadelineProto->channels->getParticipants(['channel' => $channel_all[$counter]['channel_name'], 'filter' => ['_' => 'channelParticipantsSearch', 'q' => $q], 'offset' => $i*200, 'limit' => 200, 'hash' => 0,]);
                foreach ($channels_ChannelParticipants['users'] as $users) {
                    if (!isset($users['username'])) {
                        $users['username'] = 'NO';
                    }

                    $flag_exist=0;

                    if (GeneralDev::where('user_id', $users['id'])->exists()) {
                        $flag_exist=1;
                    }
                    if($users['username'] != 'NO')
                    {
                        if (GeneralDev::where('username', $users['username'])->exists()) {
                            $flag_exist=1;
                        }
                    }
                    if($flag_exist==0)
                    {
                      $res=  GeneralDev::create([
                            'user_id' => $users['id'],
                            'username' => $users['username'],
                            'technology_id' => $channel_all[$counter]['technology_id']
                        ]);
                        $added_users++;
                    }
                }
            }
        }
        return $added_users;

    }

    public function get_channels()
    {
      return $channel_all = ChannelName::with('get_tech')->get();
    }


    public function add_old_users(Request $request)
    {
        $users_list = $request->input('add_old_users');
        preg_match_all('#@.+#', $users_list,  $matches);

        foreach ($matches[0] as $user)
        {
            $user = substr($user, 1);
                $user_in_db=GeneralDev::where('username','=',$user)->get();
               if($user_in_db->isEmpty())
               {
               $us= GeneralDev::create([
                       'user_id' => '0',
                       'username' => $user,
                       'technology_id' => '0',
                       'write' => 1
                   ]);
               }
               else
               {
                   GeneralDev::where('username', '=', $user)->update(['write' => 1]);
               }
        }
        return "ok";
    }

    public function invite_users(Request $request)
    {
        $phone = $request->input('phone');
        $group = $request->input('group');
        $first_reg = $request->input('first_reg');
        $technology_id = $request->input('technology_id');

            $MadelineProto = $this->madAuth($phone);

                    //проверяем на количество пользователей
                    try {
                        $channels_ChannelParticipants = $MadelineProto->channels->getParticipants(['channel' => $group, 'filter' => ['_' => 'channelParticipantsSearch', 'q' => 'a'], 'offset' => 0, 'limit' => 1, 'hash' => 0,]);
                    }
                    catch (\Exception $e) {
                        return response()->json([
                            'status' => 'success',
                            'success'   => 'no',
                            'message'   => 'ошибка авторизации',
                            'error'  => $e
                        ], 200);
                    }
                   $channelNumberOfUsers=$channels_ChannelParticipants['count'];
                    if($channelNumberOfUsers<200)
                    {
                    $dev_id = GeneralDev::where('technology_id', '=', $technology_id)->where('write', '=', '0')->first();
                    if ($dev_id === null) {
                        return response()->json([
                            'status' => 'success',
                            'message'   => $technology_id . ' ' . 'пустая',
                        ], 200);
                    }
                    if ($dev_id['username'] != 'NO') {
                        //если висит и не добавляет раскоменти строчку ниже, там наверное пользователь которого уже добавили или пригласили
//                          return dd($dev_id);
//                        return dd($group[$i].' '.$phones_start[$i][$s].' '.[$dev_id['username']].' '. $technology_id[$i]);
                        try {
                            $channels_ChannelParticipants = $MadelineProto->channels->inviteToChannel(['channel' => $group, 'users' => [$dev_id['username']]]);
                        }
                        catch (\Exception $e) {
                            GeneralDev::where('user_id', '=', $dev_id['user_id'])->update([
                                'write' => '1'
                            ]);
                            return response()->json([
                                'status' => 'success',
                                'success'   =>'no',
                                'message'   => $e,
                            ], 200);
                        }
                        GeneralDev::where('user_id', '=', $dev_id['user_id'])->update([
                            'write' => '1'
                        ]);
                        return response()->json([
                            'status' => 'success',
                            'message'   => 'ok',
                            'success'   => 'yes',
                            ], 200);
                         } else
                         {
                        GeneralDev::where('user_id', '=', $dev_id['user_id'])->update([
                            'write' => '1'
                        ]);
                        return response()->json([
                            'status' => 'success',
                            'success'   =>'no',
                            'message'   => 'no username',
                        ], 200);
                         }
                    }
                else {
                    return response()->json([
                        'status' =>'success',
                        'success'=>'no',
                        'message'=>'200 users',
                    ], 200);
                }
    }
}
//        $Updates = $MadelineProto->messages->addChatUser(['chat_id' => 'https://t.me/avtobaraholka_samara', 'user_id' => '@bobiksamara', 'fwd_limit' => '1', ]);
//  $channels_ChannelParticipants = $MadelineProto->channels->inviteToChannel(['channel' => 'https://t.me/avtobaraholka_samara', 'users' => ['@andrewf_163'] ]);
//            [                         'error'   => $global_error_counter.' '. 'errors global couner',
//'+79052759745','+79060426523','+79060603117','+79532031751'],
//            ['+79625741253','+79252514682','+79055176921','+79771510768'],
//            ['+79086887535','+79626348703','+79154325256','+79612614719'],
//            ['+79855217805','+79627807594','+79915813601','+79619231650'],
//смещение
//        $sm=0;
//        if($sm!=0)
//        {
//            $res= array_splice($phones_start, -$sm);
//            $res=array_reverse($res);
//            for($i = 0; $i < $sm; $i++)
//            {
//                array_unshift($phones_start, $res[$i]);
//            }
//        }
//                    return response()->json([
//                    'status' => 'success',
//                    'group'    => $channels_ChannelParticipants['count']['group'],
//                      ], 200);


//            $counter_sended = GroupCounter::where('number', '=', $phones_start[$i][$s])->where('group', '=', $group[$i])->get();

//            if ($counter_sended->isEmpty()) {
//                GroupCounter::create([
//                    'number' => $phones_start[$i][$s],
//                    'group' => $group[$i],
//                    'counter' => 0
//                ]);
//                $counter_sended = GroupCounter::where('number', '=', $phones_start[$i][$s])->where('group', '=', $group[$i])->get();
//            }
//
//                $local_counter = $counter_sended['0']['counter'];
//            $j = 0;
//            while ($j <= 9) {

//        for($i=0;$i<=2;$i++) {
//            for($s=0;$s<=3;$s++) {
//$local_counter++;
//GroupCounter::where('number', '=', $phones_start[$i][$s])->where('group', '=', $group[$i])->update(['counter' => $local_counter]);
//$j++;

//USER_CHANNELS_TOO_MUCH
//message	Object { rpc: "PEER_FLOOD", tlTrace: "<br>\nClient.php(249): \t__call(\"methodCallAsyncRead\",[\"channels.inviteToChannel\",{\"channel\":\"https:\\/\\/t.me\\/avto_rynok_moskva_2\",\"users\":[\"fractus_pro\"]},{\"apifactory\":true}])<br>\nAbstractAPIFactory.php(195):\tmethodCallAsyncRead(\"channels.inviteToChannel\",{\"channel\":\"https:\\/\\/t.me\\/avto_rynok_moskva_2\",\"users\":[\"fractus_pro\"]},{\"apifactory\":true})<br>\n__call_async(\"channels.inviteToChannel\",[{\"channel\":\"https:\\/\\/t.me\\/avto_rynok_moskva_2\",\"users\":[\"frac…ethodCallAsyncRead\",[\"channels.inviteToChannel\",{\"channel\":\"https:\\/\\/t.me\\/avto_rynok_moskva_2\",\"users\":[\"fractus_pro\"]},{\"apifactory\":true}]])\nclientRequest()\n\nPrevious TL trace:\n['channels.inviteToChannel']\nResponseHandler.php(194):\thandleRpcError()\nResponseHandler.php(73):\thandleResponse()\nDriver.php(119): \thandleMessages()\nDriver.php(72): \ttick()\nLoop.php(95): \trun()\nTools.php(296): \trun()\nentry.php(110): \twait()\nentry.php(134): \t{closure}()", localized: "You are spamreported, you can't do this", … }
