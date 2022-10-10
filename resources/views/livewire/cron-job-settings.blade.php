<div>
    <x-loading-indicator wire:loading />
    <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded mb-4 relative text-xs" role="alert">
        <strong class="font-bold">Importante!</strong>
        <span class="block sm:inline"> De clic en el input de hora, una vez seleccionada la hora el guardado se hará de
            manera automática
            <p class="m-0">La ejecución se lleva a cabo el día 1 de cada mes a las {{ $time }}</p>
        </span>
    </div>
    <p class="m-0">
        <span class="mb-2">Ajustar Hora De Ejecución</span>
    </p>
    <div class="relative mb-1">
        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
            <i class="fas fa-clock"></i>
        </div>
        <input type="text" id="timeCron" wire:model="cronTime" placeholder="{{ $time }}"
            aria-label="{{ $time }}"
            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5  dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
            value="{{ $time }}" />
    </div>

    {{-- <div class="input-group mb-3">
        <input style="border: 2px solid #bebebe;border-radius: 5px;" wire:model="cronTime" type="text"
            class="form-control" id="timeCron" placeholder="{{ $time }}" aria-label="{{ $time }}"
            aria-describedby="basic-addon1" value="{{ $time }}">
    </div> --}}
    @section('scripts')
        <script>
            let CRON_TIME = "{{ $time }}";
            let HOUR = CRON_TIME.split(':')[0];
            let MINUTE = CRON_TIME.split(':')[1];
            console.log(CRON_TIME, HOUR, MINUTE);
            $("#timeCron").flatpickr({
                enableTime: true,
                defaultHour: HOUR,
                defaultMinute: MINUTE,
                time_24hr: true,
                noCalendar: true
            });
        </script>
    @endsection
</div>
