<?php
$content = file_get_contents("resources/views/livewire/user/profile.blade.php");
// Check the number of root level divs
$lines = explode("\n", $content);
$depth = 0;
$issues = [];
foreach ($lines as $i => $line) {
    $opens = substr_count(strtolower($line), '<div');
    $closes = substr_count(strtolower($line), '</div');
    $depth += $opens - $closes;
    if ($depth < 1 && $i > 0 && $i < count($lines) - 2) {
        $issues[] = "Depth reached $depth at line " . ($i+1) . ":\n$line\n";
    }
}
echo "Final depth: $depth\n";
if (count($issues) > 0) {
    echo "Issues found:\n" . implode("\n", array_slice($issues, 0, 5)) . "\n";
} else {
    echo "No premature root closures detected via simple line check.\n";
}
