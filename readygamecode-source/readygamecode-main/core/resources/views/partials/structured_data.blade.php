@php
    $webPageJson = [
        '@context' => 'https://schema.org',
        '@type' => 'WebPage',
        'url' => url()->current(),
        'name' => trim(View::hasSection('meta_title') ? trim(View::getSection('meta_title')) : (gs()->siteName($pageTitle ?? ''))),
        'description' => trim(View::hasSection('meta_description') ? trim(View::getSection('meta_description')) : (isset($seoContents->description) ? $seoContents->description : null)),
    ];

    // FAQ structured data helper: expects $faqs as array of ['question'=>..., 'answer'=>...]
@endphp

<script type="application/ld+json">
    {!! json_encode($webPageJson, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
</script>

@if (!empty($faqs) && is_array($faqs))
    @php
        $faqSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => array_map(function ($f) {
                return [
                    '@type' => 'Question',
                    'name' => $f['question'] ?? null,
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => $f['answer'] ?? null,
                    ],
                ];
            }, $faqs),
        ];
    @endphp
    <script type="application/ld+json">
        {!! json_encode($faqSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
    </script>
@endif
