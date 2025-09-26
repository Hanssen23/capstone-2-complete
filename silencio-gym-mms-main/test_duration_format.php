<?php
// Test the session duration formatting logic
function formatSessionDuration($duration) {
    if (is_string($duration) && strpos($duration, 'h') !== false) {
        // Handle format like "0.20366143194444h 12m"
        $parts = explode('h', $duration);
        $hours = floatval($parts[0]);
        $minutes = 0;
        if (isset($parts[1])) {
            $minPart = trim($parts[1]);
            if (strpos($minPart, 'm') !== false) {
                $minutes = intval(str_replace('m', '', $minPart));
            }
        }
        
        // Convert decimal hours to minutes and add the existing minutes
        $decimalMinutes = round($hours * 60);
        $totalMinutes = $decimalMinutes + $minutes;
        
        $displayHours = floor($totalMinutes / 60);
        $displayMinutes = $totalMinutes % 60;
        
        if ($displayHours > 0) {
            return $displayHours . 'h ' . $displayMinutes . 'm';
        } else {
            return $displayMinutes . 'm';
        }
    } else {
        return $duration;
    }
}

// Test cases
$testCases = [
    '0.20366143194444h 12m' => '12m',
    '1.5h 30m' => '1h 30m',
    '0.083333333333333h 5m' => '5m',
    '2.25h 15m' => '2h 15m',
    '0h 45m' => '45m',
    '1h 0m' => '1h 0m'
];

echo "Testing Session Duration Formatting:\n";
foreach ($testCases as $input => $expected) {
    $result = formatSessionDuration($input);
    echo "Input: {$input} → Output: {$result} (Expected: {$expected})";
    if ($result === $expected) {
        echo " ✅ PASS\n";
    } else {
        echo " ❌ FAIL\n";
    }
}
