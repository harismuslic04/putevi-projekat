<div class="mt-8 space-y-8">
    <!-- Gornji red sa naslovom i brzim akcijama -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white dark:bg-gray-800 p-6 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-md">
        <div>
            <h3 class="text-2xl font-extrabold text-gray-900 dark:text-white">Operativni Panel Dispečera</h3>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Upravljanje prijavama građana, verifikacija stanja i raspodela redovnog i vanrednog održavanja.</p>
        </div>
        <button wire:click="openRegularModal" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold rounded-xl shadow-lg hover:shadow-indigo-500/20 transition-all duration-200 flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
            Novi redovni nalog
        </button>
    </div>

    @if (session()->has('message'))
        <div class="bg-green-50 dark:bg-green-950/20 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 px-6 py-4 rounded-xl shadow-sm flex items-center gap-3 animate-pulse">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <span class="text-sm font-medium">{{ session('message') }}</span>
        </div>
    @endif

    <!-- Tabela 1: Nove Prijave građana -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h4 class="text-lg font-bold text-gray-800 dark:text-white">Prijave građana na čekanju</h4>
            <p class="text-xs text-gray-500">Nove građanske prijave koje treba verifikovati i dodeliti putarskim službama.</p>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-750">
                    <tr>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">ID</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tip Kvara</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Opis i Lokacija</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider text-right">Akcije</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($issues as $issue)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-750 transition duration-150">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900 dark:text-white">#{{ $issue->id }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2.5 py-1 bg-rose-50 dark:bg-rose-950/20 text-rose-600 dark:text-rose-400 text-xs font-bold rounded-lg border border-rose-100 dark:border-rose-900/30">
                                    {{ $issue->type }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">
                                <div class="font-medium max-w-xs truncate">{{ $issue->description }}</div>
                                <div class="text-xs text-gray-450 mt-1 flex flex-wrap gap-1.5 items-center">
                                    <span>📍 {{ number_format($issue->gps_lat, 4) }}, {{ number_format($issue->gps_lng, 4) }}</span>
                                    @if($issue->roadSegment)
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 font-semibold">
                                            🛣️ {{ $issue->roadSegment->name }} (${{ $issue->roadSegment->category }})
                                        </span>
                                    @endif
                                    @if($issue->duplicates->isNotEmpty())
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] bg-amber-50 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 font-bold border border-amber-200">
                                            👥 Duplirano: {{ $issue->duplicates->count() }}x
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                @if($issue->status === 'prijavljeno')
                                    <span class="px-2.5 py-1 bg-red-100 dark:bg-red-900/40 text-red-800 dark:text-red-400 text-xs font-bold rounded-full">
                                        Novi (Prijavljeno)
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 bg-orange-100 dark:bg-orange-900/40 text-orange-850 dark:text-orange-400 text-xs font-bold rounded-full">
                                        Verifikovano
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-right space-x-2">
                                @if($issue->status === 'prijavljeno')
                                    <button wire:click="verifyIssue({{ $issue->id }})" class="px-3 py-1.5 bg-emerald-500 hover:bg-emerald-600 text-white rounded-lg shadow-sm transition">
                                        Verifikuj
                                    </button>
                                @endif
                                <button wire:click="openAssignModal({{ $issue->id }})" class="px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg shadow-sm transition">
                                    Dodeliti nalog
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400 italic">Nema novih prijava na čekanju. Sve je pod kontrolom! ✨</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Tabela 2: Aktivni radni nalozi na terenu -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h4 class="text-lg font-bold text-gray-800 dark:text-white">Aktivni radni nalozi na terenu</h4>
            <p class="text-xs text-gray-500">Radni nalozi koji su trenutno u fazi izvršenja ili čekaju na početak rada putara.</p>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-750">
                    <tr>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Nalog</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Radnik</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Zadatak & Deonica</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tip</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Prioritet</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3.5 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Akcije</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($activeWorkOrders as $order)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-750 transition duration-150">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900 dark:text-white">#{{ $order->id }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-gray-300 font-medium">
                                {{ $order->assignedUser ? $order->assignedUser->name : 'Nije dodeljen' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-750 dark:text-gray-300">
                                <div class="font-bold">
                                    {{ $order->issueReport ? $order->issueReport->type : $order->description }}
                                </div>
                                <div class="text-xs text-gray-450 mt-0.5">
                                    @if($order->roadSegment)
                                        <span>🛣️ Put: {{ $order->roadSegment->name }}</span>
                                    @elseif($order->issueReport && $order->issueReport->roadSegment)
                                        <span>🛣️ Put: {{ $order->issueReport->roadSegment->name }}</span>
                                    @else
                                        <span class="italic">Bez definisanog puta</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $order->maintenance_type === 'redovno' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/20 dark:text-emerald-400' : 'bg-rose-100 text-rose-800 dark:bg-rose-950/20 dark:text-rose-400' }}">
                                    {{ $order->maintenance_type }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $order->priority === 'critical' ? 'bg-red-500 text-white' : ($order->priority === 'high' ? 'bg-orange-500 text-white' : ($order->priority === 'normal' ? 'bg-blue-500 text-white' : 'bg-gray-500 text-white')) }}">
                                    {{ $order->priority }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <span class="px-2 py-0.5 rounded-full text-xs font-bold {{ $order->status === 'pending' ? 'bg-yellow-50 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400' : 'bg-blue-50 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400' }}">
                                    {{ $order->status === 'pending' ? 'Čeka na radnika' : 'U toku' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-right">
                                <button wire:click="cancelWorkOrder({{ $order->id }})" class="text-red-500 hover:text-red-700 transition" onclick="confirm('Da li ste sigurni da želite da otkažete ovaj radni nalog?') || event.stopImmediatePropagation()">
                                    Otkaži
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400 italic">Nema aktivnih radnih naloga na terenu.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Tabela 3: Istorija završenih naloga i troškovi -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h4 class="text-lg font-bold text-gray-800 dark:text-white">Istorija završenih radnih naloga</h4>
                <p class="text-xs text-gray-500">Pregled realizovanih naloga, utrošenog materijala i konačnih troškova po nalogu.</p>
            </div>
            
            <div>
                <select wire:model.live="filterTime" class="rounded-xl border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white text-xs py-1.5 px-3">
                    <option value="all">Svi završeniji nalozi</option>
                    <option value="week">Ove nedelje</option>
                    <option value="month">Ovog meseca</option>
                    <option value="year">Ove godine</option>
                </select>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-750">
                    <tr>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Nalog ID</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Zadatak</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Terenski Radnik</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Utrošak resursa i mašina</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Cena realizacije</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Završeno</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($completedWorkOrders as $order)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-750 transition duration-150">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900 dark:text-white">#{{ $order->id }}</td>
                            <td class="px-6 py-4 text-sm text-gray-855 dark:text-gray-300">
                                <div class="font-bold">{{ $order->issueReport ? $order->issueReport->type : $order->description }}</div>
                                <div class="text-[10px] text-gray-450 uppercase font-semibold mt-0.5">{{ $order->maintenance_type }} održavanje</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-gray-300">
                                {{ $order->assignedUser ? $order->assignedUser->name : 'N/A' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                <div class="flex flex-wrap gap-1 max-w-sm">
                                    @forelse($order->resources as $res)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300">
                                            {{ $res->name }}: {{ number_format($res->pivot->quantity, 1) }} {{ $res->unit }}
                                        </span>
                                    @empty
                                        <span class="text-xs text-gray-400 italic">Bez utrošenog materijala</span>
                                    @endforelse
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900 dark:text-white">
                                {{ number_format($order->resources->sum('pivot.cost'), 2) }} RSD
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $order->completed_at ? $order->completed_at->format('d.m.Y H:i') : '' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400 italic">Nema završenih naloga u ovom periodu.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- MODAL 1: Dodeljivanje Radnog Naloga za prijavu građana -->
    @if($isOpen)
        <div class="fixed inset-0 z-[2000] flex items-center justify-center bg-black bg-opacity-60 backdrop-blur-sm">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-md p-6 relative border border-gray-200 dark:border-gray-700">
                <button wire:click="$set('isOpen', false)" class="absolute top-4 right-4 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
                
                <h2 class="text-xl font-extrabold mb-1 text-gray-900 dark:text-white">Dodeljivanje Radnog Naloga</h2>
                <p class="text-xs text-gray-400 mb-6">Povezano sa prijavom građana #{{ $selectedIssueId }}</p>
                
                <form wire:submit.prevent="assignWorkOrder" class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase">Terenski radnik / Ekipa</label>
                        <select wire:model="assigned_worker_id" class="mt-1 block w-full rounded-xl border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white text-sm">
                            <option value="">Izaberite radnika...</option>
                            @foreach($workers as $worker)
                                <option value="{{ $worker->id }}">{{ $worker->name }} ({{ $worker->email }})</option>
                            @endforeach
                        </select>
                        @error('assigned_worker_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase">Prioritet naloga</label>
                        <select wire:model="priority" class="mt-1 block w-full rounded-xl border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white text-sm">
                            <option value="low">Nizak prioritet</option>
                            <option value="normal">Normalan prioritet</option>
                            <option value="high">Visok prioritet</option>
                            <option value="critical">Kritičan prioritet (Autoput/Opasnost)</option>
                        </select>
                        @error('priority') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase">Opis i detaljna uputstva</label>
                        <textarea wire:model="description" rows="3" class="mt-1 block w-full rounded-xl border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white text-sm" placeholder="Upišite instrukcije za ekipu na terenu (materijali, lokacija)..."></textarea>
                        @error('description') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button" wire:click="$set('isOpen', false)" class="px-4 py-2 bg-gray-150 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-650 rounded-xl text-sm font-semibold">Odustani</button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-sm font-bold shadow-md shadow-indigo-600/10">Kreiraj Nalog</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- MODAL 2: Kreiranje Naloga za Redovno Održavanje -->
    @if($isRegularOpen)
        <div class="fixed inset-0 z-[2000] flex items-center justify-center bg-black bg-opacity-60 backdrop-blur-sm">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-md p-6 relative border border-gray-200 dark:border-gray-700">
                <button wire:click="$set('isRegularOpen', false)" class="absolute top-4 right-4 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
                
                <h2 class="text-xl font-extrabold mb-1 text-gray-900 dark:text-white">Novi Nalog za Redovno Održavanje</h2>
                <p class="text-xs text-gray-400 mb-6">Plansko kreiranje radnih zadataka direktno vezanih za deonicu puta.</p>
                
                <form wire:submit.prevent="createRegularWorkOrder" class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase">Putna deonica</label>
                        <select wire:model="regular_road_id" class="mt-1 block w-full rounded-xl border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white text-sm">
                            <option value="">Izaberite deonicu...</option>
                            @foreach($roads as $road)
                                <option value="{{ $road->id }}">{{ $road->name }} ({{ $road->category }})</option>
                            @endforeach
                        </select>
                        @error('regular_road_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase">Tip planskog zadatka</label>
                        <select wire:model="regular_task_name" class="mt-1 block w-full rounded-xl border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white text-sm">
                            <option value="Čišćenje snega i posipanje soli">Čišćenje snega i posipanje soli</option>
                            <option value="Iscrtavanje horizontalnih linija">Iscrtavanje horizontalnih linija</option>
                            <option value="Košenje trave i uređenje bankina">Košenje trave i uređenje bankina</option>
                            <option value="Zamena sijalica i servis semafora">Zamena sijalica i servis semafora</option>
                            <option value="Postavljanje novih saobraćajnih znakova">Postavljanje novih saobraćajnih znakova</option>
                            <option value="Redovan pregled deonice">Redovan pregled deonice</option>
                        </select>
                        @error('regular_task_name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase">Terenski radnik / Ekipa</label>
                        <select wire:model="regular_worker_id" class="mt-1 block w-full rounded-xl border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white text-sm">
                            <option value="">Izaberite radnika...</option>
                            @foreach($workers as $worker)
                                <option value="{{ $worker->id }}">{{ $worker->name }} ({{ $worker->email }})</option>
                            @endforeach
                        </select>
                        @error('regular_worker_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase">Prioritet rada</label>
                        <select wire:model="regular_priority" class="mt-1 block w-full rounded-xl border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white text-sm">
                            <option value="low">Nizak</option>
                            <option value="normal">Normalan</option>
                            <option value="high">Visok</option>
                            <option value="critical">Kritičan</option>
                        </select>
                        @error('regular_priority') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase">Dodatni opis</label>
                        <textarea wire:model="regular_description" rows="2" class="mt-1 block w-full rounded-xl border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white text-sm" placeholder="Opciono..."></textarea>
                        @error('regular_description') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button" wire:click="$set('isRegularOpen', false)" class="px-4 py-2 bg-gray-150 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-655 rounded-xl text-sm font-semibold">Odustani</button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-sm font-bold shadow-md shadow-indigo-600/10">Kreiraj Nalog</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
