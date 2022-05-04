<div class="w-full">
    <div class="grid grid-cols-4 gap-4 mb-6">
        <div class="w-full">
            <label for="countries" class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-400"><i
                    class="fa-solid fa-filter mr-2"></i>Selecciona el periodo</label>
            <select id="periods" wire:model="period"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                <option value="7">Mes Anterior</option>
                <option value="13">Actual</option>
            </select>
        </div>
    </div>
    <div class="grid grid-cols-3 gap-3">
        @foreach ($customers_its as $customer)
            @livewire('customer-export-p-d-f', ['customer' => $customer,
            'period'=>$period,'last_month'=>$last_month],key($customer['zaaid']))
        @endforeach
    </div>
</div>
