<div class="flex h-screen overflow-hidden" x-data="{
    sidebarOpen: true,
    userDropdownOpen: false,
    searchTerm: '',
    searchResults: [],
    
    init() {
        // Handle responsive sidebar
        if (window.innerWidth < 768) {
            this.sidebarOpen = false;
        }
    }
}">
    <aside :class="{ 'w-64': sidebarOpen, 'w-20': !sidebarOpen }"
        class="flex flex-col h-screen bg-white border-r border-gray-200 shadow-sm transition-all duration-300">
        
        <div 
            :class="sidebarOpen ? 'min-h-[64px] p-2' : 'min-h-[81px]'"
            class="flex items-center justify-center border-b border-gray-200 transition-all duration-300"
        >
            <img 
                src="{{ asset('images/ipms-logo.svg') }}"
                class="drop-shadow-xl transition-all duration-300"
                :style="sidebarOpen ? 'height: 64px' : 'height: 40px'"
                alt="IPMS Logo"
            >
        </div>

        <nav class="flex-1 p-4 space-y-2 overflow-y-auto overflow-x-hidden custom-scroll">
            @php
                $currentProjectId = null;
                $isProjectMode = false;
                
                // Cek apakah sedang dalam mode edit/view project
                if (request()->route('project')) {
                    $currentProjectId = request()->route('project')->id ?? request()->route('project');
                    $isProjectMode = true;
                }
                
                // 1. Definisikan Menu Utama (Global)
                $mainItems = [
                    [
                        'name' => 'Dashboard',
                        'icon' => 'bi bi-speedometer2',
                        'url' => route('dashboard'),
                        'active' => request()->routeIs('dashboard*')
                    ],
                    [
                        'name' => 'Proyek Baru',
                        'icon' => 'bi bi-journal-plus', // Icon Folder Plus
                        'url' => route('new_project.index'), // Sesuai permintaan
                        'active' => (
                                    request()->routeIs('new_project*') ||
                                    request()->routeIs('project.create')
                                )
                    ],
                    [
                        'name' => 'Proyek Lampau',
                        'icon' => 'bi bi-archive', // Icon Archive
                        'url' => route('completed-periods.index'), // Sementara route sama
                        'active' => (request()->routeIs('completed-periods.index') && request('status') == 'completed') || request()->routeIs('completed-periods.*') || request()->routeIs('project.create-past')
                    ],
                    [
                        'name' => 'Tracking',
                        'icon' => 'bi bi-geo-alt', // Icon Lokasi/Tracking
                        'url' => route('tracking'),
                        'active' => request()->routeIs('tracking*')
                    ],
                    [
                        'name' => 'Export',
                        'icon' => 'bi bi-file-earmark-arrow-down', // Icon Export
                        'url' => route('export.index'),
                        'active' => request()->routeIs('export*')
                    ],
                ];

                // 2. Definisikan Menu Spesifik Proyek (Hanya muncul jika masuk ke project)
                $projectItems = [];
                if ($isProjectMode) {
                    $projectItems = [
                        ['divider' => true], // Penanda pemisah visual
                        [
                            'name' => 'Deskripsi',
                            'icon' => 'bi bi-card-text',
                            'url' => route('project.description', $currentProjectId),
                            'active' => request()->routeIs('description*') || request()->routeIs('project.description')
                        ],
                        [
                            'name' => 'Progress',
                            'icon' => 'bi bi-graph-up',
                            'url' => route('project.progress', $currentProjectId),
                            'active' => request()->routeIs('progress*') || request()->routeIs('project.progress')
                        ],
                        [
                            'name' => 'Aktivitas',
                            'icon' => 'bi bi-list-task',
                            'url' => route('project.activities', $currentProjectId),
                            'active' => request()->routeIs('activities*') || request()->routeIs('project.activities') || request()->routeIs('aktivitas*')
                        ],
                        [
                            'name' => 'Isu',
                            'icon' => 'bi bi-exclamation-triangle',
                            'url' => route('project.issues', $currentProjectId),
                            'active' => request()->routeIs('issues*') || request()->routeIs('project.issues') || request()->routeIs('isu*')
                        ],
                        [
                            'name' => 'Dokumentasi',
                            'icon' => 'bi bi-folder',
                            'url' => route('project.documentation', $currentProjectId),
                            'active' => request()->routeIs('documentation*') || request()->routeIs('project.documentation') || request()->routeIs('dokumentasi*')
                        ],
                    ];
                }

                // Gabungkan menu
                $menuItems = array_merge($mainItems, $projectItems);
            @endphp

            @foreach ($menuItems as $item)
            {{-- Render Divider jika ada --}}
            @if(isset($item['divider']))
                <div class="my-2 border-t border-gray-200" x-show="sidebarOpen">
                    <span class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider mt-2 block">
                        Menu Proyek
                    </span>
                </div>
                <div class="my-2 border-t border-gray-200" x-show="!sidebarOpen"></div>
            @else
                {{-- Render Menu Item Biasa --}}
                <a href="{{ $item['url'] }}" 
                    id="menu-{{ Str::slug($item['name']) }}"
                    @mouseenter="!sidebarOpen && Alpine.store('tooltip').show('{{ $item['name'] }}', document.getElementById('menu-{{ Str::slug($item['name']) }}'))"
                    @mouseleave="Alpine.store('tooltip').hide()"
                    :class="{ 'justify-center': !sidebarOpen }"
                    class="group relative flex items-center p-3 rounded-lg transition-all duration-200
                        {{ $item['active'] 
                            ? 'text-ipms-blue bg-gradient-to-r from-blue-50 to-transparent font-medium'
                            : 'text-gray-700 hover:bg-blue-50 hover:text-ipms-blue' }}">
                    
                    {{-- [BARU] Indikator Kuning Absolut (Pasti Muncul) --}}
                    @if($item['active'])
                        <div class="absolute left-0 top-1 bottom-1 w-1 bg-yellow-500 rounded-r shadow-sm"
                            style="background-color: #FFD700;"> {{-- Inline style untuk safety --}}
                        </div>
                    @endif

                    <i class="{{ $item['icon'] }} text-lg {{ $item['active'] ? 'text-ipms-blue' : 'text-gray-500' }}"
                        :class="{ 'mr-0': !sidebarOpen, 'mr-3': sidebarOpen }"></i>
                    
                    <span x-show="sidebarOpen" x-transition class="text-sm">{{ $item['name'] }}</span>
                </a>
            @endif
        @endforeach         
        </nav>

        <div class="mt-auto border-t border-gray-200">
            <div class="p-4">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" 
                        :class="{ 'justify-center': !sidebarOpen }"
                        class="flex items-center w-full p-3 rounded-lg text-gray-700 hover:bg-red-50 hover:text-red-600 transition-colors">
                        <i class="bi bi-box-arrow-right text-lg" :class="{ 'mr-0': !sidebarOpen, 'mr-3': sidebarOpen }"></i>
                        <span x-show="sidebarOpen" x-transition>Logout</span>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <div class="flex-1 flex flex-col h-screen overflow-hidden bg-gray-50">
        <nav class="bg-white border-b border-gray-200 flex-shrink-0 z-20">
            <div class="px-4 py-3">
                <div class="flex items-center justify-between">
                    <button @click="sidebarOpen = !sidebarOpen" 
                        class="p-2 rounded-lg hover:bg-gray-100 transition-colors">
                        <i class="bi bi-list text-2xl text-ipms-blue"></i>
                    </button>

                    <div class="flex-1 max-w-lg mx-4">
                        </div>

                    <div class="flex items-center space-x-4">
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open"
                                class="flex items-center space-x-3 p-2 rounded-lg hover:bg-gray-100 transition-colors">
                                <img src="{{ str_starts_with(Auth::user()->avatar_url ?? '', 'images/avatar/') ? asset('storage/' . Auth::user()->avatar_url) : asset(Auth::user()->avatar_url ?? 'images/avatar-default.svg') }}"
                                    alt="Avatar"
                                    class="w-8 h-8 rounded-full object-cover">
                                <span x-show="sidebarOpen" class="text-sm font-medium text-gray-700 hidden md:inline">
                                    {{ Auth::user()->name }}
                                </span>
                                <i x-show="sidebarOpen" class="bi bi-chevron-down text-xs text-gray-500"></i>
                            </button>

                            <div x-show="open" 
                                @click.away="open = false"
                                x-transition:enter="transition ease-out duration-100"
                                x-transition:enter-start="opacity-0 scale-95"
                                x-transition:enter-end="opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-75"
                                x-transition:leave-start="opacity-100 scale-100"
                                x-transition:leave-end="opacity-0 scale-95"
                                class="absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-lg border border-gray-200 py-2"
                                style="display: none;">
                                
                                <div class="px-4 py-3 border-b border-gray-200 bg-gradient-to-r from-ipms-blue to-ipms-light-blue rounded-t-lg">
                                    <p class="text-sm font-semibold text-white">{{ Auth::user()->name }}</p>
                                    <p class="text-xs text-white opacity-90">{{ Auth::user()->job_position ?? Auth::user()->role }}</p>
                                </div>

                                <a href="{{ route('profile.index') }}" 
                                    class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                                    <i class="fas fa-user w-5 mr-3 text-gray-500"></i>
                                    Profil
                                </a>

                                @if(auth()->user()->role == 'admin')
                                    <hr class="my-2 border-gray-200">
                                    <a href="{{ route('user.management') }}" 
                                        class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                                        <i class="fas fa-users-cog w-5 mr-3 text-gray-500"></i>
                                        Manajemen Akun
                                    </a>
                                @endif

                                <hr class="my-2 border-gray-200">
                                
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit"
                                        class="w-full flex items-center px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                        <i class="fas fa-sign-out-alt w-5 mr-3"></i>
                                        Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <main class="flex-1 overflow-y-auto p-4 md:p-6 custom-scroll">
            @yield('content')
            
            <div class="h-10"></div>
        </main>
    </div>

    <div x-data 
        x-show="$store.tooltip.visible && !sidebarOpen" 
        x-cloak 
        x-text="$store.tooltip.text"
        x-bind:style="{
            top: $store.tooltip.y + 'px',
            left: $store.tooltip.x + 'px'
        }"
        class="fixed z-50 px-3 py-2 text-xs text-white bg-gray-900 rounded shadow-lg pointer-events-none transition-all duration-200"
        style="transform: translateY(-50%);">
    </div>
</div>