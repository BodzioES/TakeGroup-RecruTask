<div class="p-6">
    <div class="mb-4 flex gap-2">
        <button wire:click="changeLocale('pl')" class="px-4 py-2 bg-blue-500 text-white rounded">Polski</button>
        <button wire:click="changeLocale('en')" class="px-4 py-2 bg-gray-500 text-white rounded">English</button>
        <button wire:click="changeLocale('de')" class="px-4 py-2 bg-green-500 text-white rounded">Deutsch</button>
    </div>

    <div class="grid gap-4">
        @foreach($movies as $movie)
            <div class="border p-4 rounded shadow">
                <h2 class="text-xl font-bold">
                    {{ $movie->languages->first()->title ?? $movie->original_title }}
                </h2>
                <p class="text-gray-600 text-sm">
                    {{ $movie->languages->first()->overview ?? 'No description' }}
                </p>
                <div class="mt-2">
                    @foreach($movie->genres as $genre)
                        <span class="text-xs bg-gray-200 px-2 py-1 rounded">
                            {{ $genre->translations->first()->name ?? 'N/A' }}
                        </span>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-4">
        {{ $movies->links() }}
    </div>
</div>
