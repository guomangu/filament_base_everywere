<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Grab user with ID 1
$user = \App\Models\User::first();
auth()->login($user);

$component = \Livewire\Livewire::test(\App\Livewire\User\Profile::class, ['user' => $user]);

// Verify canEdit output
echo "auth()->id(): " . auth()->id() . "\n";
echo "user->id: " . $user->id . "\n";
echo "canEdit: " . ($component->instance()->canEdit() ? 'true' : 'false') . "\n";

$component->call('openCreateModal');
echo "openCreateModal showCreateModal state: " . ($component->get('showCreateModal') ? 'true' : 'false') . "\n";
