@extends('layouts.app')

@section('content')
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
                            <textarea class="form-control" id="content"></textarea>
                            <label class="error-label">asdsd</label>
                            <button id="submit_post" type="submit" class="btn btn-primary pull-right" style="margin-top:10px;">
                                POST
                            </button>
                        </div>
                    </div>
                </div>

                <div class="panel-body" id="post-container">
                    {{Auth::user()->posts()->count() < 1 ? 'You, currently, have no posts' : ''}}                
    
                    @foreach(\App\Post::orderBy('created_at','desc')->get() as $post)
                        <div class="post">
                            <h3>{{$post->content}}</h3>
                            <span style="color:#00a3ff; cursor: pointer;" onclick="updateLike({{$post->id}},this)">{{$post->likes()->where('user_id',\Auth::user()->id)->count() > 0 ? 'Unlike':'Like'}} ({{$post->likes()->count()}})</span> | 
                            @if(\Auth::user()->id == $post->user_id)
                            <span style="color:rgb(114, 104, 222); cursor: pointer;" onclick="_update({{$post->id}})">Update</span> | <span style="color:#de6868; cursor: pointer;" onclick="_delete({{$post->id}})">Delete</span> | 
                            @endif
                            <span style="color:#cecece;">created {{$post->created_at}}</span> | <span style="color:#cecece;">by {{$post->author->name}}</span>
                            <hr>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    <div id="post-modal" class="modal fade" role="dialog">
        <div class="modal-dialog" style="width: 400px">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Update Post</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group" style="padding:10px;">
                                <textarea class="form-control content"></textarea>
                                <label class="error-label">asdsd</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary save" data-invoiceid="">Submit</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        $(document).ready(function(){
            var pendingpost = false;
            $('textarea,input').change(function(){
                $(this).parent().removeClass('has-error');
            });
            $('#submit_post').click(function(){
                if(!pendingpost){
                    pendingpost = true;
                    $.ajax({
                        url:'{{route("json_create_post")}}',
                        type:'POST',
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        data: { content: $('#content').val()},
                        success:function(data){
                            // 
                            pendingpost = false
                            if(data.status == "success"){
                                $('#post-container').html('');
                                $('#content').val('')
                                for(var i =0 ; i < data.posts.length; i++){
                                    addPost(data.posts[i]);
                                }
                            }
                            else
                            {
                                $.each(data.messages,function(k,v){
                                    // 
                                    $('#'+k).parent().addClass('has-error');
                                    $('#'+k).parent().find('.error-label').html(v[0]);
                                });
                            }
                        }
                    });
                }
            });

            $('#post-modal .save').click(function(){
                if(!pendingpost){
                    pendingpost = true;
                    $.ajax({
                        url:'{{route("json_update_post")}}',
                        type:'POST',
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        data: { content: $('#post-modal .content').val(), id : $('#post-modal').data('data').id},
                        success:function(data){
                            // 
                            pendingpost = false
                            if(data.status == "success"){
                                $('#post-container').html('');
                                for(var i =0 ; i < data.posts.length; i++){
                                    addPost(data.posts[i]);
                                }

                                $('#post-modal').modal('hide');
                            }
                            else
                            {
                                $.each(data.messages,function(k,v){
                                    // 
                                    $('#post-modal .'+k).parent().addClass('has-error');
                                    $('#post-modal .'+k).parent().find('.error-label').html(v[0]);
                                });
                            }
                        }
                    });
                }
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

        function _update(id){
            $('#post-modal .form-group').removeClass('has-error');
            $.ajax({
                url:"{{route('json_get_post')}}",
                type:"GET",
                data:{id:id},
                success:function(data){
                    var modal = $('#post-modal');
                    modal.modal('show');
                    modal.find('.content').val(data.content);
                    modal.data('data',data);
                }
            });
        }

        function updateLike(id,element){
            $.ajax({
                url:"{{route('json_update_like')}}",
                type:'POST',
                data:{
                    post_id:id
                },
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                success:function(data){
                    if(data.status == 'success'){
                        if(data.message == "liked"){
                            $(element).html('Unlike ('+data.likes+')');
                        }
                        else
                        {
                            $(element).html('Like ('+data.likes+')');
                        }
                    }
                }
            });
        }

        function addPost(data){
            var liked = $.grep(data.likes,function(e){return e.user_id == {{\Auth::user()->id}};});
            var html = '<div class="post"><h3>'+data.content+'</h3> <span style="color:#00a3ff; cursor: pointer;" onclick="updateLike('+data.id+',this)">'+(liked.length > 0?'Unlike':'Like')+' ('+data.likes.length+')</span> | ';

            if(data.user_id == {{\Auth::user()->id}})
            {
                html+= '<span style="color:rgb(114, 104, 222); cursor: pointer;" onclick="_update('+data.id+')">Update</span> | <span style="color:#de6868; cursor: pointer;" onclick="_delete('+data.id+')">Delete</span> | ';
            }

            html+= '<span style="color:#cecece;">created '+data.created_at+'</span> | <span style="color:#cecece;">by '+data.author.name+'</span><hr></div>';

            $('#post-container').append(html);
        }
    </script>
</div>
@endsection
