<nav x-data="{ open: false }" class="bg-gradient-to-r from-blue-600 to-blue-800 shadow-lg">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="flex items-center space-x-2">
                        <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M3 1a1 1 0 000 2h1.22l.305 1.222a.997.997 0 00.01.042l1.358 5.43-.893.892C3.74 11.846 4.632 14 6.414 14H15a1 1 0 000-2H6.414l1-1H14a1 1 0 00.894-.553l3-6A1 1 0 0017 3H6.28l-.31-1.243A1 1 0 005 1H3zM16 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM6.5 18a1.5 1.5 0 100-3 1.5 1.5 0 000 3z"/>
                        </svg>
                        <span class="text-xl font-bold text-white">TechMart</span>
                    </a>
                </div>

                <div class="hidden space-x-4 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="text-white hover:text-blue-200">
                        {{ __('Dashboard') }}
                    </x-nav-link>
                    <x-nav-link :href="route('products.index')" :active="request()->routeIs('products.index') || request()->routeIs('products.show')" class="text-white hover:text-blue-200">
                        {{ __('Products') }}
                    </x-nav-link>
                    @can('manage-products')
                        <x-nav-link :href="route('products.manage')" :active="request()->routeIs('products.manage') || request()->routeIs('products.create') || request()->routeIs('products.edit')" class="text-white hover:text-blue-200">
                            {{ __('My Products') }}
                        </x-nav-link>
                    @endcan
                    @can('access-cart')
                        <x-nav-link :href="route('cart.index')" :active="request()->routeIs('cart.*')" class="text-white hover:text-blue-200">
                            <span class="flex items-center">
                                <svg class="w-5 h-5 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M3 1a1 1 0 000 2h1.22l.305 1.222a.997.997 0 00.01.042l1.358 5.43-.893.892C3.74 11.846 4.632 14 6.414 14H15a1 1 0 000-2H6.414l1-1H14a1 1 0 00.894-.553l3-6A1 1 0 0017 3H6.28l-.31-1.243A1 1 0 005 1H3z"/>
                                </svg>
                                {{ __('Cart') }}
                            </span>
                        </x-nav-link>
                    @endcan
                    @can('view-orders')
                        <x-nav-link :href="route('orders.index')" :active="request()->routeIs('orders.*')" class="text-white hover:text-blue-200">
                            {{ __('Orders') }}
                        </x-nav-link>
                    @endcan
                    @can('manage-vouchers')
                        <x-nav-link :href="route('vouchers.index')" :active="request()->routeIs('vouchers.*')" class="text-white hover:text-blue-200">
                            {{ __('Vouchers') }}
                        </x-nav-link>
                    @endcan
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ms-6">
                @auth
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button 
                                class="inline-flex items-center gap-2 px-4 py-2 border border-blue-400 text-sm leading-4 font-medium rounded-md text-white bg-blue-700 hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-300 transition"
                            >
                                <img 
                                    class="h-8 w-8 rounded-full object-cover border-2 border-white" 
                                    src="{{ Auth::user()->photo_url ?? 'https://avatars.githubusercontent.com/u/35440139?v=4' }}" 
                                    alt="{{ Auth::user()->name ?? 'Guest' }}" 
                                />
                                <span>{{ Auth::user()->name ?? 'Guest' }}</span>
                                <span class="ml-2 px-2 py-0.5 text-xs font-semibold rounded-full bg-white/10 border border-white/30">
                                    {{ ucfirst(Auth::user()->role) }}
                                </span>
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="route('profile.edit')">
                                {{ __('Profile') }}
                            </x-dropdown-link>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault(); this.closest('form').submit();">
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                @endauth

                @guest
                    <div class="flex items-center gap-3">
                        <a href="{{ route('login') }}" class="px-4 py-2 text-sm font-semibold text-white border border-white/40 rounded-lg hover:bg-white/10 transition">
                            {{ __('Log in') }}
                        </a>
                        <a href="{{ route('register') }}" class="px-4 py-2 text-sm font-semibold text-blue-600 bg-white rounded-lg shadow hover:bg-blue-50 transition">
                            {{ __('Register') }}
                        </a>
                    </div>
                @endguest
            </div>

            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-white hover:text-blue-200 hover:bg-blue-700 focus:outline-none focus:bg-blue-700 focus:text-white transition">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-blue-700">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="text-white">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('products.index')" :active="request()->routeIs('products.*')" class="text-white">
                {{ __('Products') }}
            </x-responsive-nav-link>
            @can('manage-products')
                <x-responsive-nav-link :href="route('products.manage')" :active="request()->routeIs('products.manage')" class="text-white">
                    {{ __('My Products') }}
                </x-responsive-nav-link>
            @endcan
            @can('access-cart')
                <x-responsive-nav-link :href="route('cart.index')" :active="request()->routeIs('cart.*')" class="text-white">
                    {{ __('Cart') }}
                </x-responsive-nav-link>
            @endcan
            @can('view-orders')
                <x-responsive-nav-link :href="route('orders.index')" :active="request()->routeIs('orders.*')" class="text-white">
                    {{ __('Orders') }}
                </x-responsive-nav-link>
            @endcan
            @can('manage-vouchers')
                <x-responsive-nav-link :href="route('vouchers.index')" :active="request()->routeIs('vouchers.*')" class="text-white">
                    {{ __('Vouchers') }}
                </x-responsive-nav-link>
            @endcan
        </div>

        @auth
            <div class="pt-4 pb-1 border-t border-blue-600">
                <div class="px-4">
                    <div class="font-medium text-base text-white">{{ Auth::user()->name }}</div>
                    <div class="font-medium text-sm text-blue-200">{{ Auth::user()->email }}</div>
                    <div class="inline-flex items-center px-2 py-0.5 mt-2 text-xs font-semibold text-white bg-white/10 border border-white/20 rounded-full">
                        {{ __('Role:') }} {{ ucfirst(Auth::user()->role) }}
                    </div>
                </div>

                <div class="mt-3 space-y-1">
                    <x-responsive-nav-link :href="route('profile.edit')" class="text-white">
                        {{ __('Profile') }}
                    </x-responsive-nav-link>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-responsive-nav-link :href="route('logout')"
                                onclick="event.preventDefault(); this.closest('form').submit();" class="text-white">
                            {{ __('Log Out') }}
                        </x-responsive-nav-link>
                    </form>
                </div>
            </div>
        @endauth

        @guest
            <div class="pt-4 pb-6 border-t border-blue-600">
                <div class="space-y-2 px-4">
                    <x-responsive-nav-link :href="route('login')" class="text-white">
                        {{ __('Log in') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('register')" class="text-white">
                        {{ __('Register') }}
                    </x-responsive-nav-link>
                </div>
            </div>
        @endguest
    </div>
</nav>