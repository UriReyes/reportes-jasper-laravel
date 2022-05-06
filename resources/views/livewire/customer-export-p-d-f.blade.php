<div class="w-full">
    <div class="p-6 max-w-sm bg-white rounded-lg border border-gray-200 shadow-md dark:bg-gray-800 dark:border-gray-700">
        <h5 class="mb-2 text-xl font-bold tracking-tight text-gray-900 dark:text-white">
            {{ $customer['name'] }} - {{ $customer['zaaid'] }}
        </h5>
        <small class="mb-3 font-normal text-gray-700 dark:text-gray-400">
            <i class="fas fa-info-circle mr-2"></i> Mes a consultar: {{ $last_month }}
        </small>
        <div class="flex justify-between mb-1" style="align-items: center;">
            <span class="text-base font-medium text-blue-700 dark:text-white">
                <i class="fa-solid fa-play text-green-500" wire:loading.remove wire:click.prevent="startProcess()"></i>
                <i class="fa-solid fa-spinner fa-spin-pulse" wire:loading wire:target="startProcess"></i>
            </span>
            <div class="w-full text-right">
                <small id="progressText{{ $customer['zaaid'] }}"
                    class="text-xs font-medium text-blue-700 dark:text-white">0% (0 de 0 monitores)</small>
                <div class="ml-2 w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
                    <div id="percentageBar{{ $customer['zaaid'] }}" class="bg-blue-600 h-2.5 rounded-full"
                        style="width: {{ $percentage }}%"></div>
                </div>
            </div>
        </div>

    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Livewire.on('current-percentage', (data) => {
                let {
                    progress,
                    completed_reports,
                    total_monitors,
                    zaaid
                } = data;
                document.getElementById(`percentageBar${zaaid}`).style.width = progress + '%';
                document.getElementById(`progressText${zaaid}`).innerHTML = `
                    ${progress}% (${completed_reports} de ${total_monitors} monitores)
                `;
            });
        });
    </script>
</div>
