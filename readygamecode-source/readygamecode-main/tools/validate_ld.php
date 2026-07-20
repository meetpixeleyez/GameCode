<?php
$html = @file_get_contents(__DIR__ . '/../product_page.html');
if ($html === false) {
    echo "product_page.html not found\n";
    exit(1);
}
if (!preg_match_all('/<script[^>]*type=\"application\/ld\+json\"[^>]*>(.*?)<\/script>/si', $html, $m)) {
    echo "NO_LDJSON_FOUND\n";
    exit(1);
}
foreach ($m[1] as $i => $block) {
    $s = trim($block);
    echo "---- JSON-LD Block " . ($i+1) . " ----\n";
    $decoded = json_decode($s);
    if (json_last_error() === JSON_ERROR_NONE) {
        echo "VALID_JSON\n";
    } else {
        echo "INVALID_JSON: " . json_last_error_msg() . "\n";
    }
    echo $s . "\n---- END Block " . ($i+1) . " ----\n";
}
