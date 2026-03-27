<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

test('password can be updated', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->from('/profile')
        ->put('/password', [
            'current_password' => 'password',
            'password' => 'AtelierIPC_2026!Qz7',
            'password_confirmation' => 'AtelierIPC_2026!Qz7',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/profile');

    $this->assertTrue(Hash::check('AtelierIPC_2026!Qz7', $user->refresh()->password));
});

test('correct password must be provided to update password', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->from('/profile')
        ->put('/password', [
            'current_password' => 'wrong-password',
            'password' => 'AtelierIPC_2026!Qz7',
            'password_confirmation' => 'AtelierIPC_2026!Qz7',
        ]);

    $response
        ->assertSessionHasErrorsIn('updatePassword', 'current_password')
        ->assertRedirect('/profile');
});
