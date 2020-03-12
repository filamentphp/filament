<?php

namespace Alpine\Http\Forms;

use Kris\LaravelFormBuilder\Form;
use Kris\LaravelFormBuilder\Field;

class LoginForm extends Form
{
    public function buildForm()
    {
        $this
            ->add('email', Field::EMAIL, [
                'label' => __('E-Mail Address'),
                'rules' => 'required|email',
                'attr' => [
                    'autofocus' => true,
                    'autocomplete'=> 'email',
                ],
            ])
            ->add('password', Field::PASSWORD, [
                'label' => __('Password'),
                'rules' => 'required',
                'attr' => [
                    'autocomplete' => 'current-password',
                ],
            ])
            ->add('remember', Field::CHECKBOX, [
                'wrapper' => [
                    'class' => 'flex items-center mb-6',
                ],
                'attr' => [
                    'class' => 'form-checkbox h-4 w-4 text-gray-600 transition duration-150 ease-in-out',
                ],
                'label' => __('Remember Me'),
                'label_attr' => [
                    'class' => 'ml-2 block text-sm leading-5 text-gray-900',
                ],
                'help_block' => [
                    'text' => '<a href="'.route('alpine.auth.password.forgot').'">'.__('Forgot Your Password?').'</a>',
                    'tag' => 'p',
                    'attr' => [
                        'class' => 'text-sm font-medium text-gray-500 flex-grow text-right',
                    ],
                ],
            ])
            ->add('submit', 'submit', [
                'label' => __('Login'),
                'attr' => ['class' => 'btn'],
            ]);
    }
}
