<div class="mt-6 space-y-8" x-data="analyticsDashboard()" x-init="initDashboard()">
    <!-- Gornji Red sa KPI Karticama -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <!-- Ukupni Troškovi -->
        <div class="bg-gradient-to-br from-indigo-500 to-purple-600 dark:from-indigo-600 dark:to-purple-800 text-white rounded-2xl shadow-xl p-6 transition duration-300 hover:scale-105">
            <div class="flex justify-between items-center mb-2">
                <span class="text-xs font-bold uppercase tracking-wider opacity-90">Utrošen Budžet</span>
                <span class="p-2 bg-white bg-opacity-20 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </span>
            </div>
            <div class="text-2xl font-black">{{ number_format($totalBudget, 2) }} RSD</div>
            <p class="text-[10px] text-indigo-100 mt-2">Ukupni operativni i materijalni troškovi</p>
        </div>

        <!-- Amortizacija Mašina -->
        <div class="bg-gradient-to-br from-amber-500 to-orange-600 dark:from-amber-600 dark:to-orange-850 text-white rounded-2xl shadow-xl p-6 transition duration-300 hover:scale-105">
            <div class="flex justify-between items-center mb-2">
                <span class="text-xs font-bold uppercase tracking-wider opacity-90">Amortizacija Mašina</span>
                <span class="p-2 bg-white bg-opacity-20 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"></path></svg>
                </span>
            </div>
            <div class="text-2xl font-black">{{ number_format($totalDepreciation, 2) }} RSD</div>
            <p class="text-[10px] text-amber-100 mt-2">Akumulisana amortizacija kamiona i valjaka</p>
        </div>

        <!-- Vreme Reakcije (Sanacije) -->
        <div class="bg-gradient-to-br from-emerald-500 to-teal-600 dark:from-emerald-600 dark:to-teal-800 text-white rounded-2xl shadow-xl p-6 transition duration-300 hover:scale-105">
            <div class="flex justify-between items-center mb-2">
                <span class="text-xs font-bold uppercase tracking-wider opacity-90">Vreme Sanacije</span>
                <span class="p-2 bg-white bg-opacity-20 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </span>
            </div>
            <div class="text-2xl font-black">{{ $avgResponseTime }} sati</div>
            <p class="text-[10px] text-emerald-100 mt-2">Prosečno vreme od prijave do završetka</p>
        </div>

        <!-- Hitna Reakcija -->
        <div class="bg-gradient-to-br from-rose-500 to-red-650 dark:from-rose-600 dark:to-red-800 text-white rounded-2xl shadow-xl p-6 transition duration-300 hover:scale-105">
            <div class="flex justify-between items-center mb-2">
                <span class="text-xs font-bold uppercase tracking-wider opacity-90">Hitna Reakcija</span>
                <span class="p-2 bg-white bg-opacity-20 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                </span>
            </div>
            <div class="text-2xl font-black">{{ $avgUrgentResponseTime }} sati</div>
            <p class="text-[10px] text-rose-100 mt-2">Prosek za hitne prijave (sneg, ulje, autoput)</p>
        </div>
    </div>

    <!-- Srednji Red: Budžeti i Grafikoni -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Budžetski modul (Redovno vs. Vanredno) -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 flex flex-col justify-between">
            <div>
                <h4 class="text-lg font-bold text-gray-800 dark:text-white mb-1">Budžetiranje i Održavanje</h4>
                <p class="text-xs text-gray-500 mb-6">Poređenje planskog redovnog održavanja naspram vanrednih sanacija oštećenja.</p>
                
                <div class="space-y-6">
                    <!-- Redovno održavanje -->
                    <div>
                        <div class="flex justify-between text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                            <span>📅 Redovno Održavanje</span>
                            <span>{{ number_format($spentRegular, 0) }} / {{ number_format($budgetRegularLimit, 0) }} RSD</span>
                        </div>
                        @php
                            $percRegular = $budgetRegularLimit > 0 ? min(($spentRegular / $budgetRegularLimit) * 100, 100) : 0;
                        @endphp
                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3">
                            <div class="bg-emerald-500 h-3 rounded-full transition-all duration-500" style="width: {{ $percRegular }}%"></div>
                        </div>
                        <span class="text-[10px] text-gray-400 mt-1 block">Košenje bankina, čišćenje snega, sijalice, horizontalne linije</span>
                    </div>

                    <!-- Vanredno održavanje -->
                    <div>
                        <div class="flex justify-between text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                            <span>🚨 Vanredne Sanacije</span>
                            <span>{{ number_format($spentExtraordinary, 0) }} / {{ number_format($budgetExtraordinaryLimit, 0) }} RSD</span>
                        </div>
                        @php
                            $percExtraordinary = $budgetExtraordinaryLimit > 0 ? min(($spentExtraordinary / $budgetExtraordinaryLimit) * 100, 100) : 0;
                        @endphp
                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3">
                            <div class="bg-rose-500 h-3 rounded-full transition-all duration-500" style="width: {{ $percExtraordinary }}%"></div>
                        </div>
                        <span class="text-[10px] text-gray-400 mt-1 block">Udarne rupe, oboreni znakovi, kvarovi semafora iz prijava građana</span>
                    </div>
                </div>
            </div>
            
            <div class="mt-6 pt-4 border-t border-gray-150 dark:border-gray-700 text-xs text-gray-500 italic">
                Redovnim planiranjem smanjujemo skupe vanredne sanacije za više od 40%.
            </div>
        </div>

        <!-- Grafikoni -->
        <div class="lg:col-span-2 bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700">
            <h4 class="text-lg font-bold text-gray-800 dark:text-white mb-6">Operativna i Finansijska Analitika</h4>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Pie Chart za strukturu troškova -->
                <div class="space-y-2">
                    <h5 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Struktura troškova (Materijali vs Mašine)</h5>
                    <div class="relative h-48 flex justify-center">
                        <canvas id="costStructureChart"></canvas>
                    </div>
                </div>

                <!-- Bar Chart za broj prijava po tipu -->
                <div class="space-y-2">
                    <h5 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Broj prijava po tipu oštećenja</h5>
                    <div class="relative h-48">
                        <canvas id="issuesByTypeChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Evidencija materijala i mehanizacije -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Tabela utrošenih materijala -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 flex flex-col">
            <div class="flex items-center gap-2 mb-1">
                <span class="text-xl">📦</span>
                <h4 class="text-lg font-bold text-gray-800 dark:text-white">Evidencija utrošenog materijala</h4>
            </div>
            <p class="text-xs text-gray-500 mb-4">Tone asfalta, litri farbe, zamenjeni znakovi, so za posipanje.</p>
            
            <div class="overflow-x-auto flex-1">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-xs">
                    <thead class="bg-gray-50 dark:bg-gray-750">
                        <tr>
                            <th class="px-4 py-2.5 text-left text-gray-500 dark:text-gray-400 uppercase">Materijal</th>
                            <th class="px-4 py-2.5 text-right text-gray-500 dark:text-gray-400 uppercase">Količina</th>
                            <th class="px-4 py-2.5 text-right text-gray-500 dark:text-gray-400 uppercase">Jed. mere</th>
                            <th class="px-4 py-2.5 text-right text-gray-500 dark:text-gray-400 uppercase">Ukupna cena</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($materialUsage as $mat)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-750">
                                <td class="px-4 py-3 font-semibold text-gray-800 dark:text-gray-300">
                                    @if(str_contains($mat->name, 'Asfalt'))
                                        🛣️
                                    @elseif(str_contains($mat->name, 'so') || str_contains($mat->name, 'So'))
                                        🧂
                                    @elseif(str_contains($mat->name, 'znak'))
                                        🚧
                                    @elseif(str_contains($mat->name, 'Farba') || str_contains($mat->name, 'farb'))
                                        🎨
                                    @else
                                        📋
                                    @endif
                                    {{ $mat->name }}
                                </td>
                                <td class="px-4 py-3 text-right font-medium text-gray-900 dark:text-white">{{ number_format($mat->total_qty, 1) }}</td>
                                <td class="px-4 py-3 text-right text-gray-500">{{ $mat->unit }}</td>
                                <td class="px-4 py-3 text-right font-extrabold text-indigo-600 dark:text-indigo-400">{{ number_format($mat->total_cost, 2) }} RSD</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-6 text-center text-gray-400 italic">Nema evidentiranih materijala.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Tabela mehanizacije i amortizacije -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 flex flex-col">
            <div class="flex items-center gap-2 mb-1">
                <span class="text-xl">🚛</span>
                <h4 class="text-lg font-bold text-gray-800 dark:text-white">Praćenje mehanizacije i amortizacije</h4>
            </div>
            <p class="text-xs text-gray-500 mb-4">Sati rada kamiona, valjaka i bagera sa obračunatom amortizacijom.</p>
            
            <div class="overflow-x-auto flex-1">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-xs">
                    <thead class="bg-gray-50 dark:bg-gray-750">
                        <tr>
                            <th class="px-4 py-2.5 text-left text-gray-500 dark:text-gray-400 uppercase">Mašina / Rad</th>
                            <th class="px-4 py-2.5 text-right text-gray-500 dark:text-gray-400 uppercase">Sati rada</th>
                            <th class="px-4 py-2.5 text-right text-gray-500 dark:text-gray-400 uppercase">Trošak</th>
                            <th class="px-4 py-2.5 text-right text-gray-500 dark:text-gray-400 uppercase">Amortizacija</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($machineUsage as $mach)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-750">
                                <td class="px-4 py-3 font-semibold text-gray-800 dark:text-gray-300">
                                    @if(str_contains($mach->name, 'Kamion'))
                                        🚚
                                    @elseif(str_contains($mach->name, 'Bager'))
                                        🏗️
                                    @elseif(str_contains($mach->name, 'Valjak'))
                                        🔄
                                    @elseif(str_contains($mach->name, 'Putar'))
                                        👷
                                    @else
                                        ⚙️
                                    @endif
                                    {{ $mach->name }}
                                </td>
                                <td class="px-4 py-3 text-right font-medium text-gray-900 dark:text-white">{{ number_format($mach->total_qty, 1) }} {{ $mach->unit }}</td>
                                <td class="px-4 py-3 text-right font-medium text-emerald-600 dark:text-emerald-400">{{ number_format($mach->total_cost, 2) }} RSD</td>
                                <td class="px-4 py-3 text-right font-extrabold {{ $mach->total_depreciation > 0 ? 'text-amber-600 dark:text-amber-400' : 'text-gray-400' }}">
                                    {{ $mach->total_depreciation > 0 ? number_format($mach->total_depreciation, 2) . ' RSD' : '—' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-6 text-center text-gray-400 italic">Nema evidentiranih mašina.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Donji Red: Troškovi po kilometru i Heatmap -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Tabela troškova po kilometru deonice puta -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 flex flex-col">
            <h4 class="text-lg font-bold text-gray-800 dark:text-white mb-1">Obračun troškova po kilometru puta</h4>
            <p class="text-xs text-gray-500 mb-4">Pregled ukupnih investicija i troškova održavanja svedenih na dužinu deonica.</p>
            
            <div class="overflow-x-auto flex-1">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-xs">
                    <thead class="bg-gray-50 dark:bg-gray-750">
                        <tr>
                            <th class="px-4 py-2.5 text-left text-gray-500 dark:text-gray-450 uppercase">Deonica</th>
                            <th class="px-4 py-2.5 text-left text-gray-500 dark:text-gray-450 uppercase">Dužina</th>
                            <th class="px-4 py-2.5 text-right text-gray-500 dark:text-gray-450 uppercase">Ukupno</th>
                            <th class="px-4 py-2.5 text-right text-gray-500 dark:text-gray-450 uppercase">RSD / KM</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($roadCosts as $rc)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-750">
                                <td class="px-4 py-3 font-semibold text-gray-800 dark:text-gray-300">{{ $rc['name'] }}</td>
                                <td class="px-4 py-3 whitespace-nowrap text-gray-500">{{ $rc['length_km'] }} km</td>
                                <td class="px-4 py-3 whitespace-nowrap text-right font-medium text-gray-900 dark:text-white">
                                    {{ number_format($rc['total_cost'], 0) }} RSD
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-right font-extrabold text-indigo-600 dark:text-indigo-400">
                                    {{ number_format($rc['cost_per_km'], 0) }} RSD
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Heatmap Mapa sa Nezgodama i Oštećenjima -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 flex flex-col">
            <h4 class="text-lg font-bold text-gray-800 dark:text-white mb-1">Mapa toplotnih žarišta („Crne tačke")</h4>
            <p class="text-xs text-gray-500 mb-4">Vizuelizacija gustine oštećenja i lokacija teških saobraćajnih nezgoda na putnoj mreži.</p>
            
            <div wire:ignore class="flex-1 w-full rounded-xl relative z-0 border border-gray-200 dark:border-gray-700" style="height: 350px; min-height: 350px;">
                <div id="heatmap" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; border-radius: 0.75rem;"></div>
            </div>

            <div class="flex items-center gap-4 mt-3 text-[10px] text-gray-500">
                <span class="flex items-center gap-1"><span class="inline-block w-3 h-3 rounded-full" style="background: rgba(255,100,50,0.6);"></span> Oštećenja (prijave)</span>
                <span class="flex items-center gap-1"><span class="inline-block w-3 h-3 rounded-full" style="background: rgba(220,20,60,0.8);"></span> Saobraćajne nezgode</span>
            </div>
        </div>
    </div>

    @script
    <script>
        Alpine.data('analyticsDashboard', () => ({
            initDashboard() {
                let attempts = 0;
                const check = setInterval(() => {
                    attempts++;
                    if (typeof Chart !== 'undefined' && typeof L !== 'undefined') {
                        clearInterval(check);
                        this.renderCharts();
                    } else if (attempts >= 50) {
                        clearInterval(check);
                        console.error('Biblioteke Chart/Leaflet se nisu učitale.');
                    }
                }, 200);
            },
            renderCharts() {
                // ====== GRAFIKONI ======
                
                // Struktura troškova - Doughnut Chart
                const costCanvas = document.getElementById('costStructureChart');
                if (costCanvas) {
                    let existingChart = Chart.getChart('costStructureChart');
                    if (existingChart) existingChart.destroy();

                    const costCtx = costCanvas.getContext('2d');
                    const costKeys = @json($costByTypeKeys);
                    const costVals = @json($costByTypeValues);

                    if (costKeys.length > 0) {
                        new Chart(costCtx, {
                            type: 'doughnut',
                            data: {
                                labels: costKeys,
                                datasets: [{
                                    data: costVals,
                                    backgroundColor: ['#6366f1', '#10b981'],
                                    borderWidth: 2,
                                    borderColor: document.documentElement.classList.contains('dark') ? '#1f2937' : '#ffffff'
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: {
                                        position: 'bottom',
                                        labels: {
                                            color: document.documentElement.classList.contains('dark') ? '#e5e7eb' : '#374151',
                                            font: { size: 10 }
                                        }
                                    }
                                }
                            }
                        });
                    }
                }

                // Broj prijava po tipu - Bar Chart
                const issuesCanvas = document.getElementById('issuesByTypeChart');
                if (issuesCanvas) {
                    let existingChart = Chart.getChart('issuesByTypeChart');
                    if (existingChart) existingChart.destroy();

                    const issuesCtx = issuesCanvas.getContext('2d');
                    const issuesKeys = @json($issuesByTypeKeys);
                    const issuesVals = @json($issuesByTypeValues);

                    if (issuesKeys.length > 0) {
                        new Chart(issuesCtx, {
                            type: 'bar',
                            data: {
                                labels: issuesKeys,
                                datasets: [{
                                    label: 'Broj prijava',
                                    data: issuesVals,
                                    backgroundColor: '#3b82f6',
                                    borderRadius: 6
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                scales: {
                                    y: {
                                        beginAtZero: true,
                                        grid: { color: document.documentElement.classList.contains('dark') ? '#374151' : '#e5e7eb' },
                                        ticks: { color: document.documentElement.classList.contains('dark') ? '#9ca3af' : '#4b5563', stepSize: 1 }
                                    },
                                    x: {
                                        grid: { display: false },
                                        ticks: { color: document.documentElement.classList.contains('dark') ? '#9ca3af' : '#4b5563', font: { size: 9 } }
                                    }
                                },
                                plugins: { legend: { display: false } }
                            }
                        });
                    }
                }

                // ====== HEATMAP sa circle markerima ======
                const heatmapDiv = document.getElementById('heatmap');
                if (heatmapDiv) {
                    let container = L.DomUtil.get('heatmap');
                    if(container != null){
                        container._leaflet_id = null;
                    }

                    const map = L.map('heatmap', { zoomControl: true }).setView([44.7950, 20.4550], 13);
                    
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '&copy; OSM'
                    }).addTo(map);

                    // Tačke oštećenja (narandžaste)
                    const dmgPoints = @json($damagePoints);
                    dmgPoints.forEach(function(p) {
                        L.circleMarker([p[0], p[1]], {
                            radius: 18,
                            fillColor: '#ff6432',
                            color: '#ff4500',
                            weight: 1,
                            opacity: 0.7,
                            fillOpacity: 0.45
                        }).addTo(map).bindPopup('🛣️ Prijava oštećenja');
                    });

                    // Tačke nezgoda (crvene, veće)
                    const accPoints = @json($accidentPoints);
                    accPoints.forEach(function(p) {
                        L.circleMarker([p[0], p[1]], {
                            radius: 22,
                            fillColor: '#dc143c',
                            color: '#8b0000',
                            weight: 2,
                            opacity: 0.8,
                            fillOpacity: 0.5
                        }).addTo(map).bindPopup('🚨 Saobraćajna nezgoda');
                    });

                    // Ako postoje tačke, podesi bounds
                    const allPoints = dmgPoints.concat(accPoints);
                    if (allPoints.length > 0) {
                        const bounds = L.latLngBounds(allPoints.map(p => [p[0], p[1]]));
                        map.fitBounds(bounds, { padding: [40, 40] });
                    }

                    // Leaflet invalidateSize za siguran prikaz
                    setTimeout(() => map.invalidateSize(), 500);
                }
            }
        }));
    </script>
    @endscript
</div>
