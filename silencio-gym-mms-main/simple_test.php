<?php
echo "Testing Session Duration Formatting:\n";

// Test case: "0.20366143194444h 12m" should become "12m"
$duration = "0.20366143194444h 12m";
$parts = explode('h', $duration);
$hours = floatval($parts[0]);
$minutes = 0;
if (isset($parts[1])) {
    $minPart = trim($parts[1]);
    if (strpos($minPart, 'm') !== false) {
        $minutes = intval(str_replace('m', '', $minPart));
    }
}

echo "Hours: $hours\n";
echo "Minutes: $minutes\n";

$decimalMinutes = round($hours * 60);
$totalMinutes = $decimalMinutes + $minutes;

echo "Decimal minutes: $decimalMinutes\n";
echo "Total minutes: $totalMinutes\n";

$displayHours = floor($totalMinutes / 60);
$displayMinutes = $totalMinutes % 60;

echo "Display hours: $displayHours\n";
echo "Display minutes: $displayMinutes\n";

if ($displayHours > 0) {
    $result = $displayHours . 'h ' . $displayMinutes . 'm';
} else {
    $result = $displayMinutes . 'm';
}

echo "Final result: $result\n";
