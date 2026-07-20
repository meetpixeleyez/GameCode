<script type="application/ld+json">
{!! json_encode([
    "@context" => "https://schema.org",
    "@type" => "Product",
    "name" => $product->title,
    "description" => trim(strip_tags($product->meta_description ?? $product->description)),
    "image" => [
        getImage(getFilePath('productPreview') . '/' . productFilePath($product, 'preview_image'), getFileSize('productPreview'))
    ],
    "url" => route('product.details', $product->slug),
    "offers" => [
        "@type" => "Offer",
        "priceCurrency" => gs('cur_text'),
        "price" => $product->is_free ? '0' : (string) ($product->price ?? 0),
        "url" => route('product.details', $product->slug)
    ]
], JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT) !!}
</script>
