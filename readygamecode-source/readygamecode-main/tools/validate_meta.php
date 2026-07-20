<?php
$html = @file_get_contents(__DIR__ . '/../product_page.html');
if ($html === false) { echo "product_page.html not found\n"; exit(1); }
libxml_use_internal_errors(true);
$doc = new DOMDocument();
$doc->loadHTML($html);
$xpath = new DOMXPath($doc);
function text($node){ return trim($node->textContent ?? ''); }
$title = $doc->getElementsByTagName('title')->item(0)->textContent ?? '';
$metaDesc = '';
$metas = $doc->getElementsByTagName('meta');
foreach ($metas as $meta) {
    $name = strtolower($meta->getAttribute('name'));
    if ($name === 'description') $metaDesc = $meta->getAttribute('content');
}
$canonical = '';
$links = $doc->getElementsByTagName('link');
foreach ($links as $link) {
    if (strtolower($link->getAttribute('rel')) === 'canonical') $canonical = $link->getAttribute('href');
}
// hreflang check
$hreflang = false;
foreach ($links as $link) {
    if ($link->hasAttribute('hreflang')) { $hreflang = true; break; }
}
// Open Graph
$ogTitle = '';
$ogDesc = '';
foreach ($metas as $meta) {
    $prop = strtolower($meta->getAttribute('property'));
    if ($prop === 'og:title') $ogTitle = $meta->getAttribute('content');
    if ($prop === 'og:description') $ogDesc = $meta->getAttribute('content');
}

$titleLength = mb_strlen($title, 'UTF-8');
$metaDescLength = mb_strlen($metaDesc, 'UTF-8');

echo "Title: " . ($title ?: '[missing]') . "\n";
echo "- Length: " . $titleLength . " chars\n";
echo "Meta description: " . ($metaDesc ?: '[missing]') . "\n";
echo "- Length: " . $metaDescLength . " chars\n";
echo "Canonical: " . ($canonical ?: '[missing]') . "\n";
echo "Hreflang present: " . ($hreflang ? 'yes' : 'no') . "\n";
echo "OG title present: " . ($ogTitle ? 'yes' : 'no') . "\n";
echo "OG description present: " . ($ogDesc ? 'yes' : 'no') . "\n";

// Simple pass/fail hints
if (!$title) echo "WARNING: Missing title tag.\n";
if ($metaDescLength < 50 || $metaDescLength > 155) echo "WARNING: Meta description length should be between 50 and 155 characters.\n";
if (!$canonical) echo "WARNING: Missing canonical link.\n";
if (!$hreflang) echo "NOTE: No hreflang link detected — add for multi-language sites.\n";
if (!$ogTitle || !$ogDesc) echo "NOTE: Open Graph title/description missing or incomplete.\n";
