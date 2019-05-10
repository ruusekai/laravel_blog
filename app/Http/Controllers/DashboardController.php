<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Post;
use Kris\LaravelFormBuilder\FormBuilder;
use App\Providers\AppServiceProvider;

class DashboardController extends Controller
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
    public function index(FormBuilder $formBuilder)
    {
        $user_id = auth()->user()->id;
        $user = User::find($user_id);
        $posts =  collect($user->posts)->paginate(1);

        $form = $formBuilder->create('App\Forms\DelForm', [
                'method' => 'DELETE',
                'class' => 'float-right'
        ]);
            $data = [
            'posts' => $posts,
            'form' => $form
        ];
        return view('dashboard')->with($data);
    }
}
