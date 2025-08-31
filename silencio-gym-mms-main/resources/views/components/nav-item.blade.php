@props(['src', 'dropdown' => false, 'href' => '#', 'dataTitle' => ''])

@if($dropdown)
    <div class="nav-dropdown-item">
        <button class="nav-dropdown-toggle flex items-center gap-3 h-12 w-full hover:bg-gray-800 transition-all duration-200 cursor-pointer rounded-lg p-3 group" data-title="{{ $dataTitle }}">
            <img src="{{ asset($src) }}" alt="" class="object-cover flex-shrink-0">
            <div class="nav-text flex flex-1 justify-between items-center overflow-hidden">
                <span class="text-sm text-white font-medium whitespace-nowrap">{{ $slot }}</span>
                <img src="{{ asset('images/icons/arrow-down-icon.svg') }}" alt="" class="dropdown-arrow w-4 h-4 object-cover transition-transform duration-200 flex-shrink-0">
            </div>
        </button>
        
        <!-- Dropdown content -->
        <div class="nav-dropdown-content hidden">
            <div class="flex flex-col gap-1 py-2 pl-3 pr-3">
                {{ $dropdownContent ?? '' }}
            </div>
        </div>
    </div>
@else
    <a href="{{ $href }}" class="nav-link flex items-center gap-3 h-12 hover:bg-gray-800 transition-all duration-200 p-3 rounded-lg group" data-title="{{ $dataTitle }}">
        <img src="{{ asset($src) }}" alt="" class="object-cover flex-shrink-0">
        <div class="nav-text flex flex-1 justify-between items-center overflow-hidden">
            <span class="text-sm text-white font-medium whitespace-nowrap">{{ $slot }}</span>
        </div>
    </a>
@endif