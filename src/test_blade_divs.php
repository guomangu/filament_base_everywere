<?php
$content = file_get_contents("resources/views/livewire/user/profile.blade.php");

// extremely simplified check: let's just count divs inside major @if blocks
$blocks = [
    '/@if\(\$userProjects->count\(\) > 0.*?\)(.*?)@elseif/s',
    '/@elseif\(auth\(\)->check\(\).*?\)(.*?)@endif/s',
    '/@forelse\(\$groupedAchievements.*?\)(.*?)@empty/s',
    '/@empty(.*?)@endforelse/s',
    '/@if\(\$isCreatingProject\)(.*?)@endif/s',
    '/@if\(\$user->id === auth\(\)->id\(\) && \$user->proches->count\(\) > 0\)(.*?)@endif/s',
    '/@if\(\$showCreateModal\)(.*?)@endif/s',
    '/@if\(\$showValidationModal.*?\)(.*?)@endif/s',
];

foreach ($blocks as $pattern) {
    if (preg_match($pattern, $content, $matches)) {
        $inner = $matches[1];
        $inner = preg_replace('/<!--.*?-->/', '', $inner);
        $inner = preg_replace('/\{\{--.*?--\}\}/', '', $inner);
        $opens = substr_count(strtolower($inner), '<div'); // note: this matches <div ...> and <div>
        $closes = substr_count(strtolower($inner), '</div');
        echo "Pattern: " . substr($pattern, 0, 40) . "... -> Opens: $opens, Closes: $closes, Diff: " . ($opens - $closes) . "\n";
    }
}
