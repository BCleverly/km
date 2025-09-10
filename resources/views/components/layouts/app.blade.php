<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" @class([
    'h-full bg-white dark:bg-gray-900',
    'dark' => auth()->check() && auth()->user()->profile && auth()->user()->profile->effective_theme === 'dark'
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
      'h-full dark:bg-gray-900'
      ])>
    <el-dialog>
  <dialog id="sidebar" class="backdrop:bg-transparent lg:hidden">
    <el-dialog-backdrop class="fixed inset-0 bg-gray-900/80 transition-opacity duration-300 ease-linear data-closed:opacity-0"></el-dialog-backdrop>

    <div tabindex="0" class="fixed inset-0 flex focus:outline-none">
      <el-dialog-panel class="group/dialog-panel relative mr-16 flex w-full max-w-xs flex-1 transform transition duration-300 ease-in-out data-closed:-translate-x-full">
        <div class="absolute top-0 left-full flex w-16 justify-center pt-5 duration-300 ease-in-out group-data-closed/dialog-panel:opacity-0">
          <button type="button" command="close" commandfor="sidebar" class="-m-2.5 p-2.5 cursor-pointer hover:bg-red-600/20 rounded-lg transition-colors duration-200">
            <span class="sr-only">Close sidebar</span>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" data-slot="icon" aria-hidden="true" class="size-6 text-white">
              <path d="M6 18 18 6M6 6l12 12" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
          </button>
        </div>

        <!-- Sidebar component, swap this element with another sidebar if you like -->
        <div class="relative flex grow flex-col gap-y-5 overflow-y-auto bg-red-500 px-6 pb-4 dark:bg-red-900/30 dark:ring-1 dark:ring-white/10">
          <div class="flex h-16 shrink-0 items-center">
            <img src="https://tailwindcss.com/plus-assets/img/logos/mark.svg?color=white" alt="Your Company" class="h-8 w-auto" />
          </div>
          <nav class="flex flex-1 flex-col">
            <ul role="list" class="flex flex-1 flex-col gap-y-7">
              <li>
                <ul role="list" class="-mx-2 space-y-2">
                  <li>
                    <a href="{{ route('app.dashboard') }}"
                       wire:navigate
                       @class([
                           'group flex gap-x-3 rounded-md p-3 text-base font-semibold leading-relaxed',
                           'bg-red-600 dark:bg-red-800/20 text-white' => request()->routeIs('app.dashboard'),
                           'text-red-200 dark:text-red-200/80 hover:text-white hover:bg-red-600 dark:hover:bg-red-700/25' => !request()->routeIs('app.dashboard'),
                       ])>
                      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" data-slot="icon" aria-hidden="true" class="size-6 shrink-0 text-white">
                        <path d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" stroke-linecap="round" stroke-linejoin="round" />
                      </svg>
                      Dashboard
                    </a>
                  </li>
                  <li>
                    <a href="{{ route('app.tasks') }}"
                       wire:navigate
                       @class([
                           'group flex gap-x-3 rounded-md p-3 text-base font-semibold leading-relaxed',
                           'bg-red-600 dark:bg-red-800/20 text-white' => request()->routeIs('app.tasks'),
                           'text-red-200 dark:text-red-200/80 hover:text-white hover:bg-red-600 dark:hover:bg-red-700/25' => !request()->routeIs('app.tasks'),
                       ])>
                      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" data-slot="icon" aria-hidden="true" class="size-6 shrink-0 text-white">
                        <path d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" stroke-linecap="round" stroke-linejoin="round" />
                      </svg>
                      Tasks
                    </a>
                  </li>
                  <li>
                    <a href="{{ route('app.fantasies.index') }}"
                       wire:navigate
                       @class([
                           'group flex gap-x-3 rounded-md p-3 text-base font-semibold leading-relaxed',
                           'bg-red-600 dark:bg-red-800/20 text-white' => request()->routeIs('app.fantasies.*'),
                           'text-red-200 dark:text-red-200/80 hover:text-white hover:bg-red-600 dark:hover:bg-red-700/25' => !request()->routeIs('app.fantasies.*'),
                       ])>
                      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" data-slot="icon" aria-hidden="true" class="size-6 shrink-0 text-white">
                        <path d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" stroke-linecap="round" stroke-linejoin="round" />
                      </svg>
                      Fantasies
                    </a>
                  </li>
                  <li>
                    <a href="{{ route('app.stories.index') }}"
                       wire:navigate
                       @class([
                           'group flex gap-x-3 rounded-md p-3 text-base font-semibold leading-relaxed',
                           'bg-red-600 dark:bg-red-800/20 text-white' => request()->routeIs('app.stories.*'),
                           'text-red-200 dark:text-red-200/80 hover:text-white hover:bg-red-600 dark:hover:bg-red-700/25' => !request()->routeIs('app.stories.*'),
                       ])>
                      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" data-slot="icon" aria-hidden="true" class="size-6 shrink-0 text-white">
                        <path d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" stroke-linecap="round" stroke-linejoin="round" />
                      </svg>
                      Stories
                    </a>
                  </li>
                </ul>
              </li>
              <li>
                <div class="text-sm font-semibold text-red-100 dark:text-red-100/90 uppercase tracking-wide mb-3">Community</div>
                <ul role="list" class="-mx-2 mt-2 space-y-1">
                  <li>
                    <a href="{{ route('app.tasks.community') }}" wire:navigate class="group flex gap-x-3 rounded-md p-3 text-base font-semibold text-red-100 hover:bg-red-600 hover:text-white dark:text-red-100/90 dark:hover:bg-red-700/25 leading-relaxed transition-colors duration-200">
                      <span class="flex size-6 shrink-0 items-center justify-center rounded-lg border border-red-400 bg-red-500 text-[0.625rem] font-medium text-white dark:border-red-600/30 dark:bg-red-900/30">T</span>
                      <span class="truncate">Task Community</span>
                    </a>
                  </li>
                </ul>
              </li>
              <li class="mt-auto">
                <a href="#"
                   wire:navigate
                   class="group -mx-2 flex gap-x-3 rounded-md p-3 text-base font-semibold leading-relaxed text-red-200 dark:text-red-200/80 hover:text-white hover:bg-red-600 dark:hover:bg-red-700/25">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" data-slot="icon" aria-hidden="true" class="size-6 shrink-0 text-white">
                    <path d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 0 1 0-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28Z" stroke-linecap="round" stroke-linejoin="round" />
                    <path d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" stroke-linecap="round" stroke-linejoin="round" />
                  </svg>
                  Settings
                </a>
              </li>
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
  <div class="relative flex grow flex-col gap-y-5 overflow-y-auto bg-red-500 px-6 pb-4 dark:bg-red-900/30 dark:after:pointer-events-none dark:after:absolute dark:after:inset-y-0 dark:after:right-0 dark:after:w-px dark:after:bg-white/10">
    <div class="flex h-16 shrink-0 items-center">
      <img src="https://tailwindcss.com/plus-assets/img/logos/mark.svg?color=white" alt="Your Company" class="h-8 w-auto" />
    </div>
    <nav class="flex flex-1 flex-col">
      <ul role="list" class="flex flex-1 flex-col gap-y-7">
        <li>
          <ul role="list" class="-mx-2 space-y-1">
            <li>
              <a href="{{ route('app.dashboard') }}"
                 wire:navigate
                 class="group flex gap-x-3 rounded-md p-2 text-sm/6 font-semibold {{ request()->routeIs('app.dashboard') ? 'bg-red-600 dark:bg-red-800/20 text-white' : 'text-red-200 dark:text-red-200/80 hover:text-white hover:bg-red-600 dark:hover:bg-red-700/25' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" data-slot="icon" aria-hidden="true" class="size-6 shrink-0 text-white">
                  <path d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                Dashboard
              </a>
            </li>
            <li>
              <a href="{{ route('app.tasks') }}"
                 wire:navigate
                 class="group flex gap-x-3 rounded-md p-2 text-sm/6 font-semibold {{ request()->routeIs('app.tasks') ? 'bg-red-600 dark:bg-red-800/20 text-white' : 'text-red-200 dark:text-red-200/80 hover:text-white hover:bg-red-600 dark:hover:bg-red-700/25' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" data-slot="icon" aria-hidden="true" class="size-6 shrink-0 text-white">
                  <path d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                Tasks
              </a>
            </li>
            <li>
              <a href="{{ route('app.fantasies.index') }}"
                 wire:navigate
                 class="group flex gap-x-3 rounded-md p-2 text-sm/6 font-semibold {{ request()->routeIs('app.fantasies.*') ? 'bg-red-600 dark:bg-red-800/20 text-white' : 'text-red-200 dark:text-red-200/80 hover:text-white hover:bg-red-600 dark:hover:bg-red-700/25' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" data-slot="icon" aria-hidden="true" class="size-6 shrink-0 text-white">
                  <path d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                Fantasies
              </a>
            </li>
            <li>
              <a href="{{ route('app.stories.index') }}"
                 wire:navigate
                 class="group flex gap-x-3 rounded-md p-2 text-sm/6 font-semibold {{ request()->routeIs('app.stories.*') ? 'bg-red-600 dark:bg-red-800/20 text-white' : 'text-red-200 dark:text-red-200/80 hover:text-white hover:bg-red-600 dark:hover:bg-red-700/25' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" data-slot="icon" aria-hidden="true" class="size-6 shrink-0 text-white">
                  <path d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                Stories
              </a>
            </li>
          </ul>
        </li>
        <li>
          <div class="text-xs/6 font-semibold text-red-200 dark:text-red-200/80">Community</div>
          <ul role="list" class="-mx-2 mt-2 space-y-1">
            <li>
              <a href="{{ route('app.tasks.community') }}" wire:navigate class="group flex gap-x-3 rounded-md p-3 text-base font-semibold text-red-100 hover:bg-red-600 hover:text-white dark:text-red-100/90 dark:hover:bg-red-700/25 leading-relaxed transition-colors duration-200">
                <span class="flex size-6 shrink-0 items-center justify-center rounded-lg border border-red-400 bg-red-500 text-[0.625rem] font-medium text-white dark:border-red-600/30 dark:bg-red-900/30">T</span>
                <span class="truncate">Task Community</span>
              </a>
            </li>
          </ul>
        </li>
        <li class="mt-auto">
          <a href="#"
             wire:navigate
             class="group -mx-2 flex gap-x-3 rounded-md p-2 text-sm/6 font-semibold text-red-200 dark:text-red-200/80 hover:text-white hover:bg-red-600 dark:hover:bg-red-700/25">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" data-slot="icon" aria-hidden="true" class="size-6 shrink-0 text-white">
              <path d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 0 1 0-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28Z" stroke-linecap="round" stroke-linejoin="round" />
              <path d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
            Settings
          </a>
        </li>
      </ul>
    </nav>
  </div>
</div>

<!-- Notification System -->
<div x-data="notificationManager()" class="fixed top-4 right-4 z-[10000] space-y-2">
  <template x-for="notification in notifications" :key="notification.id">
    <div 
      x-show="notification.visible" 
      x-transition:enter="transition ease-out duration-300"
      x-transition:enter-start="opacity-0 transform translate-x-full"
      x-transition:enter-end="opacity-100 transform translate-x-0"
      x-transition:leave="transition ease-in duration-200"
      x-transition:leave-start="opacity-100 transform translate-x-0"
      x-transition:leave-end="opacity-0 transform translate-x-full"
      class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-lg p-4 max-w-sm"
    >
      <div class="flex items-start">
        <div class="flex-shrink-0">
          <svg x-show="notification.type === 'success'" class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
          </svg>
          <svg x-show="notification.type === 'error'" class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
          </svg>
          <svg x-show="notification.type === 'info'" class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
          </svg>
        </div>
        <div class="ml-3 flex-1">
          <p class="text-sm font-medium text-gray-900 dark:text-white" x-text="notification.message"></p>
        </div>
        <div class="ml-4 flex-shrink-0">
          <button @click="removeNotification(notification.id)" class="inline-flex text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
            <span class="sr-only">Close</span>
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>
      </div>
    </div>
  </template>
</div>

<script>
function notificationManager() {
  return {
    notifications: [],
    
    init() {
      // Listen for Livewire events
      Livewire.on('show-notification', (data) => {
        this.addNotification(data[0]);
      });
    },
    
    addNotification(data) {
      const notification = {
        id: Date.now() + Math.random(),
        message: data.message,
        type: data.type || 'info',
        visible: true
      };
      
      this.notifications.push(notification);
      
      // Auto-remove after 4 seconds
      setTimeout(() => {
        this.removeNotification(notification.id);
      }, 4000);
    },
    
    removeNotification(id) {
      const index = this.notifications.findIndex(n => n.id === id);
      if (index > -1) {
        this.notifications[index].visible = false;
        setTimeout(() => {
          this.notifications.splice(index, 1);
        }, 200);
      }
    }
  }
}
</script>

<div class="lg:pl-72">
  <div class="sticky top-0 z-40 flex h-16 shrink-0 items-center gap-x-4 border-b border-gray-200 bg-white px-4 shadow-xs sm:gap-x-6 sm:px-6 lg:px-8 dark:border-white/10 dark:bg-gray-900 dark:shadow-none">
    <button type="button" command="show-modal" commandfor="sidebar" class="-m-2.5 p-2.5 text-gray-700 hover:text-gray-900 lg:hidden dark:text-gray-400 dark:hover:text-white cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg transition-colors duration-200">
      <span class="sr-only">Open sidebar</span>
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" data-slot="icon" aria-hidden="true" class="size-6">
        <path d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" stroke-linecap="round" stroke-linejoin="round" />
      </svg>
    </button>

    <!-- Separator -->
    <div aria-hidden="true" class="h-6 w-px bg-gray-900/10 lg:hidden dark:bg-white/10"></div>

    <div class="flex flex-1 gap-x-4 self-stretch lg:gap-x-6">
      <form action="#" method="GET" class="grid flex-1 grid-cols-1">
        <input name="search" placeholder="Search" aria-label="Search" class="col-start-1 row-start-1 block size-full bg-white pl-8 text-base text-gray-900 outline-hidden placeholder:text-gray-400 sm:text-sm/6 dark:bg-gray-900 dark:text-white dark:placeholder:text-gray-500" />
        <svg viewBox="0 0 20 20" fill="currentColor" data-slot="icon" aria-hidden="true" class="pointer-events-none col-start-1 row-start-1 size-5 self-center text-gray-400">
          <path d="M9 3.5a5.5 5.5 0 1 0 0 11 5.5 5.5 0 0 0 0-11ZM2 9a7 7 0 1 1 12.452 4.391l3.328 3.329a.75.75 0 1 1-1.06 1.06l-3.329-3.328A7 7 0 0 1 2 9Z" clip-rule="evenodd" fill-rule="evenodd" />
        </svg>
      </form>
      <div class="flex items-center gap-x-4 lg:gap-x-6">
        <button type="button" class="-m-2.5 p-2.5 text-gray-400 hover:text-gray-500 dark:text-gray-400 dark:hover:text-white">
          <span class="sr-only">View notifications</span>
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" data-slot="icon" aria-hidden="true" class="size-6">
            <path d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" stroke-linecap="round" stroke-linejoin="round" />
          </svg>
        </button>

        <!-- Theme Selector -->
        <livewire:theme-selector />

        <!-- Separator -->
        <div aria-hidden="true" class="hidden lg:block lg:h-6 lg:w-px lg:bg-gray-900/10 dark:lg:bg-gray-100/10"></div>

        <!-- Profile dropdown -->
        <div class="relative" x-data="{ open: false }" @click.outside="open = false">
          <button class="relative flex items-center cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-800 rounded-md p-1 transition-colors" @click="open = !open">
            <span class="absolute -inset-1.5"></span>
            <span class="sr-only">Open user menu</span>
            <img src="{{ auth()->user()->profile_picture_url }}" alt="{{ auth()->user()->display_name }}" class="size-8 rounded-full bg-gray-50 outline -outline-offset-1 outline-black/5 dark:bg-gray-800 dark:outline-white/10" />
            <span class="hidden lg:flex lg:items-center">
              <span aria-hidden="true" class="ml-4 text-sm/6 font-semibold text-gray-900 dark:text-white">{{ auth()->user()->display_name }}</span>
              <svg viewBox="0 0 20 20" fill="currentColor" data-slot="icon" aria-hidden="true" class="ml-2 size-5 text-gray-400 dark:text-gray-500" :class="{ 'rotate-180': open }">
                <path d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" fill-rule="evenodd" />
              </svg>
            </span>
          </button>

          <!-- Dropdown menu -->
          <div x-show="open"
               x-cloak
               x-transition:enter="transition ease-out duration-100"
               x-transition:enter-start="transform opacity-0 scale-95"
               x-transition:enter-end="transform opacity-100 scale-100"
               x-transition:leave="transition ease-in duration-75"
               x-transition:leave-start="transform opacity-100 scale-100"
               x-transition:leave-end="transform opacity-0 scale-95"
               class="absolute right-0 z-10 mt-2 w-48 origin-top-right rounded-md bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none dark:bg-gray-800 dark:ring-white/10"
               role="menu"
               aria-orientation="vertical"
               aria-labelledby="user-menu-button"
               tabindex="-1">
            <a href="{{ route('app.profile') }}" wire:navigate class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700 cursor-pointer transition-colors" role="menuitem" tabindex="-1">Your profile</a>
            <a href="#" wire:navigate class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700 cursor-pointer transition-colors" role="menuitem" tabindex="-1">Settings</a>
            <form method="POST" action="{{ route('logout') }}" class="block">
                @csrf
                <button type="submit" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700 cursor-pointer transition-colors" role="menuitem" tabindex="-1">Sign out</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

  <main class="py-10">
    <div class="px-4 sm:px-6 lg:px-8">
    {{ $slot }}
    </div>
  </main>
</div>


    </body>
</html>
