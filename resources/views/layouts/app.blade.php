<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div x-data="{ open:false, easter:false }" @open-easter.window="easter=true" class="min-h-screen bg-gray-100">
            <!-- Mobile top bar -->
            <div class="md:hidden flex items-center justify-between h-14 px-3 bg-[#3d2b1f] text-white">
                <button @click="open = true" aria-label="Open menu" class="p-2 rounded-md">
                    <!-- Visible hamburger -->
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-7 h-7 text-white"><path d="M3 6h18M3 12h18M3 18h18"/></svg>
                </button>
                <span class="font-semibold">DeSeventeen POS</span>
                <div class="w-9"></div>
            </div>

            <!-- Sidebar desktop -->
            @include('layouts.sidebar')

            <!-- Mobile drawer -->
            <div x-show="open" x-transition.opacity class="fixed inset-0 bg-black/40 z-40 md:hidden" @click="open=false"></div>
            <div x-show="open" x-transition:enter="transition transform ease-out duration-200" x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transition transform ease-in duration-150" x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full" class="fixed inset-y-0 left-0 w-[240px] bg-[#3d2b1f] text-white z-50 md:hidden">
                <div class="h-14 flex items-center px-4 border-b border-white/10">
                    <div
                        x-data="{ c:0, t:null, tap(){ clearTimeout(this.t); this.c++; if(this.c>=5){ this.c=0; window.dispatchEvent(new CustomEvent('open-easter')); } this.t=setTimeout(()=>this.c=0, 800); } }"
                        @click.prevent.stop="tap()"
                        class="h-8 w-8 rounded-full bg-white/20 flex items-center justify-center cursor-pointer select-none mr-2"
                        title="Logo"
                        aria-label="Logo">
                        <img src="{{ asset('images/images.png') }}" alt="Logo" class="h-16 w-16 object-contain" />
                    </div>
                    <span class="font-semibold">Menu</span>
                    <button class="ml-auto p-2" @click="open=false" aria-label="Close menu">✖</button>
                </div>
                <div class="overflow-y-auto h-[calc(100%-56px)] p-2">
                    <ul class="space-y-1">
                        <li>
                            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2 rounded-md {{ request()->routeIs('dashboard') ? 'bg-[#E9CDAF] text-[#3d2b1f] border-l-4 border-[#C59B78]' : 'hover:bg-white/10' }}">
                                <span class="text-xl">🏠</span>
                                <span class="text-sm font-medium">Dashboard</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('pos.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md {{ request()->routeIs('pos.index') ? 'bg-[#E9CDAF] text-[#3d2b1f] border-l-4 border-[#C59B78]' : 'hover:bg-white/10' }}">
                                <span class="text-xl">🛒</span>
                                <span class="text-sm font-medium">POS</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('products.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md {{ request()->routeIs('products.index') ? 'bg-[#E9CDAF] text-[#3d2b1f] border-l-4 border-[#C59B78]' : 'hover:bg-white/10' }}">
                                <span class="text-xl">🍵</span>
                                <span class="text-sm font-medium">Products</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('categories.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md {{ request()->routeIs('categories.index') ? 'bg-[#E9CDAF] text-[#3d2b1f] border-l-4 border-[#C59B78]' : 'hover:bg-white/10' }}">
                                <span class="text-xl">📂</span>
                                <span class="text-sm font-medium">Categories</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('reports.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md {{ request()->routeIs('reports.index') ? 'bg-[#E9CDAF] text-[#3d2b1f] border-l-4 border-[#C59B78]' : 'hover:bg-white/10' }}">
                                <span class="text-xl">📊</span>
                                <span class="text-sm font-medium">Reports</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('analytics.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md {{ request()->routeIs('analytics.index') ? 'bg-[#E9CDAF] text-[#3d2b1f] border-l-4 border-[#C59B78]' : 'hover:bg-white/10' }}">
                                <span class="text-xl">📈</span>
                                <span class="text-sm font-medium">Analytics</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.orders.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md {{ request()->routeIs('admin.orders.index') ? 'bg-[#E9CDAF] text-[#3d2b1f] border-l-4 border-[#C59B78]' : 'hover:bg-white/10' }}">
                                <span class="text-xl">📦</span>
                                <span class="text-sm font-medium">Orders</span> <!-- Added this line for Orders -->
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-3 py-2 rounded-md {{ request()->routeIs('profile.edit') ? 'bg-[#E9CDAF] text-[#3d2b1f] border-l-4 border-[#C59B78]' : 'hover:bg-white/10' }}">
                                <span class="text-xl">👤</span>
                                <span class="text-sm font-medium">Profile</span>
                            </a>
                        </li>
                        <li class="pt-4 border-t border-white/10 mt-4">
                            <form method="POST" action="{{ route('logout') }}" class="px-2">
                                @csrf
                                <button type="submit" class="w-full flex items-center gap-3 px-3 py-2 rounded-md hover:bg-white/10">
                                    <span class="text-xl">🚪</span>
                                    <span class="text-sm font-medium">Logout</span>
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Main content -->
            <main class="md:ml-[240px] p-4 md:p-6">
                {{ $slot }}
            </main>

            <!-- Easter egg modal -->
            <div x-cloak x-show="easter" x-transition.opacity class="fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4">
                <div class="bg-white rounded-2xl shadow-xl max-w-md w-full p-6 text-center">
                    <div class="text-4xl mb-2">😹</div>
                    <h2 class="text-xl font-semibold text-cafe-900 mb-1">Ihhh ang Bangissss!</h2>
                    <p class="text-cafe-700">Napaka-pogi mo Carl...</p>
                    <button class="mt-4 px-4 py-2 rounded-md btn-cafe" @click="easter=false">Close</button>
                </div>
            </div>
        </div>
    </body>
</html>
