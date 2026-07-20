@extends($activeTemplate . 'layouts.frontend')

@section('meta_title', gs()->siteName(__('Blog')))
@section('meta_description', 'Read the latest news, tutorials and insights for Unity game source code, mobile game templates and app development projects.')
@section('meta_keywords', 'Unity game development blog, game source code news, mobile game tutorials, app development articles')
@section('meta_image', asset('assets/images/default.png'))

@section('content')
<section class="pt-60 pb-120">
    <div class="container">
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
            @forelse($posts as $post)
                <div class="col">
                    <div class="card h-100 shadow-sm border-0 blog-card">
                        @if($post->cover_image)
                            <a href="{{ route('blog.show', $post->slug) }}">
                                <img class="card-img-top" src="{{ getImage(getFilePath('blogCover') . '/' . $post->cover_image) }}" alt="{{ $post->title }}" loading="lazy" style="object-fit:cover;width:100%;height:220px;" />
                            </a>
                        @endif
                        <div class="card-body d-flex flex-column">
                            @if($post->category)
                                <a class="badge badge--base mb-2" href="{{ route('blog.index', ['category' => $post->category->slug]) }}">{{ $post->category->name }}</a>
                            @endif
                            <h5 class="card-title"><a class="link" href="{{ route('blog.show', $post->slug) }}">{{ $post->title }}</a></h5>
                            <p class="text-muted mb-2">{{ str()->limit(strip_tags($post->body), 100) }}</p>
                            <small class="text-muted mt-auto">{{ optional($post->published_at)->format('M d, Y') }}</small>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <x-empty-list title="No blog posts yet" />
                </div>
            @endforelse
        </div>
        <div class="mt-4">
            {{ $posts->links() }}
        </div>
    </div>
    </section>
@endsection

@push('script')
    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "ItemList",
            "name": "Blog Posts",
            "description": "Latest Unity game source code articles, tutorials and marketplace news.",
            "itemListElement": [
                @foreach($posts as $index => $post)
                    {
                        "@type": "ListItem",
                        "position": {{ $index + 1 }},
                        "url": "{{ route('blog.show', $post->slug) }}",
                        "name": "{{ e($post->title) }}"
                    }@if(!$loop->last),@endif
                @endforeach
            ]
        }
    </script>

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
                }
            ]
        }
    </script>
@endpush


