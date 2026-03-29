    <div id="avatarSimple" class="p-8 flex items-center gap-4" style="width: 230px">
        <x-filament::avatar
            src="https://avatars.githubusercontent.com/u/41773797?v=4"
            alt="Dan Harrin"
        />
        <x-filament::avatar
            src="https://avatars.githubusercontent.com/u/41837763?v=4"
            alt="Ryan Chandler"
        />
        <x-filament::avatar
            src="https://avatars.githubusercontent.com/u/44533235?v=4"
            alt="Zep Fietje"
        />
    </div>

    <div id="avatarSquare" class="p-8 flex items-center gap-4" style="width: 230px">
        <x-filament::avatar
            src="https://avatars.githubusercontent.com/u/41773797?v=4"
            alt="Dan Harrin"
            :circular="false"
        />
        <x-filament::avatar
            src="https://avatars.githubusercontent.com/u/41837763?v=4"
            alt="Ryan Chandler"
            :circular="false"
        />
        <x-filament::avatar
            src="https://avatars.githubusercontent.com/u/44533235?v=4"
            alt="Zep Fietje"
            :circular="false"
        />
    </div>

    <div id="avatarSizes" class="p-8 flex items-end gap-4" style="width: 250px">
        <x-filament::avatar
            src="https://avatars.githubusercontent.com/u/41773797?v=4"
            alt="Dan Harrin"
            size="sm"
        />
        <x-filament::avatar
            src="https://avatars.githubusercontent.com/u/41773797?v=4"
            alt="Dan Harrin"
            size="md"
        />
        <x-filament::avatar
            src="https://avatars.githubusercontent.com/u/41773797?v=4"
            alt="Dan Harrin"
            size="lg"
        />
    </div>
