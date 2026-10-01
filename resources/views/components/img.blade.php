{{--
    An <img> sized for where it is shown. Cloudinary sources get a srcset at the display width
    and twice it (for sharp screens) in AVIF/WebP; local sources pass through as they are.
    width: CSS pixels at the largest layout; sizes: the usual sizes attribute (defaults to width).
--}}
@props(['src', 'width' => 800, 'sizes' => null])
@php
    $srcset = \App\Support\ImageUrl::srcset($src, [(int) round($width / 2), $width, $width * 2]);
@endphp
<img src="{{ \App\Support\ImageUrl::width($src, $width) }}" @if($srcset) srcset="{{ $srcset }}" sizes="{{ $sizes ?? $width.'px' }}" @endif {{ $attributes->merge(['decoding' => 'async']) }}>
