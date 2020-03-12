<?php

namespace Alpine\Http\Forms;

use Kris\LaravelFormBuilder\Form;
use Kris\LaravelFormBuilder\Field;

class ResetPasswordForm extends Form
{
    public function buildForm()
    {
        $this
            ->add('token', Field::HIDDEN)
            ->add('email', Field::EMAIL, [
                'label' => __('E-Mail Address'),
                'rules' => 'required|email',
                'attr' => [
                    'autocomplete'=> 'email',
                ],
            ])
            ->add('password', Field::PASSWORD, [
                'label' => __('Password'),
                'rules' => 'required|confirmed',
                'attr' => [
                    'autofocus' => true,
                    'autocomplete'=> 'new-password',
                ],
            ])   
            ->add('password_confirmation', Field::PASSWORD, [
                'label_show' => false,
                'rules' => 'required|confirmed',
                'attr' => [
                    'placeholder' => __('Confirm Password'),
                    'autocomplete'=> 'new-password',
                ],
            ])            
            ->add('submit', 'submit', [
                'wrapper' => [
                    'class' => 'text-center',
                ],
                'label' => __('Reset Password'),
                'attr' => ['class' => 'btn'],
                'help_block' => [
                    'text' => '<a href="'.route('alpine.auth.login').'">'.__('Back to Login').'</a>',
                    'tag' => 'p',
                ],
            ]);
    }
}
