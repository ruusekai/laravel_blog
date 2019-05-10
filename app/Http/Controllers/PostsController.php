<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
//for deleting images
use Illuminate\Support\Facades\Storage;
use App\Post;
use App\User;
//for directly using SQL
use DB;
use Kris\LaravelFormBuilder\FormBuilder;

class PostsController extends Controller
{    /**
    * Create a new controller instance.
    *
    * @return void
    */
   public function __construct()
   {
       $this->middleware('auth', ['except' => ['index', 'show']]);
   }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //$post = Post::all();
        //$post = Post::where('title', 'Post Two')->get();        
        //WITHOUT ELEQUENT
            //$posts = DB::select('SELECT * FROM posts');
        //$posts =  Post::orderBy('title','asc')->take(1)->get();
 //       $user_id = auth()->user()->id;
 //       $user = User::find($usesr_id);
//        $posts =  $user->posts->paginate(1);
        $posts =  Post::orderBy('created_at','desc')->paginate(5);
        return view('posts.index')->with('posts',$posts);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function create(FormBuilder $formBuilder)
    {
        $form = $formBuilder->create('App\Forms\BlogForm', [
            'method' => 'POST',
            'url' => route('posts.store'),
            'files' => true
        ]);

        return view('posts.create', compact('form'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required',
            'article-ckeditor' => 'required',
            'cover_image' => 'image|nullable|max:1999'
        ]);
        // Handle File Upload
        if($request->hasFile('cover_image')){
            // Get filename with the extention
            $filenameWithExt = $request -> file('cover_image')->getClientOriginalName();
            // Get just filename
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            // Get just extension
            $extension = $request->file('cover_image')->getClientOriginalExtension();
            // Filename to store
            $fileNameToStore = $filename.'_'.time().'.'.$extension;
            // Upload Image
            $path = $request->file('cover_image')->storeAs('public/cover_images', $fileNameToStore);
        }else{
            $fileNameToStore = 'noimage.jpg';
        }
        //Create Post
        $post = new Post;
        $post->title = $request->input('title');
        $post->body = $request->input('article-ckeditor');
        $post->user_id = auth()->user()->id;
        $post->cover_image = $fileNameToStore;
        $post->save();

        return redirect('/posts')->with('success', 'Post Created');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id, FormBuilder $formBuilder)
    {
        $post = Post::find($id);
        $form = $formBuilder->create('App\Forms\DelForm', [
            'method' => 'DELETE',
            'url' => route('posts.destroy',$post->id),
            'class' => 'float-right'
        ]);
        $data = [
            'post' => $post,
            'form' => $form
        ];
        return view('posts.show')->with($data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id, FormBuilder $formBuilder)
    {
        $post = Post::find($id);
        //check for correct user
        if(auth()->user()->id != $post->user_id){
            return redirect('/posts')->with('error','Unauthorized Page');
        }
        //change the body name so CK editor will work properly
        $post['article-ckeditor'] = $post->body; 
        $form = $formBuilder->create('App\Forms\BlogForm', [
            'method' => 'PUT',
            'url' => route('posts.update',$post->id),
            'model' => $post,
            'files' => true
        ]);

        return view('posts.edit', compact('form'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'title' => 'required',
            'article-ckeditor' => 'required',
            'cover_image' => 'image|nullable|max:1999'
        ]);
        // Handle File Upload
        if($request->hasFile('cover_image')){
            // Get filename with the extention
            $filenameWithExt = $request -> file('cover_image')->getClientOriginalName();
            // Get just filename
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            // Get just extension
            $extension = $request->file('cover_image')->getClientOriginalExtension();
            // Filename to store
            $fileNameToStore = $filename.'_'.time().'.'.$extension;
            // Upload Image
            $path = $request->file('cover_image')->storeAs('public/cover_images', $fileNameToStore);
        }
        
        //Create Post
        $post = Post::find($id);
        $post->title = $request->input('title');
        $post->body = $request->input('article-ckeditor');
        if($request->hasFile('cover_image')){
            $post->cover_image = $fileNameToStore;
        }
        $post->save();

        return redirect('/posts')->with('success', 'Post Updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $post = Post::find($id);
    
        //check for correct user
        if(auth()->user()->id != $post->user_id){
            return redirect('/posts')->with('error','Unauthorized Page');
        }
        if($post->cover_image != 'noimage.jpg'){
            //Delete Image
            Storage::delete('public/cover_images/'.$post->cover_image);
        }
        $post->delete();
        return redirect('/posts')->with('success', 'Post Removed');
    }

    
}
