<div class="w-full">
    <div class="p-6 bg-white rounded-lg border border-gray-200 shadow-md dark:bg-gray-800 dark:border-gray-700">
        @if (count($customers_its) > 0)
            <h5 class="mb-2 text-xl font-bold tracking-tight text-gray-900 dark:text-white">
                Exportar Todo
            </h5>
            <div class="flex justify-between mb-1" style="align-items: center;">
                <span class="text-base font-medium text-blue-700 dark:text-white">
                    <i class="fa-solid fa-play text-green-500" wire:loading.remove
                        wire:click.prevent="startProcess()"></i>
                    <i class="fa-solid fa-spinner fa-spin-pulse" wire:loading wire:target="startProcess"></i>
                </span>
                <div class="w-full text-right">
                    <small id="progressText" class="text-xs font-medium text-blue-700 dark:text-white">0% (0 de 0
                        monitores)</small>
                    <div class="ml-2 w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
                        <div id="percentageBar" class="bg-blue-600 h-2.5 rounded-full" style="width: 0%"></div>
                    </div>
                </div>
            </div>
            <div class="w-full" x-data="{ open: true }">
                <i x-bind:class="!open ? 'fas fa-plus-circle' : 'fas fa-minus-circle'" x-on:click="open = ! open"></i>
                <ul id="customersList" x-show="open" style="height: calc(100vh - 240px);overflow: auto;"
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
                console.log(data);
                let {
                    progress,
                    completed_reports,
                    total_monitors,
                    zaaid,
                    customer
                } = data;
                let li = document.getElementById(`${zaaid}_customer`);
                li.innerHTML =
                    `<span class="text-sm font-medium text-blue-700 dark:text-white">
                        ${customer} - ${progress}% (${completed_reports} de ${total_monitors} monitores)    
                    </span>`
            });
            Livewire.on('current-percentage-customers', (data) => {
                //console.log(data);
                let {
                    total_customers,
                    progress,
                    completed_customers,
                    customer_id
                } = data;
                document.getElementById(`percentageBar`).style.width = progress + '%';
                document.getElementById(`progressText`).innerHTML = `
                    ${progress}% (${completed_customers} de ${total_customers} clientes)`;
            });
        });
    </script>
</div>
