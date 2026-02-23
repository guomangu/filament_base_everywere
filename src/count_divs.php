<?php
$content = file_get_contents("resources/views/livewire/user/profile.blade.php");

// Strip html comments
$content = preg_replace('/<!--[\s\S]*?-->/', '', $content);
// Strip blade comments
$content = preg_replace('/\{\{--[\s\S]*?--\}\}/', '', $content);

$lines = explode("\n", $content);
$depth = 0;

foreach($lines as $i => $line) {
    $opens = substr_count(strtolower($line), '<div ');
    $opens += substr_count(strtolower($line), '<div>');
    $closes = substr_count(strtolower($line), '</div>');
    
    $depth += $opens - $closes;
    
    if ($depth < 0) {
        echo "NEGATIVE DEPTH at line " . ($i+1) . ": $line\n";
    }
}
echo "Final depth: $depth\n";
