<?php

namespace Filament\Tests\Feature;

use Filament\Filament;
use Filament\Http\Livewire\EditAccount;
use Filament\Models\User;
use Filament\Tests\TestCase;
use Illuminate\Support\Str;
use Livewire\Livewire;

class EditAccountTest extends TestCase
{
    /** @test */
    public function can_update_account_information()
    {
        $user = User::factory()->create();
        $newUserDetails = User::factory()->make();
//        $newPassword = Str::random();

        $this->be($user);

        Livewire::test(EditAccount::class)
            ->assertSet('record.email', $user->email)
            ->assertSet('record.name', $user->name)
            ->set('record.email', $newUserDetails->email)
            ->set('record.name', $newUserDetails->name)
//            ->set('record.password', $newPassword)
//            ->set('record.passwordConfirmation', $newPassword)
            ->call('save')
            ->assertHasNoErrors()
//            ->assertNotSet('record.password', $newPassword)
//            ->assertNotSet('record.passwordConfirmation', $newPassword)
            ->assertDispatchedBrowserEvent('notify');

        $user->refresh();

        $this->assertEquals($newUserDetails->email, $user->email);
        $this->assertEquals($newUserDetails->name, $user->name);
//        $this->assertTrue(Filament::auth()->attempt([
//            'email' => $newUserDetails->email,
//            'password' => $newPassword,
//        ]));
    }

    /** @test */
    public function can_view_account_page()
    {
        $user = User::factory()->create();

        $this->be($user);

        $this->get(route('filament.account'))
            ->assertSuccessful()
            ->assertSeeLivewire('filament.core.edit-account');
    }

    /** @test */
    public function record_email_is_required()
    {
        $user = User::factory()->create();

        $this->be($user);

        Livewire::test(EditAccount::class)
            ->set('record.email', null)
            ->call('save')
            ->assertHasErrors(['record.email' => 'required']);
    }

    /** @test */
    public function record_email_is_valid_email()
    {
        $user = User::factory()->create();

        $this->be($user);

        Livewire::test(EditAccount::class)
            ->set('record.email', 'invalid-email')
            ->call('save')
            ->assertHasErrors(['record.email' => 'email']);
    }

    /** @test */
    public function record_name_is_required()
    {
        $user = User::factory()->create();

        $this->be($user);

        Livewire::test(EditAccount::class)
            ->set('record.name', null)
            ->call('save')
            ->assertHasErrors(['record.name' => 'required']);
    }

//    /** @test */
//    public function record_password_contains_minimum_8_characters()
//    {
//        $user = User::factory()->create();
//
//        $this->be($user);
//
//        Livewire::test(EditAccount::class)
//            ->set('record.password', 'pass')
//            ->call('save')
//            ->assertHasErrors(['record.password' => 'min']);
//    }

    /** @test */
    public function record_password_is_confirmed()
    {
        $user = User::factory()->create();

        $this->be($user);

        Livewire::test(EditAccount::class)
            ->set('record.password', 'password')
            ->set('record.passwordConfirmation', 'different-password')
            ->call('save')
            ->assertHasErrors(['record.passwordConfirmation' => 'same']);
    }
}
