<?php

namespace App\Forms;

use Kris\LaravelFormBuilder\Form;

class BlogForm extends Form
{
    public function buildForm()
    {
        $this
            ->add('title', 'text')
            ->add('article-ckeditor', 'textarea', ['label' => 'body'])
            ->add('cover_image','file')
            ->add('submit', 'submit', ['label' => 'Save form']);
   
    }
}
