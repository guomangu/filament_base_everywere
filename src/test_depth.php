<?php
$content = file_get_contents("resources/views/livewire/user/profile.blade.php");
$lines = explode("\n", $content);
$depth = 0;
foreach ($lines as $i => $line) {
    $opens = substr_count(strtolower($line), '<div');
    $closes = substr_count(strtolower($line), '</div');
    $depth += $opens - $closes;
    if ($depth < 1 && $i < count($lines) - 2) {
        echo "Depth 0 at line " . ($i+1) . "\n";
    }
}
echo "Final depth: $depth\n";
