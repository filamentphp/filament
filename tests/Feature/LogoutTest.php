<?php

namespace Filament\Tests\Feature;

//class LogoutTest extends TestCase
//{
//    public function test_authenticated_user_can_see_log_out_button()
//    {
//        $this->withoutExceptionHandling();
//        $user = User::factory()->create();
//        $this->actingAs($user)
//            ->get(FilamentManager::home())
//            ->assertSeeLivewire('filament-logout');
//    }
//
//    public function test_authenticated_user_can_log_out()
//    {
//        $user = User::factory()->create();
//        Livewire::actingAs($user)
//            ->test(Logout::class)
//            ->call('logout')
//            ->assertRedirect(route('filament.login'));
//
//        $this->assertGuest();
//    }
//
//    public function test_guest_cannot_log_out()
//    {
//        Livewire::test(Logout::class)
//            ->call('logout')
//            ->assertRedirect(route('filament.login'));
//    }
//}
