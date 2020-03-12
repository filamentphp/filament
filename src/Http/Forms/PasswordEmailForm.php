<?php

namespace Alpine\Http\Forms;

use Kris\LaravelFormBuilder\Form;
use Kris\LaravelFormBuilder\Field;

class PasswordEmailForm extends Form
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
            
            ->add('submit', 'submit', [
                'wrapper' => [
                    'class' => 'text-center',
                ],
                'label' => __('Send Password Reset Link'),
                'attr' => ['class' => 'btn'],
                'help_block' => [
                    'text' => '<a href="'.route('alpine.auth.login').'">'.__('Back to Login').'</a>',
                    'tag' => 'p',
                ],
            ]);
    }
}
