<div class="mt-8">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-xl font-bold text-gray-800 dark:text-white">Moji zadaci na terenu</h3>
    </div>

    @if (session()->has('worker_message'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
            {{ session('worker_message') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($workOrders as $order)
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md border-t-4 {{ $order->priority == 'critical' ? 'border-red-500' : ($order->priority == 'high' ? 'border-orange-500' : 'border-blue-500') }} p-5">
                <div class="flex justify-between items-start mb-2">
                    <span class="text-sm font-semibold text-gray-500 dark:text-gray-400">Nalog #{{ $order->id }}</span>
                    <span class="px-2 py-1 text-xs font-bold rounded {{ $order->status == 'pending' ? 'bg-gray-200 text-gray-800' : 'bg-blue-100 text-blue-800' }}">
                        {{ $order->status == 'pending' ? 'Čeka na vas' : 'U toku' }}
                    </span>
                </div>
                
                <h4 class="text-lg font-bold text-gray-900 dark:text-white mb-1">
                    {{ $order->issueReport ? $order->issueReport->type : 'Nepoznat tip' }}
                </h4>
                <p class="text-sm text-gray-600 dark:text-gray-300 mb-4 line-clamp-2">
                    {{ $order->description ?? ($order->issueReport ? $order->issueReport->description : '') }}
                </p>

                @if($order->issueReport)
                    <div class="text-xs text-gray-500 dark:text-gray-400 mb-4">
                        📍 Lokacija: {{ number_format($order->issueReport->gps_lat, 4) }}, {{ number_format($order->issueReport->gps_lng, 4) }}
                    </div>
                @endif

                <div class="flex gap-2 mt-auto">
                    @if($order->status == 'pending')
                        <button wire:click="startWork({{ $order->id }})" class="w-full py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">
                            Započni rad
                        </button>
                    @elseif($order->status == 'in_progress')
                        <button wire:click="openCompletionModal({{ $order->id }})" class="w-full py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition">
                            Završi posao
                        </button>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-full p-6 bg-white dark:bg-gray-800 rounded-lg shadow text-center text-gray-500 dark:text-gray-400">
                Trenutno nemate dodeljenih radnih naloga. Odmarajte! ☕
            </div>
        @endforelse
    </div>

    <!-- Istorija završenih poslova -->
    <div class="mt-12 mb-4 flex justify-between items-center">
        <h3 class="text-xl font-bold text-gray-800 dark:text-white">Istorija završenih poslova</h3>
    </div>

    <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700 mb-8">
        <ul class="divide-y divide-gray-200 dark:divide-gray-700">
            @forelse($completedOrders as $order)
                <li class="p-4 hover:bg-gray-50 dark:hover:bg-gray-750 transition duration-150 ease-in-out">
                    <div class="flex items-center justify-between">
                        <div class="flex flex-col">
                            <span class="text-sm font-medium text-indigo-600 dark:text-indigo-400">
                                Nalog #{{ $order->id }} - {{ $order->issueReport ? $order->issueReport->type : 'Nepoznat problem' }}
                            </span>
                            <span class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                {{ $order->description ?? ($order->issueReport ? $order->issueReport->description : '') }}
                            </span>
                            @if($order->resources->isNotEmpty())
                                <div class="mt-2 flex flex-wrap gap-1">
                                    @foreach($order->resources as $res)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-750 dark:text-gray-300">
                                            {{ $res->name }}: {{ number_format($res->pivot->quantity, 1) }} {{ $res->unit }}
                                        </span>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                        <div class="flex flex-col items-end">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                Završeno
                            </span>
                            <span class="text-xs text-gray-500 mt-1">
                                {{ $order->completed_at ? $order->completed_at->format('d.m.Y H:i') : '' }}
                            </span>
                        </div>
                    </div>
                </li>
            @empty
                <li class="p-4 text-center text-gray-500 dark:text-gray-400">
                    Još uvek niste završili nijedan zadatak.
                </li>
            @endforelse
        </ul>
    </div>

    <!-- Modal za unos resursa i završetak rada -->
    @if($isCompletionModalOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-md p-6 relative">
                <button wire:click="$set('isCompletionModalOpen', false)" class="absolute top-4 right-4 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
                
                <h2 class="text-2xl font-bold mb-2 text-gray-800 dark:text-white">Završetak Naloga #{{ $selectedWorkOrderId }}</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Molimo unesite resurse i materijal utrošen na sanaciji ovog problema pre zatvaranja naloga.</p>
                
                <!-- Forma za dodavanje pojedinačnog resursa -->
                <div class="bg-gray-50 dark:bg-gray-750 p-3 rounded-lg mb-4 border border-gray-200 dark:border-gray-700">
                    <div class="grid grid-cols-1 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase">Resurs / Materijal</label>
                            <select wire:model="selectedResourceId" class="mt-1 block w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                <option value="">Izaberite resurs...</option>
                                @foreach($availableResources as $res)
                                    <option value="{{ $res->id }}">{{ $res->name }} (u {{ $res->unit }}) - {{ number_format($res->cost_per_unit, 2) }} KM/RSD</option>
                                @endforeach
                            </select>
                            @error('selectedResourceId') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        
                        <div class="flex gap-2 items-end">
                            <div class="flex-1">
                                <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase">Količina</label>
                                <input type="number" step="0.01" wire:model="quantity" class="mt-1 block w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="0.00">
                                @error('quantity') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            
                            <button type="button" wire:click="addResource" class="px-4 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-md hover:bg-indigo-700 transition">
                                Dodaj
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Lista evidentiranih resursa -->
                <div class="mb-6">
                    <h4 class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase mb-2">Utrošeno na terenu:</h4>
                    @if(empty($loggedResources))
                        <p class="text-sm text-gray-500 italic">Nije dodat nijedan resurs. (Dodajte radne sate ili materijale)</p>
                    @else
                        <div class="max-h-40 overflow-y-auto divide-y divide-gray-200 dark:divide-gray-700 border border-gray-200 dark:border-gray-700 rounded-md">
                            @foreach($loggedResources as $index => $item)
                                <div class="flex justify-between items-center p-2 text-sm bg-white dark:bg-gray-800">
                                    <span class="dark:text-gray-200">{{ $item['name'] }}</span>
                                    <div class="flex items-center gap-3">
                                        <span class="font-bold text-gray-700 dark:text-gray-300">{{ $item['quantity'] }} {{ $item['unit'] }}</span>
                                        <button type="button" wire:click="removeResource({{ $index }})" class="text-red-500 hover:text-red-700">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="flex justify-end gap-2">
                    <button type="button" wire:click="$set('isCompletionModalOpen', false)" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600 text-sm">Odustani</button>
                    <button type="button" wire:click="completeWorkWithResources" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 text-sm font-semibold">Završi Posao</button>
                </div>
            </div>
        </div>
    @endif
</div>
