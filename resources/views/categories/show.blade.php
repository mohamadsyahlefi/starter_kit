<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $category->name }}
            </h2>
            @if(auth()->user()->isAdmin())
                <div>
                    <a href="{{ route('categories.edit', $category) }}" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded mr-2">
                        Edit
                    </a>
                    <form action="{{ route('categories.destroy', $category) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded" onclick="return confirm('Apakah Anda yakin ingin menghapus kategori ini?')">
                            Hapus
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($news as $item)
                            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                                @if($item->image)
                                    <img src="{{ Storage::url($item->image) }}" alt="{{ $item->title }}" class="w-full h-48 object-cover">
                                @endif
                                <div class="p-4">
                                    <h3 class="text-lg font-semibold mb-2">{{ $item->title }}</h3>
                                    <p class="text-sm text-gray-500 mb-2">
                                        Ditulis oleh {{ $item->user->name }} | {{ $item->created_at->format('d M Y') }}
                                    </p>
                                    <p class="text-sm text-gray-600 mb-4">
                                        {{ Str::limit(strip_tags($item->content), 100) }}
                                    </p>
                                    <div class="flex justify-between items-center">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $item->status === 'published' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                            {{ $item->status === 'published' ? 'Published' : 'Draft' }}
                                        </span>
                                        <a href="{{ route('news.show', $item) }}" class="text-indigo-600 hover:text-indigo-900">Baca selengkapnya</a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-4">
                        {{ $news->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 