<div class="w-full" style="background: white;padding: 10px;border-radius: 8px;">
    <x-loading-indicator wire:loading.delay wire:target="period" />
    <x-loading-indicator wire:loading.delay wire:target="start_custom_date" />
    <x-loading-indicator wire:loading.delay wire:target="end_custom_date" />
    <div class="grid grid-cols-2 gap-6 mb-6">
        <div class="w-full">
            <label for="countries" class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-400"><i
                    class="fa-solid fa-filter mr-2"></i>Selecciona el periodo</label>
            <select wire:loading.attr="disabled" wire:target="startProcess" id="periods" wire:model="period"
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
    {{-- <div class="grid grid-cols-4 gap-4 mb-6">
        <div class="w-full">
            <h5 class="mb-2 text-xl font-bold tracking-tight text-gray-900 dark:text-white">
                <i class="fas fa-home mr-2"></i> Exportar Por MSP
            </h5>
            <label for="countries" class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-400"><i
                    class="fa-solid fa-filter mr-2"></i>Selecciona el periodo</label>
            <select id="periods" wire:model="period"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                <option value="">Selecciona una opción</option>
                <option value="7">Mes Anterior</option>
                <option value="13">Actual</option>
                <option value="25">Últimos 3 meses</option>
            </select>
        </div>
    </div> --}}
    <div style="text-align: end">
        <strong>{{ count($customers_its) }} MSP</strong>
    </div>
    <div style="height: calc(100vh - 270px);overflow: auto;" class="">

        <div class="grid grid-cols-3 gap-3">
            @foreach ($customers_its as $customer)
                @livewire('customer-export-p-d-f', ['customer' => $customer, 'period' => $period, 'last_month' => $last_month, 'start_custom_date' => $start_custom_date, 'end_custom_date' => $end_custom_date], key($customer['zaaid']))
            @endforeach
        </div>
        
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Livewire.on('current-percentage', (data) => {
                let {
                    progress,
                    completed_reports,
                    total_monitors,
                    zaaid,
                    customer,
                    textType
                } = data;
                //console.table(data);
                if (zaaid != '0000') {
                    document.getElementById(`progressTextType${zaaid}`).innerHTML = textType;
                    document.getElementById(`percentageBar${zaaid}`).style.width = progress + '%';
                    document.getElementById(`progressText${zaaid}`).innerHTML = `
                    ${progress}% (${completed_reports} de ${total_monitors} monitores)
                `;
                }
            });
            Livewire.on('current-percentage-donwload', (data) => {
                let {
                    progress,
                    completed_reports,
                    total_monitors,
                    zaaid,
                    customer,
                    textType
                } = data;
                //console.table(data);
                if (zaaid != '0000') {
                    document.getElementById(`progressTextType${zaaid}`).innerHTML = textType;
                    document.getElementById(`percentageBar${zaaid}`).style.width = progress + '%';
                    document.getElementById(`progressText${zaaid}`).innerHTML = `
                    ${progress}% (${completed_reports} de ${total_monitors} monitores)
                `;
                }
            });
        });
    </script>
</div>
