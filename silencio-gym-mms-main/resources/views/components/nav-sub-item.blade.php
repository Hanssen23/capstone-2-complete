@props(['src' => null, 'href' => '#'])

<a href="{{ $href }}" class="nav-sub-link flex items-center gap-3 h-10 hover:bg-gray-700 transition-all duration-200 pl-3 rounded-lg group">
    @if($src)
        <img src="{{ asset($src) }}" alt="" class="object-cover flex-shrink-0">
    @else
        <div class="w-5 h-5 bg-gray-600 rounded flex-shrink-0"></div>
    @endif
    <div class="nav-text flex flex-1 justify-between items-center overflow-hidden">
        <span class="text-xs text-gray-300 whitespace-nowrap">{{ $slot }}</span>
    </div>
</a>