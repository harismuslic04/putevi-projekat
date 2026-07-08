<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Leva strana: Interaktivna mapa -->
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex justify-between items-center mb-4">
                <div>
                    <h3 class="text-xl font-extrabold text-gray-900 dark:text-white">Interaktivna mapa puteva</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Kliknite na mapu da označite tačnu GPS lokaciju problema koji želite da prijavite.</p>
                </div>
            </div>
            
            <livewire:issue-map />
        </div>
    </div>

    <!-- Desna strana: Moje prijave -->
    <div class="space-y-6">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 p-6 flex flex-col h-[585px]">
            <h3 class="text-xl font-extrabold text-gray-900 dark:text-white mb-2">Moje prijave oštećenja</h3>
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-6">Pratite status i napredak sanacije problema koje ste prijavili.</p>

            <div class="flex-1 overflow-y-auto pr-2 space-y-6 scrollbar-thin">
                @forelse($myReports as $report)
                    <div class="bg-gray-50 dark:bg-gray-750 p-4 rounded-xl border border-gray-150 dark:border-gray-700 space-y-4">
                        <div class="flex justify-between items-start">
                            <div>
                                <span class="px-2 py-0.5 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 text-xs font-semibold rounded-md">
                                    {{ $report->type }}
                                </span>
                                <h4 class="text-sm font-bold text-gray-800 dark:text-white mt-1.5 truncate max-w-[180px]">
                                    {{ $report->description }}
                                </h4>
                            </div>
                            <span class="text-[10px] text-gray-400 dark:text-gray-500">
                                {{ $report->reported_at ? $report->reported_at->format('d.m.Y H:i') : $report->created_at->format('d.m.Y H:i') }}
                            </span>
                        </div>

                        <!-- Prikaz statusa -->
                        @if($report->status === 'odbijeno')
                            <div class="flex items-center gap-2 p-2 bg-red-50 dark:bg-red-950/20 text-red-700 dark:text-red-400 rounded-lg text-xs font-medium">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Prijava je odbijena / neispravna.
                            </div>
                        @elseif($report->status === 'duplikat')
                            <div class="flex items-center gap-2 p-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg text-xs">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"></path></svg>
                                <span>Duplikat (Spojeno sa prijavom <strong class="underline">#{{ $report->duplicate_of_id }}</strong>)</span>
                            </div>
                        @else
                            <!-- Koraci toka: Prijavljeno -> Verifikovano -> Nalog izdat -> Sanirano -->
                            <div class="relative flex items-center justify-between w-full mt-2">
                                <!-- Pozadinska linija -->
                                <div class="absolute left-0 right-0 top-1/2 -translate-y-1/2 h-0.5 bg-gray-200 dark:bg-gray-700 z-0"></div>
                                
                                <!-- Progres linija -->
                                @php
                                    $step = 1;
                                    if ($report->status === 'verifikovano') $step = 2;
                                    if ($report->status === 'nalog_izdat') $step = 3;
                                    if ($report->status === 'sanirano') $step = 4;
                                @endphp
                                <div class="absolute left-0 top-1/2 -translate-y-1/2 h-0.5 bg-indigo-600 z-0 transition-all duration-500" 
                                     style="width: {{ (($step - 1) / 3) * 100 }}%"></div>

                                <!-- Korak 1: Prijavljeno -->
                                <div class="flex flex-col items-center z-10">
                                    <div class="w-6 h-6 rounded-full flex items-center justify-center text-[10px] font-bold {{ $step >= 1 ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-500 dark:bg-gray-700' }}">
                                        1
                                    </div>
                                    <span class="text-[9px] font-medium mt-1 text-gray-600 dark:text-gray-400">Prijavljeno</span>
                                </div>

                                <!-- Korak 2: Verifikovano -->
                                <div class="flex flex-col items-center z-10">
                                    <div class="w-6 h-6 rounded-full flex items-center justify-center text-[10px] font-bold {{ $step >= 2 ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-500 dark:bg-gray-700' }}">
                                        2
                                    </div>
                                    <span class="text-[9px] font-medium mt-1 text-gray-600 dark:text-gray-400">Verifikovano</span>
                                </div>

                                <!-- Korak 3: Nalog izdat -->
                                <div class="flex flex-col items-center z-10">
                                    <div class="w-6 h-6 rounded-full flex items-center justify-center text-[10px] font-bold {{ $step >= 3 ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-500 dark:bg-gray-700' }}">
                                        3
                                    </div>
                                    <span class="text-[9px] font-medium mt-1 text-gray-600 dark:text-gray-400 font-semibold">Nalog izdat</span>
                                </div>

                                <!-- Korak 4: Sanirano -->
                                <div class="flex flex-col items-center z-10">
                                    <div class="w-6 h-6 rounded-full flex items-center justify-center text-[10px] font-bold {{ $step >= 4 ? 'bg-green-600 text-white' : 'bg-gray-200 text-gray-500 dark:bg-gray-700' }}">
                                        ✓
                                    </div>
                                    <span class="text-[9px] font-medium mt-1 text-gray-600 dark:text-gray-400">Sanirano</span>
                                </div>
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="flex flex-col items-center justify-center h-48 text-center border-2 border-dashed border-gray-200 dark:border-gray-700 rounded-xl p-4">
                        <span class="text-3xl mb-2">🚗</span>
                        <p class="text-sm font-semibold text-gray-600 dark:text-gray-400">Nemate aktivnih prijava</p>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Ukoliko uočite rupu ili oštećenje, kliknite na mapu levo.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
