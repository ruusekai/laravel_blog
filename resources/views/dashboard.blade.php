@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Dashboard</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                    <a href="/posts/create" class="btn btn-primary">Create Post</a>
                    @if(count($posts) > 0)
                        <h3>Your Blog Posts</h3>
                        <table class ="table table-striped">
                            <tr>
                                <td>Title</td>
                                <td></td>
                                <td></td>
                            </tr>
                            @foreach($posts as $post)
                                <tr>
                                    <td>{{$post->title}}</td>
                                    <td><a href="/posts/{{$post->id}}/edit" class="btn btn-primary">Edit</a></td>
                                    <td>{!! form($form, $formOptions = [                'url' => route('posts.destroy',$post->id)]) !!}</td>
                                </tr>
                            @endforeach
                        </table>
                        {{$posts->links()}}
                    @else
                        <p>You Dont have a post</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
