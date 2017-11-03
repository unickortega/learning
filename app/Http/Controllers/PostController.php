<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    
    public function json_create_post(Request $request){
        $validator = Validator::make($request->all(),[
            'content'=>'required'
            ]);

        if($validator->passes())
        {
            $post = new \App\Post;
            $post->user_id = \Auth::user()->id;
            $post->content = $request->content;
            $post->save();
        }
        else
        {
            return ['status'=>'fail','messages'=>$validator->messages()];
        }

        return ['status'=>'success','posts'=>\App\Post::orderBy('created_at','desc')->get()->load('author')->load('likes')];
    }

    public function json_update_post(Request $request){
        $validator = Validator::make($request->all(),[
            'content'=>'required',
            'id'=>'required'
            ]);

        if($validator->passes())
        {
            $post = \App\Post::find($request->id);

            if($post->user_id != \Auth::user()->id)
                return ['status'=>'fail','messages'=>["content"=>["This post is not yours"]]];

            $post->content = $request->content;
            $post->save();
        }
        else
        {
            return ['status'=>'fail','messages'=>$validator->messages()];
        }

        return ['status'=>'success','posts'=>\App\Post::orderBy('created_at','desc')->get()->load('author')->load('likes')];
    }

    public function json_delete_post(Request $request){
        \App\Post::find($request->id)->delete();

        return ['status'=>'deleted','posts'=>\Auth::user()->posts()->orderBy('created_at','desc')->get()->load('author')->load('likes')];
    }

    public function json_get_post(Request $request){
        return \App\Post::find($request->id);
    }

    public function json_update_like(Request $request){
        $post = \App\Post::find($request->post_id);

        if($post->likes()->where('user_id',\Auth::user()->id)->count() > 0){
            \DB::table('likes')->where('post_id',$request->post_id)->where('user_id',\Auth::user()->id)->delete();
            return ['status'=>'success', 'message'=>'unliked', 'likes'=> $post->likes()->count()];
        }
        else
        {
            $like = new \App\Like;
            $like->user_id = \Auth::user()->id;
            $like->post_id = $request->post_id;
            $like->save();

            return ['status'=>'success', 'message'=>'liked', 'likes'=> $post->likes()->count()];
        }

        return ['status'=>'success'];
    }
}
