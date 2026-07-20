<?php

$path = $argv[1] ?? __DIR__ . '/../product_page.html';
if (!file_exists($path)) {
    fwrite(STDERR, "Error: file not found: {$path}\n");
    exit(2);
}

$html = file_get_contents($path);
if ($html === false) {
    fwrite(STDERR, "Error: failed to read file: {$path}\n");
    exit(2);
}

libxml_use_internal_errors(true);
$dom = new DOMDocument();
$dom->loadHTML($html, LIBXML_NOWARNING | LIBXML_NOERROR);
$xpath = new DOMXPath($dom);

function getMetaContent(DOMXPath $xpath, string $name): ?string {
    $node = $xpath->query("//meta[@name='" . $name . "']")->item(0);
    return $node ? trim($node->getAttribute('content')) : null;
}

function getPropertyContent(DOMXPath $xpath, string $property): ?string {
    $node = $xpath->query("//meta[@property='" . $property . "']")->item(0);
    return $node ? trim($node->getAttribute('content')) : null;
}

function getLinkHref(DOMXPath $xpath, string $rel): ?string {
    $node = $xpath->query("//link[@rel='" . $rel . "']")->item(0);
    return $node ? trim($node->getAttribute('href')) : null;
}

function assertValue(string $label, $value): bool {
    if ($value === null || $value === '') {
        echo "FAIL: {$label} is missing or empty\n";
        return false;
    }
    echo "PASS: {$label} = " . (is_array($value) ? json_encode($value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) : $value) . "\n";
    return true;
}

$results = [];
$results[] = assertValue('title', trim($xpath->query('//title')->item(0)?->textContent ?? ''));
$results[] = assertValue('meta.description', getMetaContent($xpath, 'description'));
$results[] = assertValue('meta.keywords', getMetaContent($xpath, 'keywords'));
$results[] = assertValue('meta.robots', getMetaContent($xpath, 'robots'));
$results[] = assertValue('canonical', getLinkHref($xpath, 'canonical'));
$results[] = assertValue('og:title', getPropertyContent($xpath, 'og:title'));
$results[] = assertValue('og:description', getPropertyContent($xpath, 'og:description'));
$results[] = assertValue('og:image', getPropertyContent($xpath, 'og:image'));
$results[] = assertValue('og:url', getPropertyContent($xpath, 'og:url'));
$results[] = assertValue('twitter:title', getMetaContent($xpath, 'twitter:title'));
$results[] = assertValue('twitter:description', getMetaContent($xpath, 'twitter:description'));
$results[] = assertValue('twitter:image', getMetaContent($xpath, 'twitter:image'));

$scriptNodes = $xpath->query("//script[@type='application/ld+json']");
$foundSchemas = [];
$schemaErrors = [];
for ($i = 0; $i < $scriptNodes->length; $i++) {
    $script = $scriptNodes->item($i);
    $text = trim($script->textContent);
    if ($text === '') {
        continue;
    }
    $data = json_decode($text, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        $schemaErrors[] = "Script #{$i} contains invalid JSON: " . json_last_error_msg();
        continue;
    }
    if (isset($data['@type'])) {
        $foundSchemas[$data['@type']][] = $data;
    } else {
        $foundSchemas['unknown'][] = $data;
    }
}

if (!empty($schemaErrors)) {
    foreach ($schemaErrors as $error) {
        echo "FAIL: {$error}\n";
    }
}

function validateSchema(array $data, string $type): bool {
    $ok = true;
    echo "\nSchema: {$type}\n";
    if (!isset($data['@context']) || $data['@context'] !== 'https://schema.org') {
        echo "FAIL: @context must be https://schema.org\n";
        $ok = false;
    } else {
        echo "PASS: @context\n";
    }
    if (!isset($data['@type']) || $data['@type'] !== $type) {
        echo "FAIL: @type expected {$type}\n";
        $ok = false;
    } else {
        echo "PASS: @type\n";
    }
    return $ok;
}

$schemaResults = [];

if (!empty($foundSchemas['WebPage'])) {
    $webPage = $foundSchemas['WebPage'][0];
    $schemaResults[] = validateSchema($webPage, 'WebPage');
    $schemaResults[] = assertValue('WebPage.url', $webPage['url'] ?? null);
    $schemaResults[] = assertValue('WebPage.name', $webPage['name'] ?? null);
    $schemaResults[] = assertValue('WebPage.description', $webPage['description'] ?? null);
} else {
    echo "FAIL: WebPage schema is missing\n";
    $schemaResults[] = false;
}

if (!empty($foundSchemas['VideoGame'])) {
    $videoGame = $foundSchemas['VideoGame'][0];
    $schemaResults[] = validateSchema($videoGame, 'VideoGame');
    $schemaResults[] = assertValue('VideoGame.name', $videoGame['name'] ?? null);
    $schemaResults[] = assertValue('VideoGame.description', $videoGame['description'] ?? null);
    $schemaResults[] = assertValue('VideoGame.url', $videoGame['url'] ?? null);
    $schemaResults[] = assertValue('VideoGame.image', $videoGame['image'] ?? null);
    if (isset($videoGame['publisher']) && (is_array($videoGame['publisher']) || is_string($videoGame['publisher']))) {
        if (is_array($videoGame['publisher'])) {
            $schemaResults[] = assertValue('VideoGame.publisher.name', $videoGame['publisher']['name'] ?? null);
        } else {
            $schemaResults[] = assertValue('VideoGame.publisher', $videoGame['publisher']);
        }
    } else {
        echo "FAIL: VideoGame.publisher is missing\n";
        $schemaResults[] = false;
    }
    $schemaResults[] = assertValue('VideoGame.genre', $videoGame['genre'] ?? null);
    $schemaResults[] = assertValue('VideoGame.keywords', $videoGame['keywords'] ?? null);
} else {
    echo "FAIL: VideoGame schema is missing\n";
    $schemaResults[] = false;
}

if (!empty($foundSchemas['BreadcrumbList'])) {
    $breadcrumb = $foundSchemas['BreadcrumbList'][0];
    $schemaResults[] = validateSchema($breadcrumb, 'BreadcrumbList');
    if (!isset($breadcrumb['itemListElement']) || !is_array($breadcrumb['itemListElement'])) {
        echo "FAIL: BreadcrumbList.itemListElement is missing or invalid\n";
        $schemaResults[] = false;
    } else {
        $schemaResults[] = assertValue('BreadcrumbList.itemListElement count', count($breadcrumb['itemListElement']));
    }
} else {
    echo "FAIL: BreadcrumbList schema is missing\n";
    $schemaResults[] = false;
}

$passing = count(array_filter($results)) + count(array_filter($schemaResults));
$total = count($results) + count($schemaResults);

echo "\nSummary: {$passing}/{$total} checks passed." . PHP_EOL;
exit($passing === $total ? 0 : 1);
