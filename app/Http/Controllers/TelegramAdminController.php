<?php

namespace App\Http\Controllers;

use App\Models\ChannelName;
use App\Models\GroupNumber;
use App\Models\TechList;
use App\Models\TechnologyText;
use App\Models\TelegramPhones;
use Illuminate\Http\Request;

class TelegramAdminController extends Controller
{
    public function get_technology_text(Request $request)
    {
        $technology = $request->input('technology');
        if($technology=='')
        {
            return 'error';
        }
        $text_technology = TechnologyText::where('technology_id','=',$technology) ->value('text_technology');
        return $text_technology;
    }
    public function save_technology_text(Request $request)
    {
        $technology = $request->input('technology');
        $technology_text = $request->input('technology_text');
        if(($technology=='')||($technology_text==''))
        {
            return 'error';
        }
        TechnologyText::where('technology_id','=',$technology)->update([
            'text_technology' =>$technology_text
        ]);
        return 'ok';
    }
    public function new_save_technology_text(Request $request)
    {
        $new_technology = $request->input('new_technology');
        $new_text_technology = $request->input('new_text_technology');
        if(($new_technology=='')||($new_text_technology==''))
        {
            return 'пустое поле технлогии или текста технологии';
        }
        if (TechList::where('technology', $new_technology)->exists()) {
            return 'технология уже существует';
        }
        else
        {
           $res=TechList::create([
                'technology' => $new_technology
            ]);
            TechnologyText::create([
                'technology_id' => $res['id'],
                'text_technology' =>$new_text_technology
            ]);
            return 'ok';
        }

    }

    public function add_group_name(Request $request)
    {
        $group_name = $request->input('group_name');
        if(($group_name==''))
        {
            return 'пустое поле имени группы';
        }
        if (GroupNumber::where('group_name', $group_name)->exists()) {
            return 'имя группы уже существует';
        }
        else
        {
            GroupNumber::create([
                'group_name' => $group_name,
            ]);
            return 'ok';
        }
    }

    public function add_phone_to_group(Request $request)
    {
        $phone = $request->input('phone');
        $group_id = $request->input('group_id');
        TelegramPhones::where('phone','=',$phone)->update([
            'group_id' =>$group_id
        ]);
    }

    public function get_groups()
    {
        $group_list = GroupNumber::all();
        return $group_list;
    }

    public function get_group_list(Request $request)
    {
        $group_id = $request->input('group_id');
        $group_list = TelegramPhones::where('group_id','=',$group_id)->get();
        return $group_list;
    }
    public function delete_number(Request $request)
    {
        $phone = $request->input('phone');
        TelegramPhones::where('phone','=',$phone)->update([
            'group_id' =>'0'
        ]);
    }
    public function add_channel(Request $request)
    {

        $technology = $request->input('technology');
        $channel= $request->input('channel');
        if(($technology=='')||($channel==''))
        {
            return 'пустое поле канала или технологии';
        }
        if (ChannelName::where('channel_name', $channel)->exists()) {
            return 'канал уже есть в бд';
        }
        else
        {
            ChannelName::create([
                'channel_name' => $channel,
                'technology_id' => $technology,
            ]);
            return 'ok';
        }
    }
}
