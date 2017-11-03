@extends('layouts.app')

@section('content')
<style type="text/css">
    .post{
        padding: 10px;
        border: 1px solid #cecece;
        background: white;
    }
</style>
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Dashboard</div>

                <div class="panel-body">
                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif

                    You are logged in!
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading">Posts</div>
                
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group" style="padding:10px;">
                            <textarea class="form-control" id="text-area"></textarea>
                            <button id="submit_post" type="submit" class="btn btn-primary pull-right" style="margin-top:10px;">
                                POST
                            </button>
                        </div>
                    </div>
                </div>

                <div class="panel-body" id="post-container">
                    {{Auth::user()->posts()->count() < 1 ? 'You, currently, have no posts' : ''}}                
    
                    @foreach(Auth::user()->posts()->orderBy('created_at','desc')->get() as $post)
                        <div class="post">
                            <h3>{{$post->content}}</h3>
                            <span style="color:#de6868; cursor: pointer;" onclick="_delete({{$post->id}})">DELETE</span> | <span style="color:#cecece;">created {{$post->created_at}}</span>
                            <hr>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        $(document).ready(function(){
            $('#submit_post').click(function(){
                $.ajax({
                    url:'{{route("json_create_post")}}',
                    type:'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    data: { content: $('#text-area').val()},
                    success:function(data){
                        // 
                        if(data.status == "success"){
                            $('#post-container').html('');
                            for(var i =0 ; i < data.posts.length; i++){
                                addPost(data.posts[i]);
                            }
                        }
                    }
                });
            });
        });

        function _delete(id){
            $.ajax({
                    url:'{{route("json_delete_post")}}',
                    type:'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    data: { id: id},
                    success:function(data){
                        // 
                        if(data.status == "deleted"){
                            $('#post-container').html('');
                            for(var i =0 ; i < data.posts.length; i++){
                                addPost(data.posts[i]);
                            }
                        }
                    }
                });
        }

        function addPost(data){
                $('#post-container').append('<div class="post"><h3>'+data.content+'</h3><span style="color:#cecece;">created '+data.created_at+'</span><hr></div>');
            }
    </script>
</div>
@endsection
