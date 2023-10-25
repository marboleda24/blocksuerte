@extends('layouts.blog')

@section('content')
    <div class="intro-y grid grid-cols-12 gap-6 mt-5">
        @if($posts->isEmpty())
            <div class="post-preview">
                <h2 class="text-4xl font-semibold">Aún no hay artículos para mostrar. 😔</h2>
            </div>
        @else
            @foreach ($posts as $post)
                <div class="intro-y blog col-span-12 md:col-span-12 box border mb-10">
                    <div class="blog__preview image-fit">
                        <img alt="post-img" src="/img/home_bg.jpg" class="rounded-t-md">
                        <div class="absolute w-full flex items-center px-5 pt-6 z-10">
                            <div class="w-10 h-10 flex-none image-fit">
                                @if ($post->user->profile_photo_path)
                                    <img alt="{{ $post->user->name }}" src="{{ "storage/".$post->user->profile_photo_path }}" class="rounded-full">
                                @else
                            <img alt="{{ $post->user->name }}" src="{{"https://ui-avatars.com/api/?name=".$post->user->name."&color=7F9CF5&background=EBF4FF" }}" class="rounded-full">
                                @endif
                            </div>
                            <div class="ml-3 text-white mr-auto">
                                <a href="" class="font-medium">{{$post->user->name}}</a>
                                <div class="text-xs text-left">{{ $post->created_at->diffForHumans() }}</div>
                            </div>
                        </div>
                        <div class="absolute bottom-0 text-white px-5 pb-6 z-10">
                            @foreach ($post->categories as $category)
                                <a href="{{ route('category', $category->slug) }}" class="blog__category px-2 py-1 rounded">{{ $category->name }}</a>
                            @endforeach

                            <a href="{{ route('post', $post->slug) }}" class="block font-medium text-xl text-left mt-3">{{ $post->title }}</a>
                        </div>
                    </div>
                    <div class="p-5 text-gray-700 dark:text-gray-600 text-left">
                        {!! Str::substr($post->body, 0, 400)  !!}... <a  href="{{ route('post', $post->slug) }}" class="bg-red-700 text-white active:bg-pink-600 font-bold uppercase text-xs px-2 py-1 rounded shadow hover:shadow-md outline-none focus:outline-none mr-1 mb-1" style="transition: all .15s ease">
                            continuar leyendo
                        </a>
                    </div>
                </div>
            @endforeach
        @endif
    </div>
@stop
