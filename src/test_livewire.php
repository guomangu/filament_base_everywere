<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$user = \App\Models\User::first();
auth()->login($user);

$component = \Livewire\Livewire::test(\App\Livewire\User\Profile::class, ['user' => $user]);

try {
    $component->call('openCreateModal');
    echo "openCreateModal Success! showCreateModal = " . ($component->get('showCreateModal') ? 'true' : 'false') . "\n";
} catch (\Exception $e) {
    echo "Exception in openCreateModal: " . $e->getMessage() . "\n" . $e->getTraceAsString() . "\n";
}

try {
    $component->call('addProofForSkill', 'Test Skill');
    echo "addProofForSkill Success! showCreateModal = " . ($component->get('showCreateModal') ? 'true' : 'false') . "\n";
} catch (\Exception $e) {
    echo "Exception in addProofForSkill: " . $e->getMessage() . "\n" . $e->getTraceAsString() . "\n";
}
