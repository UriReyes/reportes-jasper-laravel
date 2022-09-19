@extends('layouts.layout')
{{-- @section('title')
    Exportar Por MSP
@endsection --}}
@section('content')
    @if (count($customers_its) > 0)
        @livewire('reportes-site-jasper', ['customers_its' => $customers_its])
    @else
        @include('partials.error-api', [
            'titleErrorAPI' => 'Exportar Por MSP',
            'errorAPI' => 'No se pudo consultar la información del API, espere un momento y refresque la pagina',
        ])
    @endif
@endsection
@section('scripts')
    <script>
        let pusher_key = "{{ env('PUSHER_APP_KEY') }}";
        let pusher_cluster = "{{ env('PUSHER_APP_CLUSTER') }}";
        let PUSHER_RELOAD_BECAUSE_ERROR_CHANNEL = "{{ env('PUSHER_RELOAD_BECAUSE_ERROR_CHANNEL') }}";
        let PUSHER_RELOAD_BECAUSE_ERROR_EVENT = "{{ env('PUSHER_RELOAD_BECAUSE_ERROR_EVENT') }}";
        let PUSHER_DOWNLOAD_GENERATE_INDIVIDUAL_PROGRESS_CHANNEL =
            "{{ env('PUSHER_DOWNLOAD_GENERATE_INDIVIDUAL_PROGRESS_CHANNEL') }}"
        let PUSHER_DOWNLOAD_GENERATE_INDIVIDUAL_PROGRESS_EVENT =
            "{{ env('PUSHER_DOWNLOAD_GENERATE_INDIVIDUAL_PROGRESS_EVENT') }}";
        let PUSHER_DOWNLOAD_INFORMATION_INDIVIDUAL_PROGRESS_CHANNEL =
            "{{ env('PUSHER_DOWNLOAD_INFORMATION_INDIVIDUAL_PROGRESS_CHANNEL') }}"
        let PUSHER_DOWNLOAD_INFORMATION_INDIVIDUAL_PROGRESS_EVENT =
            "{{ env('PUSHER_DOWNLOAD_INFORMATION_INDIVIDUAL_PROGRESS_EVENT') }}"
        Pusher.logToConsole = false;
        var pusher = new Pusher(pusher_key, {
            cluster: pusher_cluster
        });
        var channel = pusher.subscribe(PUSHER_DOWNLOAD_GENERATE_INDIVIDUAL_PROGRESS_CHANNEL);
        channel.bind(PUSHER_DOWNLOAD_GENERATE_INDIVIDUAL_PROGRESS_EVENT, function(data) {
            localStorage.setItem('error-api-one', false);
            Livewire.emit('current-percentage', data);
        });
        var privateChannelDownloadAPI = pusher.subscribe(PUSHER_DOWNLOAD_INFORMATION_INDIVIDUAL_PROGRESS_CHANNEL);
        privateChannelDownloadAPI.bind(PUSHER_DOWNLOAD_INFORMATION_INDIVIDUAL_PROGRESS_EVENT, function(data) {
            localStorage.setItem('error-api-one', false);
            Livewire.emit('current-percentage-donwload', data);
        });

        var privateChannelDownloadAPI2 = pusher.subscribe(PUSHER_RELOAD_BECAUSE_ERROR_CHANNEL);
        privateChannelDownloadAPI2.bind(PUSHER_RELOAD_BECAUSE_ERROR_EVENT, function(data) {
            localStorage.setItem('error-api-one', true);
            // setTimeout(() => {
            //     window.location.reload();
            // }, 1000);
            sendEventReload();
        });

        document.addEventListener('DOMContentLoaded', () => {
            sendEventReload();
        })

        function sendEventReload() {
            let errorOnAPI = localStorage.getItem('error-api-one');
            errorOnAPI = errorOnAPI == 'true' ? true : false;
            if (errorOnAPI) {
                setTimeout(() => {
                    console.log('Error on API ONE');
                    Livewire.emit('reloadProcessExport');
                }, 2000);
            }
        }
    </script>
@endsection
