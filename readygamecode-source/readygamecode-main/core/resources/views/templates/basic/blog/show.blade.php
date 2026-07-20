@extends($activeTemplate . 'layouts.frontend')

@section('meta_title', gs()->siteName($post->title))
@section('meta_description', str()->limit(strip_tags($post->excerpt ?? $post->body), 155))
@section('meta_keywords', is_array($post->tags) ? implode(', ', $post->tags) : ($post->tags ?? $post->category?->name ?? 'blog, game source code, unity'))
@section('meta_image', $post->cover_image ? getImage(getFilePath('blogCover') . '/' . $post->cover_image) : asset('assets/images/default.png'))

@section('content')
<section class="pt-60 pb-120">
    <div class="container">
        <div class="row gy-4">
            <div class="col-lg-8">
                <article class="card border-0 shadow-sm">
                    @if($post->cover_image)
                        <img class="card-img-top" src="{{ getImage(getFilePath('blogCover') . '/' . $post->cover_image) }}" alt="{{ $post->title }}" loading="lazy" />
                    @endif
                    <div class="card-body" style="padding:2.5rem;">
                        @if($post->category)
                            <a class="badge badge--base mb-2" href="{{ route('blog.index', ['category' => $post->category->slug]) }}">{{ $post->category->name }}</a>
                        @endif
                        <h2 class="mb-2">{{ $post->title }}</h2>
                        <small class="text-muted d-block mb-3">{{ optional($post->published_at)->format('M d, Y') }}</small>
                        <div class="content">
                            {!! $post->body !!}
                        </div>
                    </div>
                </article>
            </div>
            <div class="col-lg-4">
                <div class="common-sidebar__item">
                    <h6 class="common-sidebar__title">Recent Posts</h6>
                    <ul class="list list--column">
                        @foreach($related as $item)
                            <li><a class="link" href="{{ route('blog.show', $item->slug) }}">{{ $item->title }}</a></li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('script')
    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "BreadcrumbList",
            "itemListElement": [
                {
                    "@type": "ListItem",
                    "position": 1,
                    "name": "Home",
                    "item": "{{ route('home') }}"
                },
                {
                    "@type": "ListItem",
                    "position": 2,
                    "name": "Blog",
                    "item": "{{ route('blog.index') }}"
                },
                {
                    "@type": "ListItem",
                    "position": 3,
                    "name": "{{ e($post->title) }}",
                    "item": "{{ url()->current() }}"
                }
            ]
        }
    </script>
@endpush

@push('script')
    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "Article",
            "mainEntityOfPage": {
                "@type": "WebPage",
                "@id": "{{ url()->current() }}"
            },
            "headline": "{{ e($post->title) }}",
            "description": "{{ e(str()->limit(strip_tags($post->excerpt ?? $post->body), 150)) }}",
            "image": [
                "{{ $post->cover_image ? getImage(getFilePath('blogCover') . '/' . $post->cover_image) : asset('assets/images/default.png') }}"
            ],
            "author": {
                "@type": "Person",
                "name": "{{ e(optional($post->author)->name ?? 'ReadyGameCode') }}"
            },
            "publisher": {
                "@type": "Organization",
                "name": "{{ e(gs()->siteName()) }}",
                "logo": {
                    "@type": "ImageObject",
                    "url": "{{ asset('assets/images/default.png') }}"
                }
            },
            "datePublished": "{{ optional($post->published_at)->toIso8601String() }}",
            "dateModified": "{{ optional($post->updated_at)->toIso8601String() }}"
        }
    </script>
@endpush


