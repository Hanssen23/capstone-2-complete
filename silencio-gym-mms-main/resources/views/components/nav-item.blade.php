@props(['src', 'dropdown' => false, 'href' => '#'])

@if($dropdown)
    <div class="nav-dropdown-item">
        <button class="nav-dropdown-toggle flex items-center gap-3 h-12 w-full cursor-pointer rounded-lg p-3">
            <img src="{{ asset($src) }}" alt="" class="w-6 h-6 object-cover flex-shrink-0">
            <div class="nav-text flex flex-1 justify-between items-center overflow-hidden">
                <span class="text-sm font-medium whitespace-nowrap">{{ $slot }}</span>
                <img src="{{ asset('images/icons/arrow-down-icon.svg') }}" alt="" class="dropdown-arrow w-4 h-4 object-cover flex-shrink-0">
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
    <a href="{{ $href }}" class="nav-link flex items-center gap-3 h-12 p-3 rounded-lg">
        <img src="{{ asset($src) }}" alt="" class="w-6 h-6 object-cover flex-shrink-0">
        <div class="nav-text flex flex-1 justify-between items-center overflow-hidden">
            <span class="text-sm font-medium whitespace-nowrap nav-item-text">{{ $slot }}</span>
        </div>
    </a>
@endif