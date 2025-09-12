<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" @class([
//    'dark' => auth()->check() && auth()->user()->profile && auth()->user()->profile->effective_theme === 'dark',
    'h-full bg-white dark:bg-gray-900',
])>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $title ?? 'Page Title' }}</title>

    <!-- Styles / Scripts -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif

    @googlefonts

</head>
<body @class([
        'debug-screens' => !app()->isProduction(),
        'h-full'
    ])>

<el-dialog>
    <dialog id="sidebar" class="backdrop:bg-transparent lg:hidden">
        <el-dialog-backdrop class="fixed inset-0 bg-gray-900/80 transition-opacity duration-300 ease-linear data-closed:opacity-0"></el-dialog-backdrop>

        <div tabindex="0" class="fixed inset-0 flex focus:outline-none">
            <el-dialog-panel class="group/dialog-panel relative mr-16 flex w-full max-w-xs flex-1 transform transition duration-300 ease-in-out data-closed:-translate-x-full">
                <div class="absolute top-0 left-full flex w-16 justify-center pt-5 duration-300 ease-in-out group-data-closed/dialog-panel:opacity-0">
                    <button type="button" command="close" commandfor="sidebar" class="-m-2.5 p-2.5">
                        <span class="sr-only">Close sidebar</span>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" data-slot="icon" aria-hidden="true" class="size-6 text-white">
                            <path d="M6 18 18 6M6 6l12 12" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </button>
                </div>

                <!-- Sidebar component, swap this element with another sidebar if you like -->
                <div class="relative flex grow flex-col gap-y-5 overflow-y-auto bg-red-600 px-6 pb-4 dark:bg-red-800 dark:ring-1 dark:ring-white/10">
                    <div class="flex h-16 shrink-0 items-center">
                        <img src="https://tailwindcss.com/plus-assets/img/logos/mark.svg?color=white" alt="Your Company" class="h-8 w-auto" />
                    </div>
                    <nav class="flex flex-1 flex-col">
                        <ul role="list" class="flex flex-1 flex-col gap-y-7">
                            <li>
                                <ul role="list" class="-mx-2 space-y-1">
                                    @foreach($topNav as $item)
                                        <li>
                                            <!-- Current:  -->
                                            <a wire:navigate
{{--                                               wire:current="text-red-200 dark:text-red-100 hover:text-white hover:bg-red-700 dark:hover:bg-red-950/25" --}}
                                               href="{{ $item['link'] }}" class="group flex gap-x-3 rounded-md hover:bg-red-700 p-2 text-sm/6 font-semibold text-white hover:dark:bg-red-950/25">
                                                <x-dynamic-component :component="$item['icon']" class="mt-4" />
                                                {{ $item['text'] }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </li>
                            <li>
                                <div class="text-xs/6 font-semibold text-red-200 dark:text-red-100">Community</div>
                                <ul role="list" class="-mx-2 mt-2 space-y-1">
                                    @foreach($bottomNav as $item)
                                        <li>
                                            <!-- Current:  -->
                                            <a wire:navigate
{{--                                               wire:current="text-red-200 dark:text-red-100 hover:text-white hover:bg-red-700 dark:hover:bg-red-950/25" --}}
                                               href="{{ $item['link'] }}" class="group flex gap-x-3 rounded-md hover:bg-red-700 p-2 text-sm/6 font-semibold text-white hover:dark:bg-red-950/25">
                                                <x-dynamic-component :component="$item['icon']" class="mt-4" />
                                                {{ $item['text'] }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </li>
{{--                            <li class="mt-auto">--}}
{{--                                <a href="#" class="group -mx-2 flex gap-x-3 rounded-md p-2 text-sm/6 font-semibold text-red-200 hover:bg-red-700 hover:text-white dark:text-red-100 dark:hover:bg-red-950/25">--}}
{{--                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" data-slot="icon" aria-hidden="true" class="size-6 shrink-0 text-red-200 group-hover:text-white dark:text-red-100">--}}
{{--                                        <path d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 0 1 0-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28Z" stroke-linecap="round" stroke-linejoin="round" />--}}
{{--                                        <path d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" stroke-linecap="round" stroke-linejoin="round" />--}}
{{--                                    </svg>--}}
{{--                                    Settings--}}
{{--                                </a>--}}
{{--                            </li>--}}
                        </ul>
                    </nav>
                </div>
            </el-dialog-panel>
        </div>
    </dialog>
</el-dialog>

<!-- Static sidebar for desktop -->
<div class="hidden lg:fixed lg:inset-y-0 lg:z-50 lg:flex lg:w-72 lg:flex-col">
    <!-- Sidebar component, swap this element with another sidebar if you like -->
    <div class="relative flex grow flex-col gap-y-5 overflow-y-auto bg-red-600 px-6 pb-4 dark:bg-red-800 dark:after:pointer-events-none dark:after:absolute dark:after:inset-y-0 dark:after:right-0 dark:after:w-px dark:after:bg-white/10">
        <div class="flex h-16 shrink-0 items-center">
            <img src="https://tailwindcss.com/plus-assets/img/logos/mark.svg?color=white" alt="Your Company" class="h-8 w-auto" />
        </div>
        <nav class="flex flex-1 flex-col">
            <ul role="list" class="flex flex-1 flex-col gap-y-7">
                <li>
                    <ul role="list" class="-mx-2 space-y-1">
                        @foreach($topNav as $item)
                            <li>
                                <!-- Current:  -->
                                <a wire:navigate
{{--                                   wire:current="text-red-200 dark:text-red-100 hover:text-white hover:bg-red-700 dark:hover:bg-red-950/25"--}}
                                   href="{{ $item['link'] }}" class="group flex gap-x-3 rounded-md hover:bg-red-700 p-2 text-sm/6 font-semibold text-white hover:dark:bg-red-950/25">
                                    <x-dynamic-component :component="$item['icon']" class="mt-4" />
                                    {{ $item['text'] }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </li>
                <li>
                    <div class="text-xs/6 font-semibold text-red-200 dark:text-red-100">Community</div>
                    <ul role="list" class="-mx-2 mt-2 space-y-1">
                        @foreach($bottomNav as $item)
                            <li>
                                <!-- Current:  -->
                                <a wire:navigate
{{--                                   wire:current="text-red-200 dark:text-red-100 hover:text-white hover:bg-red-700 dark:hover:bg-red-950/25" --}}
                                   href="{{ $item['link'] }}" class="group flex gap-x-3 rounded-md hover:bg-red-700 p-2 text-sm/6 font-semibold text-white hoverdark:bg-red-950/25">
                                    <x-dynamic-component :component="$item['icon']" class="mt-4" />
                                    {{ $item['text'] }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </li>
{{--                <li class="mt-auto">--}}
{{--                    <a href="#" class="group -mx-2 flex gap-x-3 rounded-md p-2 text-sm/6 font-semibold text-red-200 hover:bg-red-700 hover:text-white dark:text-red-100 dark:hover:bg-red-950/25">--}}
{{--                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" data-slot="icon" aria-hidden="true" class="size-6 shrink-0 text-red-200 group-hover:text-white dark:text-red-100">--}}
{{--                            <path d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 0 1 0-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28Z" stroke-linecap="round" stroke-linejoin="round" />--}}
{{--                            <path d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" stroke-linecap="round" stroke-linejoin="round" />--}}
{{--                        </svg>--}}
{{--                        Settings--}}
{{--                    </a>--}}
{{--                </li>--}}
            </ul>
        </nav>
    </div>
</div>

<div class="lg:pl-72">
    <x-app.header />


    <main class="py-10">
        <div class="px-4 sm:px-6 lg:px-8">
            {{ $slot }}
        </div>
    </main>
</div>

<!-- Toast Notifications -->
<x-toast-notifications />

</body>
</html>

