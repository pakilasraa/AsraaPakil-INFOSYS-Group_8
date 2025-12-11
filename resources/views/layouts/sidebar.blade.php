<aside class="fixed inset-y-0 left-0 w-[240px] bg-[#3d2b1f] text-white hidden md:flex flex-col">
    <div class="h-16 flex items-center px-4 border-b border-white/10">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
            <div
                x-data="{ c:0, t:null, tap(){ clearTimeout(this.t); this.c++; if(this.c>=5){ this.c=0; window.dispatchEvent(new CustomEvent('open-easter')); } this.t=setTimeout(()=>this.c=0, 800); } }"
                @click.prevent.stop="tap()"
                class="h-9 w-9 rounded-full bg-white/20 flex items-center justify-center cursor-pointer select-none"
                title="Logo"
                aria-label="Logo">
                <img src="{{ asset('images/images.png') }}" alt="Logo" class="h-16 w-16 object-contain" />
            </div>
            <span class="font-semibold">DeSeventeen POS</span>
        </a>
    </div>
    <nav class="flex-1 overflow-y-auto py-4">
        <ul class="px-2 space-y-1">
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
                <a href="{{ route('admin.orders.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md {{ request()->routeIs('admin.orders.index') ? 'bg-[#E9CDAF] text-[#3d2b1f] border-l-4 border-[#C59B78]' : 'hover:bg-white/10' }}">
                    <span class="text-xl">📦</span>
                    <span class="text-sm font-medium">Orders</span> <!-- Added this line for Orders -->
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
    </nav>
</aside>

