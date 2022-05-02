<div class="w-full">
    {{-- <a target="_blank"
        href="{{ route('reporteParametros', [
            'customer' => $customer['name'],
            'zaaid' => $customer['zaaid'],
        ]) }}"
        class="leading-normal bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-base md:text-2xl mb-8 text-center md:text-left slide-in-bottom-subtitle">
        {{ $customer['name'] }}</a> --}}

    <div class="flex justify-between mb-1">
        <span class="text-base font-medium text-blue-700 dark:text-white">
            {{ $customer['name'] }}
            <i class="fa-solid fa-play text-green-500" wire:click.prevent="startProcess()"></i>
        </span>
        <span class="text-sm font-medium text-blue-700 dark:text-white">{{ $totalMonitors }}</span>
    </div>
    <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
        <div class="bg-blue-600 h-2.5 rounded-full" style="width: 45%"></div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Livewire.on('current-percentage', (e) => {
                console.log(e);
            });
        });
    </script>
</div>
