<x-app-layout :meta-title="'The Salawat\'s blog - Posts by category '.$category->title" :meta-description="$category->title .' posts'">
    <!-- Posts Section -->
    <section class="w-full md:w-2/3 flex flex-col items-center px-3">
        @foreach($posts as $post)
            <x-post-item :post="$post"></x-post-item>
        @endforeach
        <!-- Pagination -->
        <div class="flex items-center py-8">
            {{$posts->links()}}
        </div>
    </section>
    <x-sidebar/>
</x-app-layout>
