<div class="w-full">
    <div class="p-6 bg-white rounded-lg border border-gray-200 shadow-md dark:bg-gray-800 dark:border-gray-700">
        <x-loading-indicator wire:loading.delay wire:target="period_report" />
        <x-loading-indicator wire:loading.delay wire:target="start_custom_date" />
        <x-loading-indicator wire:loading.delay wire:target="end_custom_date" />
        @if (count($customers_its) > 0)
            <h5 class="mb-2 text-xl font-bold tracking-tight text-gray-900 dark:text-white">
                <i class="fas fa-file-download mr-2"></i> Exportar Todo
            </h5>
            <div class="grid grid-cols-2 gap-6 mb-6">
                <div class="w-full">
                    <label for="countries" class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-400"><i
                            class="fa-solid fa-filter mr-2"></i>Selecciona el periodo</label>
                    <select wire:loading.attr="disabled" id="periods" wire:model="period_report"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        <option value="">Selecciona una opción</option>
                        <option value="7">Mes Anterior</option>
                        <option value="13">Actual</option>
                        <option value="25">Últimos 3 meses</option>
                        <option value="50">Periodo Customizado</option>
                    </select>
                </div>
                <div class="w-full" style="display: {{ $showStartEndDate ? 'block' : 'none' }}">
                    <div class="grid grid-cols-2 gap-6">
                        <div class="w-full">
                            <label for="msp_init" class="block text-sm font-medium text-gray-900 dark:text-gray-400"><i
                                    class="fa-solid fa-filter mr-2"></i>Fecha de Inicio</label>
                            <input wire:loading.attr="disabled"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                type="date" id="start_custom_date" wire:model="start_custom_date"
                                max='{{ $end_custom_date ?? now()->format('Y-m-d') }}'>
                        </div>
                        <div class="w-full">
                            <label for="msp_init" class="block text-sm font-medium text-gray-900 dark:text-gray-400"><i
                                    class="fa-solid fa-filter mr-2"></i>Fecha de Fin</label>
                            <input wire:loading.attr="disabled"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                type="date" id="end_custom_date" wire:model="end_custom_date"
                                max='{{ now()->format('Y-m-d') }}'>
                        </div>
                    </div>
                </div>
            </div>
            <div class="grid">
                <label for="msp_init" class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-400"><i
                        class="fa-solid fa-filter mr-2"></i>Selecciona un MSP para iniciar (Opcional)</label>
                <Select id="msp_init" wire:loading.attr="disabled"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    wire:model.defer="msp_init">
                    <option value="undefinedMSP" disabled selected>-- Selecciona un MSP --</option>
                    @foreach ($customers_its as $customer_it)
                        <option value="{{ $customer_it['zaaid'] }}">{{ $customer_it['name'] }}</option>
                    @endforeach
                </Select>
            </div>
            <div style="text-align: end">
                <strong>Total de {{ count($customers_its) }} MSP</strong>

            </div>
            <div style="text-align: end">
                <span><i class="fas fa-calendar-day mr-2"></i>Periodo Seleccionado: {{ $last_month }}</span>
            </div>
            <div class="flex justify-between mb-1" style="align-items: center;">
                <span class="text-base font-medium text-blue-700 dark:text-white">
                    <i class="fa-solid fa-play text-green-500" wire:loading.remove
                        wire:click.prevent="startProcess()"></i>
                    {{-- <i class="fas fa-stop text-danger" wire:loading wire:target="startProcess"
                        wire:click="stopProcess"></i> --}}
                    <i class="fa-solid fa-spinner fa-spin-pulse" wire:loading wire:target="startProcess"></i>
                </span>
                <div class="w-full text-right">
                    <small id="progressTextType"></small>
                    <small id="progressText" class="text-xs font-medium text-blue-700 dark:text-white"> 0% (0 de 0
                        monitores)</small>
                    <div class="ml-2 w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
                        <div id="percentageBar" class="bg-blue-600 h-2.5 rounded-full" style="width: 0%"></div>
                    </div>
                </div>
            </div>
            <div class="w-full" x-data="{ open: true }">
                <div class="w-full" style="text-align: end">
                    <span x-text="!open?'Maximizar':'Minimizar'"></span>
                    <i x-bind:class="!open ? 'fas fa-plus-circle' : 'fas fa-minus-circle'"
                        x-on:click="open = ! open"></i>
                </div>
                <ul id="customersList" x-show="open" style="height: calc(100vh - 390px);overflow: auto;"
                    class="w-full text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    @foreach ($customers_its as $customer)
                        <li id="{{ $customer['zaaid'] }}_customer"
                            class="w-full px-4 py-2 border-b border-gray-200 rounded-t-lg dark:border-gray-600">
                            {{ $customer['name'] }}
                        </li>
                    @endforeach
                </ul>

            </div>
        @else
            @include('partials.error-api', [
                'titleErrorAPI' => 'Exportar Por MSP',
                'errorAPI' =>
                    'No se pudo consultar la información del API, espere un momento y refresque la pagina',
            ])
        @endif
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Livewire.on('current-percentage', (data) => {
                // console.log(data);
                let {
                    progress,
                    completed_reports,
                    total_monitors,
                    zaaid,
                    customer,
                    textType
                } = data;
                if (zaaid != '0000') {
                    let li = document.getElementById(`${zaaid}_customer`);
                    li.innerHTML =
                        `<span class="text-sm font-medium text-blue-700 dark:text-white">
                        <span>${textType}</span>
                        ${customer} - ${progress}% (${completed_reports} de ${total_monitors} monitores)    
                        </span>`
                }
            });
            Livewire.on('current-percentage-customers', (data) => {
                //console.log(data);
                let {
                    total_customers,
                    progress,
                    completed_customers,
                    customer_id,
                    textType
                } = data;
                document.getElementById('progressTextType').innerHTML = `<small>${textType}</small>`;
                document.getElementById(`percentageBar`).style.width = progress + '%';
                document.getElementById(`progressText`).innerHTML = `
                    ${progress}% (${completed_customers} de ${total_customers} clientes)`;
            });
            Livewire.on('current-all-percentage-donwload', (data) => {
                //console.log(data);
                let {
                    total_customers,
                    progress,
                    completed_customers,
                    customer_id,
                    textType
                } = data;
                //console.log(completed_customers, 'de', total_customers);
                document.getElementById('progressTextType').innerHTML = `<small>${textType}</small>`;
                document.getElementById(`percentageBar`).style.width = progress + '%';
                document.getElementById(`progressText`).innerHTML = `
                    ${progress}% (${completed_customers} de ${total_customers} clientes)`;
            });

            Livewire.on('current-percentage-donwload', (data) => {
                //console.table(data);
                let {
                    progress,
                    completed_reports,
                    total_monitors,
                    zaaid,
                    customer,
                    textType,
                    state_stored
                } = data;
                if (zaaid != '0000') {
                    let li = document.getElementById(`${zaaid}_customer`);
                    li.innerHTML =
                        `<span class="text-sm font-medium text-blue-700 dark:text-white">
                        <span>${textType}</span>
                        ${customer} - ${progress}% (${completed_reports} de ${total_monitors} monitores)    
                    </span>`
                }
            });
        });
    </script>
</div>
