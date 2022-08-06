<?php

namespace App\Http\Controllers;
use App\Models\Posts;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PostsController extends Controller
{
    public function get_posts()
    {
        //$posts = Posts::all()->sortByDesc('anchor')->sortByDesc('counter');
        $posts=DB::table('posts')->orderBy('anchor', 'DESC')->orderBy('counter', 'DESC')->get();
        return $posts;
    }
    public function add_post(Request $request)
    {
       $post=Posts::create([
            'title' => $request->input('new_post_header'),
            'description' => $request->input('new_post_text'),
            'anchor' => '0',
            'counter' =>0
        ]);
       return $post;
    }
    public function get_post_info(Request $request)
    {
        $post = Posts::where('id', '=', $request->input('id'))
            ->get();
        return $post;
    }
    public function increment_counter(Request $request)
    {
        Posts::where('id', '=', $request->input('id'))
            ->increment('counter');
    }
    public function change_anchor(Request $request)
    {
        Posts::where('anchor', '=', 1)->update(['anchor' =>0]);
        Posts::where('id', '=', $request->input('id'))->update(['anchor' =>1]);
    }
}
