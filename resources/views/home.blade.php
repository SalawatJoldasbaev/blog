<x-app-layout meta-description="Salawat Joldasbaev blogina xush keldin'iz!">
    <div class="container max-w-5xl mx-auto py-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Latest post -->
            <div class="col-span-2">
                <h2 class="text-lg sm:text-xl font-bold text-blue-500 uppercase pb-1  border-b-2 border-blue-500 mb-3">
                    Latest post
                </h2>
                <x-post-item :post="$latestPosts"/>
            </div>
            <!-- Popular 3 post -->
            <div>
                <h2 class="text-lg sm:text-xl font-bold text-blue-500 uppercase pb-1  border-b-2 border-blue-500 mb-3">
                    Popular posts
                </h2>
                @foreach($popularPosts as $post)
                    <div class="grid grid-cols-4 gap-2 mb-4">
                        <a href="{{route('view', $post)}}" class="pt-1">
                            <img src="{{$post->getThumbnail()}}" alt="{{$post->title}}"/>
                        </a>
                        <div class="col-span-3">
                            <a href="{{route('view', $post)}}">
                                <h3 class="text-sm uppercase whitespace-nowrap truncate">
                                    {{$post->title}}
                                </h3>
                            </a>
                            <div class="flex gap-4 mb-2">
                                @foreach($post->categories as $category)
                                    <a href="{{route('by-category', $category)}}"
                                       class="bg-blue-500 text-white p-1 rounded text-xs font-bold uppercase">
                                        {{$category->title}}
                                    </a>
                                @endforeach
                            </div>
                            <div class="text-xs">
                                {{$post->shortBody(10)}}
                            </div>

                            <a href="{{route('view', $post)}}" class="text-xs uppercase text-gray-800 hover:text-black">Continue Reading <i class="fas fa-arrow-right"></i></a>

                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        <!-- Recommended posts -->
        <div>
            <h2 class="text-lg sm:text-xl font-bold text-blue-500 uppercase pb-1  border-b-2 border-blue-500 mb-3">
                Recommended posts
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                @foreach($recommendedPosts as $post)
                    <x-post-item :post="$post" :showAuthor="false"/>
                @endforeach
            </div>
        </div>
        <!-- Latest categories -->
        <div>
            <h2 class="text-lg sm:text-xl font-bold text-blue-500 uppercase pb-1  border-b-2 border-blue-500 mb-3">
                Recent categories
            </h2>
        </div>
    </div>
</x-app-layout>
