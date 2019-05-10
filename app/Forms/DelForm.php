<?php

namespace App\Forms;

use Kris\LaravelFormBuilder\Form;


class DelForm extends Form
{
    public function buildForm()
    {
        $this
            ->add('submit', 'submit', [
                'label' => 'Delete', 
                'attr' => ['class' => 'btn btn-danger'],
                ]);
    }
}

