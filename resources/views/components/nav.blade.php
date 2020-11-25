<nav aria-label="primary" class="flex-grow px-4">
    <ol class="space-y-1">
        @foreach($nav as $group => $items)
        @if ($group)
            <li>          
                <h3 class="text-xs font-semibold text-gray-600 leading-tight tracking-wider uppercase my-2">{{ $group }}</h3>            
                <ol class="space-y-1">
        @endif
                @foreach($items as $item)
                    <li>
                        <x-filament::nav-link 
                            :path="$item['path']" 
                            :active="$item['active']" 
                            :label="$item['label']" 
                            :icon="$item['icon']" />
                    </li>
                @endforeach
        @if ($group)        
                </ol>
            </li>
        @endif
        @endforeach
    </ol>
</nav>