<div class="relative">
    <input 
        type="{{ $type }}" 
        name="{{ $name }}" 
        value="{{ $value }}" 
        class="form-input block w-full sm:text-sm sm:leading-5
            @error($name) 
                pr-10 border-red-300 text-red-900 placeholder-red-300 focus:border-red-300 focus:shadow-outline-red 
            @enderror
            {{ $class }}" 
        {{ $attributes }} />
    @error($name) 
        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
            {{ Alpine::svg('heroicons/solid-sm/sm-exclamation-circle', 'h-5 w-5 text-red-500') }}
        </div>
    @enderror
</div>
@error($name)
    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
@enderror