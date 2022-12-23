<div class="w-full">
    <x-loading-indicator-minified />
    <div class="p-6 bg-white rounded-lg border border-gray-200 shadow-md dark:bg-gray-800 dark:border-gray-700">
        <h5 class="mb-2 text-xl font-bold tracking-tight text-gray-900 dark:text-white">
            <i class="fas fa-grip-vertical"></i> Ordenar MSPs
        </h5>
        @if ($mspsSortByOrder->count())
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3">
                <div class="text-center">
                    <div class="text-start">
                        <h6>Cambiar MSP de Posición</h6>
                    </div>
                    <div class="inline-block relative w-64 text-center">
                        <select id="fromMsp" wire:model="from"
                            class="select2 block appearance-none w-full bg-white border border-gray-400 hover:border-gray-500 px-4 py-2 pr-8 rounded shadow leading-tight focus:outline-none focus:shadow-outline">
                            <option value="" disabled>-- Selecciona un MSP --</option>
                            @foreach ($mspsSortByName as $msp)
                                @if ($to != '')
                                    @if (explode('|', $to)[1] != $msp->name)
                                        <option value="{{ $msp->order }}|{{ $msp->name }}">{{ $msp->name }}
                                        </option>
                                    @endif
                                @else
                                    <option value="{{ $msp->order }}|{{ $msp->name }}">{{ $msp->name }}</option>
                                @endif
                            @endforeach
                        </select>
                        <div
                            class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                            <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z" />
                            </svg>
                        </div>
                    </div>
                    <div class="text-center">
                        <i class="fas fa-arrows-alt-v"></i>
                    </div>
                    <div class="inline-block relative w-64 text-center">
                        <select id="toMsp" wire:model="to"
                            class="select2 block appearance-none w-full bg-white border border-gray-400 hover:border-gray-500 px-4 py-2 pr-8 rounded shadow leading-tight focus:outline-none focus:shadow-outline">
                            <option value="" disabled>-- Selecciona un MSP --</option>
                            @foreach ($mspsSortByName as $msp)
                                @if ($from != '')
                                    @if (explode('|', $from)[1] != $msp->name)
                                        <option value="{{ $msp->order }}|{{ $msp->name }}">{{ $msp->name }}
                                        </option>
                                    @endif
                                @else
                                    <option value="{{ $msp->order }}|{{ $msp->name }}">{{ $msp->name }}
                                    </option>
                                @endif
                            @endforeach
                        </select>
                        <div
                            class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                            <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z" />
                            </svg>
                        </div>
                    </div>
                    <div class="w-full mt-2">
                        <button wire:click.prevent="changeMsps"
                            class="bg-yellow-500 hover:bg-black text-white font-bold py-1 px-2 rounded">
                            <i class="fas fa-sync"></i> Cambiar Posición
                        </button>
                    </div>
                </div>
                <div class="w-full col-span-2">
                    <div>
                        <h6>Arrastra el MSP al lugar que desees</h6>
                    </div>
                    <div class="flex justify-center w-full col-span-2" style="max-height: 450px;">
                        <ul wire:sortable="updateMspOrder" role="list"
                            class="divide-y divide-gray-200 dark:divide-gray-700 w-full"
                            style="max-height: 450px;overflow: auto;">
                            @foreach ($mspsSortByOrder as $msp)
                                <li class="py-3 sm:py-4 cursor-move" wire:sortable.item="{{ $msp->name }}"
                                    wire:key="msp-{{ $msp->order }}" wire:sortable.handle>
                                    <div class="flex items-center space-x-4">
                                        <div class="flex-shrink-0">
                                            @if ($msp->order == 1)
                                                <img class="w-8 h-8 rounded-full"
                                                    src="{{ asset('assets/medals/gold-medal.png') }}" alt="medal">
                                            @elseif ($msp->order == 2)
                                                <img class="w-8 h-8 rounded-full"
                                                    src="{{ asset('assets/medals/second-prize.png') }}" alt="medal">
                                            @elseif ($msp->order == 3)
                                                <img class="w-8 h-8 rounded-full"
                                                    src="{{ asset('assets/medals/third-prize.png') }}" alt="medal">
                                            @else
                                                <img class="w-8 h-8 rounded-full"
                                                    src="{{ asset('assets/medals/medal.png') }}" alt="medal">
                                            @endif
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="m-0 text-sm font-medium text-gray-900 truncate dark:text-white">
                                                <strong>
                                                    {{ $msp->order }}.- {{ $msp->name }}
                                                </strong>
                                            </p>
                                        </div>
                                        <div
                                            class="inline-flex items-center text-base font-semibold text-gray-900 dark:text-white">
                                            <i class="fas fa-grip-vertical"></i>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @else
            <x-loading-indicator wire:loading />
            <div class="flex justify-center">
                <div>
                    <img class="object-cover h-full w-96" src="{{ asset('assets/img/undraw/no-data.svg') }}"
                        alt="No Data">
                </div>
            </div>
            <div class="text-center">
                <h5>No se encontraron MSPs</h5>
                <span wire:click="syncMsps" class="cursor-pointer"><i class="fas fa-sync"></i> Obtener</span>
            </div>
        @endif
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            $('.select2').select2();
            $('#fromMsp').on('select2:select select2:unselect', function(e) {
                var data = e.params.data;

                @this.set('from', data.id, true);
            });
            $('#toMsp').on('select2:select select2:unselect', function(e) {
                var data = e.params.data;
                @this.set('to', data.id, true);
            });

            Livewire.on('select2', () => {
                $('.select2').select2();
            })
        });
    </script>
</div>
