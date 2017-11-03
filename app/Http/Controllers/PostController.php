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

        return ['status'=>'success','posts'=>\Auth::user()->posts()->orderBy('created_at','desc')->get()];
    }

    public function json_delete_post(Request $request){
        \App\Post::find($request->id)->delete();

        return ['status'=>'deleted','posts'=>\Auth::user()->posts()->orderBy('created_at','desc')->get()];
    }
}
