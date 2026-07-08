<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- DISPEČER: Samo operativni panel i mapa --}}
            @role('dispecer')
                <div class="mb-6 space-y-6">
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-indigo-100 dark:border-indigo-900/30 px-6 py-4 flex items-center gap-3">
                        <span class="text-2xl">📋</span>
                        <div>
                            <h2 class="text-lg font-extrabold text-gray-900 dark:text-white">Operativni panel dispečera</h2>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Upravljajte prijavama, verifikujte ih i dodeljujte naloge terenskim ekipama.</p>
                        </div>
                    </div>

                    <livewire:dispatcher-dashboard />

                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow sm:rounded-2xl border border-gray-200 dark:border-gray-700">
                        <div class="p-6 text-gray-900 dark:text-gray-100">
                            <h3 class="text-lg font-semibold mb-2">Interaktivna mapa puteva</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Pregled svih prijava, deonica i infrastrukture na mreži puteva.</p>
                            <livewire:issue-map />
                        </div>
                    </div>
                </div>
            @endrole

            {{-- MENADŽER: Samo analitika i izveštaji --}}
            @role('menadzer')
                <div class="mb-6 space-y-6">
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-purple-100 dark:border-purple-900/30 px-6 py-4 flex items-center gap-3">
                        <span class="text-2xl">📊</span>
                        <div>
                            <h2 class="text-lg font-extrabold text-gray-900 dark:text-white">Analitički izveštaji menadžera</h2>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Pregled troškova, amortizacije, budžeta i toplotnih žarišta na putnoj mreži.</p>
                        </div>
                    </div>

                    <livewire:analytics />
                </div>
            @endrole


            @role('terenski_radnik')
                <livewire:worker-dashboard />
                
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow sm:rounded-lg mb-6 mt-6 border border-gray-200 dark:border-gray-700">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <h3 class="text-lg font-semibold mb-2">Interaktivna mapa puteva</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Kliknite bilo gde na mapu da prijavite problem (udarnu rupu, znak, itd).</p>
                        
                        <!-- Mesto za mapu -->
                        <livewire:issue-map />
                    </div>
                </div>
            @endrole

            @role('vozac')
                <livewire:driver-dashboard />
            @endrole
        </div>
    </div>
    
    <!-- Forma za prijavu koja iskace (Modal) -->
    <livewire:report-issue-form />
</x-app-layout>
