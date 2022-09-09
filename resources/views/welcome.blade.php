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
        Pusher.logToConsole = false;
        var pusher = new Pusher(pusher_key, {
            cluster: pusher_cluster
        });
        var channel = pusher.subscribe('progress-reportD');
        channel.bind('process-report', function(data) {
            localStorage.setItem('error-api-one', false);
            Livewire.emit('current-percentage', data);
        });
        var privateChannelDownloadAPI = pusher.subscribe('downloadAPI');
        privateChannelDownloadAPI.bind('downloadAPIEvent', function(data) {
            localStorage.setItem('error-api-one', false);
            Livewire.emit('current-percentage-donwload', data);
        });

        var privateChannelDownloadAPI2 = pusher.subscribe('reloadBecauseExistErrorOnAPI');
        privateChannelDownloadAPI2.bind('reloadBecauseExistErrorOnAPIEvent', function(data) {
            localStorage.setItem('error-api-one', true);
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        });

        document.addEventListener('DOMContentLoaded', () => {
            let errorOnAPI = localStorage.getItem('error-api-one');
            errorOnAPI = errorOnAPI == 'true' ? true : false;
            if (errorOnAPI) {
                setTimeout(() => {
                    console.log('Error on API ONE');
                    Livewire.emit('reloadProcessExport');
                }, 2000);
            }
        })
    </script>
@endsection
