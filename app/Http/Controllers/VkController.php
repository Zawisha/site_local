<?php

namespace App\Http\Controllers;

use App\Models\OldPostVk;
use App\Models\VKGroups;
use App\Models\VKMessageText;
use App\Models\VKPosts;
use App\Models\VkSearchGroup;
use App\Models\VkTimeTelegram;
use App\Models\VKUsersSend;
use App\Models\VKMessageSecond;
use App\Models\VkWordsSearch;
use ATehnix\VkClient\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Traits\AuthMaddellineTrait;
class VkController extends Controller
{

    use AuthMaddellineTrait;
    public function send_to_telegram(Request $request)
    {
        //обязательно перезагрузка апача
        set_time_limit(20);
//        Storage::append('/logVK.txt', '1');
        //очищаем папку
        $dir = './vk/';
        foreach(glob($dir.'*.*') as $v){
            unlink($v);
        }
        $post_id = $request->input('post_id');
        echo preg_match_all('#w=wall-([0-9]+)+_([0-9]+)+#', $post_id,  $matches);
        $group_vk_number=$matches[1][0];
        $post_vk_number=$matches[2][0];
        $telegram_channels_list = VKGroups::where('vk_group_id','=',$group_vk_number)->where('in_work','=','1')->get('telegram_channel');
        //получаем пост
        try {
            $xml = json_decode(file_get_contents("https://api.vk.com/method/wall.getById?posts=-" . $group_vk_number . '_' . $post_vk_number . "&v=5.131&access_token=ebb4e240a677264ecf6de2ecfc9ab45a83a90c3773f309e92677ab19c624d132e9abe70e44d09a41cba59"));
        }
        catch (\Throwable $e)
        {
            return response()->json([
                'status' => 'not success',
                'todo'    => 'get post error',
            ], 200);
        }
        //получаем текст
        $post_text=$xml->response['0']->text;
        //$post_text=trim($post_text);
        $post_text='Хендай тибурон
Мотор 2.7, полностью был обслужен три-четыре месяца назад (масло поменяли на этой неделе с фильтрами) коробка отлично, не пинается, переключает плавно без ударов. Тихий выхлоп, перфорируемые тормозные диски с теплоотводами
';
//        $post_text1='
//Хендай тибурон
//Мотор 2.7, полностью был обслужен три-четыре месяца назад (масло поменяли на этой неделе с фильтрами) коробка отлично, не пинается, переключает плавно без ударов. Тихий выхлоп, перфорируемые тормозные диски с теплоотводами
//Возможен обмен, на авто дороже, возможна доплата с моей стороны
//Авто на полном ходу , доки чистые , никаких штрафов и запретов
//По плюшкам: автозапуск , 2din магнитола sonу с камерой заднего вида
//С учетом проблем не будет
//Небольшие проблемы по лкп, но ничего критичного. Дыр гнили нет и не ожидается
//
//Торг только у капота (по телефону и смс не торгуюсь)
//
//333.000р
//Тверь';
//        Storage::append('/logVK.txt', $post_text);
//        Storage::append('/logVK.txt', $post_text1);
        //сохраняем фото
        $photo_urls=[];
        foreach ($xml->response['0']->attachments as $attach)
        {
            //получаем номер самого большого формата фотографии
            try {
                $sizes_arr_counter = (count($attach->photo->sizes) - 1);
            }
            catch (\Throwable $e)
            {
                return response()->json([
                    'status' => 'not success',
                    'todo'    => 'get size photo error',
                ], 200);
            }
            //получаем фотку
            try {
                $photo_urls[] = $attach->photo->sizes[$sizes_arr_counter]->url;
            }
            catch (\Throwable $e)
            {
                return response()->json([
                    'status' => 'not success',
                    'todo'    => 'get photo error',
                ], 200);
            }
        }

        foreach($photo_urls as $key=>$url)
        {
        $path = './vk/'.$key.'.jpg';
            try {
                file_put_contents($path, file_get_contents($url));
            }
            catch (\Throwable $e)
            {
                return response()->json([
                    'status' => 'not success',
                    'todo'    => 'save photo error',
                ], 200);
            }
        }

        //работа с телеграм
        $phone = "+375298684190";
        $MadelineProto = $this->madAuth($phone);
        $inputSingleMediaGloabl=[];
        foreach($photo_urls as $key=>$url)
        {
            $result =  $MadelineProto->messages->uploadMedia([
                'media' => [
                    '_' => 'inputMediaUploadedPhoto',
                    'file' => './vk/'.$key.'.jpg'
                ],
            ]);
            $id=($result['photo']['id']);
            $access_hash=($result['photo']['access_hash']);
            $file_reference=($result['photo']['file_reference']);

            $inputMediaUploadedPhoto = ['_' => 'inputPhoto', 'id' => $id, 'access_hash' => $access_hash, 'file_reference' => $file_reference];
            $inputMediaPhoto = ['_' => 'inputMediaPhoto', 'id' => $inputMediaUploadedPhoto];
            if($key==0)
            {
                $inputSingleMedia = ['_' => 'inputSingleMedia', 'media' =>$inputMediaPhoto ,'message' => $post_text];
            }
            else
            {
                $inputSingleMedia = ['_' => 'inputSingleMedia', 'media' =>$inputMediaPhoto ];
            }
            $inputSingleMediaGlobal[]=$inputSingleMedia;
        }
            foreach ($telegram_channels_list as $telegram_channel)
            {
                $old_post_isset=OldPostVk::where('post','=',$group_vk_number.'_'.$post_vk_number)->where('telegram_channel','=',$telegram_channel['telegram_channel'])->get();
                if ($old_post_isset->isEmpty()) {
                    $schedule_date= VkTimeTelegram::where('channel','=',$telegram_channel['telegram_channel'])->get('time');
                    if($schedule_date[0]['time']=='0')
                    {

                        $schedule_date=time()+3600;
                        VkTimeTelegram::where('channel','=',$telegram_channel['telegram_channel'])->
                        update([
                            'time' => $schedule_date,
                        ]);
                    }
                    else
                    {

                        if(($schedule_date[0]['time'])<(time()))
                        {
                            $schedule_date=time()+3600;
                        }
                        else
                        {
                            $schedule_date=$schedule_date[0]['time']+3600;
                        }
                        VkTimeTelegram::where('channel','=',$telegram_channel['telegram_channel'])->
                        update([
                            'time' => $schedule_date,
                        ]);
                    }
                    try {

                    $MadelineProto->messages->sendMultiMedia([
                            'peer' => $telegram_channel['telegram_channel'],
                            'multi_media' => $inputSingleMediaGlobal,
                            'schedule_date'=>$schedule_date
                        ]);
                    return response()->json([
                            'status' => 'success',
                            'todo'    => 'success',
                        ], 200);
                    }
                    catch (\Throwable $e)
                    {

                        OldPostVk::create([
                            'post' => $group_vk_number.'_'.$post_vk_number,
                            'telegram_channel' => $telegram_channel['telegram_channel'],
                        ]);
                        return response()->json([
                            'status' => 'not success',
                            'todo'    => 'send telegram error',
                        ], 200);
                    }
                    return response()->json([
                        'status' => 'success',
                        'todo'    => 'success',
                    ], 200);
                }

            }
        return response()->json([
                    'status' => 'success',
                    'todo'    => 'success',
                ], 200);
    }
    public function add_vk_group_to_channel(Request $request)
    {
        $vk_group = trim($request->input('vk_group'));
        $telegram_channel = trim($request->input('telegram_channel'));
        if(($vk_group=='')||($telegram_channel==''))
        {
            return response()->json([
                'status' => 'success',
                'message'    => 'Группа или технология пустая',
            ], 200);
        }

        $isset_type = VKGroups::where('vk_group_id','=',$vk_group)->where('telegram_channel','=',$telegram_channel) ->first();

        if($isset_type)
        {
            return response()->json([
                'status' => 'success',
                'message'    => 'Такая связка группа канал уже есть',
                'todo'    => 'clear_field',
            ], 200);
        }
        VKGroups::create([
            'vk_group_id' => $vk_group,
            'telegram_channel' => $telegram_channel,
        ]);
        $old_group_isset=VkTimeTelegram::where('channel','=',$telegram_channel)->get();
        if ($old_group_isset->isEmpty())
        {
            VkTimeTelegram::create([
                'channel' => $telegram_channel,
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message'    => 'Группа успешно добавлена',
            'todo'    => 'clear_field',
        ], 200);
    }
    public function send_inv_message_second(Request $request)
    {
        $number = $request->input('number');
        $token = $request->input('token');
        try {
            $xml = json_decode(file_get_contents("https://api.vk.com/method/wall.get?user_id=12254923&v=5.131&access_token=".$token));
            }
            catch (\Throwable $e)
            {
                return response()->json([
                    'message' => 'Проблемы с аккаунтом',
                    'number'   => $number,
                ], 200);
            }
            if(isset($xml->error))
            {
                return response()->json([
                    'message' => 'Проблемы с аккаунтом',
                    'number'   => $number,
                ], 200);
            }

        $number_to_message = VKUsersSend::where('message_number','=',1)->where('is_closed','=',0)->where('vk_number','=',$number)->get();
        foreach($number_to_message as $numb)
        {
        //user id
        $user_id=$numb['user_id'];
        $local_counter_error=0;
        //возвращаем диалог
        try {
            $xml = json_decode(file_get_contents("https://api.vk.com/method/messages.getHistory?user_id=".$user_id."&offset=0&rev=1&count=100&v=5.131&access_token=".$token));
            }
            catch (\Throwable $e)
            {
                return $e;
                $local_counter_error++;
            }
            $flag_resp=0;
            $resp_text='';
            foreach($xml->response->items as $item)
            {
                if($item->out==0)
                {
                    $resp_text.=' '.$item->text;
                    $flag_resp=1;
                }
            }
            //если есть хоть один ответ
            if($flag_resp==1)
            {

            $resp_text = mb_strtolower($resp_text);

            //текст отправки
          $message_second = VkMessageSecond::where('id_message','=',$numb['technology_id'])->first();
          //слова поиска
          $vk_words=VkWordsSearch::get();
          $flag=0;

            foreach ($vk_words as $word) {
                $reg_result = preg_match('#' . $word['search_word'] . '#', $resp_text);
                if($reg_result==1)
                    {
                        $mes=urlencode($message_second['message_text']);
                         $rand=rand(1,100000);
                        try {

                        $xml = json_decode(file_get_contents("https://api.vk.com/method/messages.send?user_id=".$user_id."&message=".$mes."&random_id=".$rand."&v=5.131&access_token=".$token));
                        }
                        catch (\Throwable $e)
                        {

                        }
                        sleep(1);
                        VKUsersSend::where('user_id',$user_id)->
                        update([
                            'message_number' => '2',
                            'is_closed' => 1,
                        ]);
                        $flag=1;
                        break;
                    }
                 }
                 //ответ есть но нету слова
                 if($flag==0)
                 {
                    VKUsersSend::where('user_id',$user_id)->
                    update([
                        'is_closed' => 1,
                    ]);
                 }
        }
        else
        {
            $time_left= date(time())-strtotime($numb['updated_at']);
            if($time_left>259200)
            {
             VKUsersSend::where('user_id',$user_id)->
             update([
                 'is_closed' => 1,
             ]);
            }
        }
    }
        return response()->json([
            'message' => 'рассылка ответов закончена',
            'number'   => $number,
        ], 200);
    }
    public function send_inv_message(Request $request)
    {

        $number = $request->input('number');
        $token = $request->input('token');
        try {
            $xml = json_decode(file_get_contents("https://api.vk.com/method/wall.get?user_id=12254923&v=5.131&access_token=".$token));
            }
            catch (\Throwable $e)
            {
                return response()->json([
                    'message' => 'Проблемы с аккаунтом catch',
                    'number'   => $number,
                ], 200);
            }
            // if(!isset($xml->response)->count!=1)
        if(!isset($xml->response))
        {
            return response()->json([
                'message' => 'Проблемы с аккаунтом',
                'number'   => $number,
            ], 200);
        }
        $poriadok_arr=['24'];
        $poriadok_numb=0;
        $local_counter_error=0;
        $local_counter_success=0;
        $error_list=[];
        for ( $i = 0; $i <10;) {
            sleep(1);
        $tech=$poriadok_arr[$poriadok_numb];
        $number_to_message = VKUsersSend::where('technology_id','=',$tech)->where('message_number','=',0)->where('is_closed','=',0)->first();

        if($number_to_message==null)
        {
            $poriadok_numb++;
            if(count($poriadok_arr)==$poriadok_numb)
            {
                return response()->json([
                    'message' => 'Закончились аккаунты для рассылки(тем кому отправлять сообщения)',
                ], 200);
            }
            else
            {
                continue;
            }

        }
    $numb=$local_counter_success+1;
       VKUsersSend::where('user_id','=',$number_to_message['user_id'])->update([
           'message_number' => '1',
           'vk_number' => $number,
       ]);
        $message = VKMessageText::where('id_message','=',$tech)->where('numb','=',$numb)->get('message_text');
       // return $message[0]['message_text'];
        $mes=$message[0]['message_text'];
        $mes=urlencode($mes);

        $rand=rand(1,100000);
        try {
        $xml = json_decode(file_get_contents("https://api.vk.com/method/messages.send?user_id=".$number_to_message['user_id']."&message=".$mes."&random_id=".$rand."&v=5.131&access_token=".$token));
        }
        catch (\Throwable $e)
        {
            $local_counter_error++;
        }

        if (isset($xml->response)&& is_numeric($xml->response)) {
            $i++;
            $local_counter_success++;
        }
        else
        {

          if((isset($xml->error->error_msg))&&($xml->error->error_msg=='Access denied: no access to call this method'))
          {
            VKUsersSend::where('user_id','=',$number_to_message['user_id'])->update([
                'message_number' => '0',
                'vk_number' =>null,
            ]);
            return response()->json([
                'message' => 'Рассылка с номера '.$number.' невозможна, нет прав у токена',

            ], 200);
          }
            array_push($error_list,$xml);
            $local_counter_error++;
        }


        if($local_counter_error>25)
        {
            break;
        }
        //for success
    }

    return response()->json([
        'message' => 'Рассылка с номера '.$number.' окончена',
        'success_count' => $local_counter_success,
        'error_count'   => $local_counter_error,
        'list'   => $error_list,
    ], 200);

    }

    public function getVKusers(Request $request)
    {
//        $post_id = $request->input('post_id');
//        echo preg_match_all('#w=wall-([0-9]+)+_([0-9]+)+#', $post_id,  $matches);
//        $group_vk_number=$matches[1][0];
//        $post_vk_number=$matches[2][0];

        $group_id = $request->input('group_id');
        $technology_id = $request->input('technology_id');
            try {
                $xml = json_decode(file_get_contents("https://api.vk.com/method/wall.get?owner_id=-".$group_id."&count=100&v=5.131&access_token=ebb4e240a677264ecf6de2ecfc9ab45a83a90c3773f309e92677ab19c624d132e9abe70e44d09a41cba59"));
            }
            catch (\Throwable $e)
            {
                return response()->json([
                    'status' => 'ошибка в получении списка постов группы',
                    'error'    =>  $e,
                    'group'    =>  $group_id,
                ], 200);
            }
            sleep(1);
            //убираем закреплённый пост
        $posts=[];
        foreach ($xml->response->items as $post)
        {
            if(!isset($post->is_pinned))
            {
                array_unshift($posts, $post);
            }
        }
      $group_post_last=VKPosts::where('group_id',$group_id)->get('last_post');

        //получили список постов и теперь перебираем их по одному
            foreach ($posts as $post)
            {

                //закреплённый пост не берём и смотрим чтобы пост был новее чем тот что я уже брал
                if((!isset($post->is_pinned))&&($post->id>$group_post_last[0]->last_post))
                {

                    //кто запостил
                    if(isset($post->signer_id))
                    {
                    VKUsersSend::firstOrCreate(
                    //по какой строке проверяем
                        [ 'user_id' => $post->signer_id],
                        //что добавляем
                        [ 'technology_id' => $technology_id, 'message_number' => '0', 'is_closed' => '0' ]
                    );
                    }

//                    получем лайки с поста
                    try {
                        $likes_list = json_decode(file_get_contents("https://api.vk.com/method/likes.getList?type=post&owner_id=-".$group_id."&item_id=".$post->id."&v=5.131&access_token=ebb4e240a677264ecf6de2ecfc9ab45a83a90c3773f309e92677ab19c624d132e9abe70e44d09a41cba59"));
                        foreach ($likes_list->response->items as $like)
                        {
                            //id того кто поставил лайк
                            VKUsersSend::firstOrCreate(
                            //по какой строке проверяем
                                [ 'user_id' => $like],
                                //что добавляем
                                [ 'technology_id' => $technology_id, 'message_number' => '0', 'is_closed' => '0' ]
                            );
                        }
                    }
                    catch (\Throwable $e)
                    {
                        return response()->json([
                            'status' => 'not success',
                            'todo'    => 'get post error',
                            'todo'    => $e,
                        ], 200);
                    }
                    sleep(1);
               //     получем комментарии с поста
                    try {
                        $comment_list = json_decode(file_get_contents("https://api.vk.com/method/wall.getComments?owner_id=-".$group_id."&post_id=".$post->id."&count=100&v=5.131&access_token=ebb4e240a677264ecf6de2ecfc9ab45a83a90c3773f309e92677ab19c624d132e9abe70e44d09a41cba59"));
                    }
                    catch (\Throwable $e)
                    {
                        return response()->json([
                            'status' => 'not success',
                            'todo'    => 'get post error1',
                        ], 200);
                    }
                    if(isset($comment_list->response))
                    {
                    foreach ($comment_list->response->items as $comment)
                    {
                        VKUsersSend::firstOrCreate(
                        //по какой строке проверяем
                            [ 'user_id' => $comment->from_id],
                            //что добавляем
                            [ 'technology_id' => $technology_id, 'message_number' => '0', 'is_closed' => '0' ]
                        );
                    }
                    }
                    VKPosts::where('group_id',$group_id)->
                    update([
                        'last_post' => $post->id,
                    ]);
                    VkSearchGroup::where('group_id',$group_id)->
                    update([
                        'last_post' => $post->id,
                    ]);
                }
            }
      $count_users= VKUsersSend::where('technology_id',$technology_id)->where('message_number','0')->where('is_closed','0')->count();
        return response()->json([
            'message'    =>  'собрано',
            'count'    =>  $count_users,
        ], 200);


//                try {
//            $xml = json_decode(file_get_contents("https://api.vk.com/method/wall.get?owner_id=-122606077&count=10&v=5.131&access_token=ebb4e240a677264ecf6de2ecfc9ab45a83a90c3773f309e92677ab19c624d132e9abe70e44d09a41cba59"));
//
//        }
//                catch (\Throwable $e)
//
//        {
//            return response()->json([
//                'status' => 'not success',
//                'todo'    =>  $e,
//            ], 200);
//        }
//        return response()->json([
//            'status' => 'not success',
//            'todo'    =>  $xml,
//        ], 200);
       //получить один пост
//        try {
//            $xml = json_decode(file_get_contents("https://api.vk.com/method/wall.getById?posts=-" . $group_vk_number . '_' . $post_vk_number . "&v=5.131&access_token=ebb4e240a677264ecf6de2ecfc9ab45a83a90c3773f309e92677ab19c624d132e9abe70e44d09a41cba59"));
//
//        }
//        catch (\Throwable $e)
//        {
//            return response()->json([
//                'status' => 'not success',
//                'todo'    => 'get post error',
//            ], 200);
//        }
//        //получил id того кто отправил пост
//        return response()->json([
//            'status' => 'not success',
//            'todo'    => 'get post error',
//            'res'    => $post_text=$xml->response['0']->signer_id,
//        ], 200);
        //конец получить один пост

    }
    public function get_list_of_search_group(Request $request)
    {
        $groups=VkSearchGroup::all();
        return response()->json([
            'status' => 'success',
            'groups'    => $groups,
        ], 201);

    }
    public function showVKusers(Request $request)
    {
        $technology_id = $request->input('technology_id');
        $user_list=VKUsersSend::where('technology_id',$technology_id)->where('is_closed','0')->get('user_id');
        return response()->json([
            'message'    =>  'собрано',
            'user_list'    =>  $user_list,
        ], 200);
    }
    public function get_vk_text_buy(Request $request)
    {
        $user_list=VKMessageText::where('id','>',0)->get('message_text');
        return response()->json([
            'message'    =>  'собрано',
            'messages'    =>  $user_list,
        ], 200);
    }
    public function deleteshowVKusers(Request $request)
    {
        $user_id = $request->input('user_id');
        VKUsersSend::where('user_id',$user_id)->
        update([
            'is_closed' => 1,
        ]);
        return response()->json([
            'message'    =>  'сделано',
        ], 200);
    }
}
