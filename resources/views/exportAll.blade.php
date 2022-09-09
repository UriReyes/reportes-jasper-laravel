@extends('layouts.layout')
{{-- @section('title')
    Exportar Todo
@endsection --}}
@section('content')
    @livewire('export-all-reports')
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
            localStorage.setItem('error-api', false);
            Livewire.emit('current-percentage', data);
        });

        var channel2 = pusher.subscribe('progress-reportDs');
        channel2.bind('process-reports', function(data) {
            localStorage.setItem('error-api', false);
            Livewire.emit('current-percentage-customers', data);
        });

        var privateChannelDownloadAPI = pusher.subscribe('downloadAPI');
        privateChannelDownloadAPI.bind('downloadAPIEvent', function(data) {
            localStorage.setItem('error-api', false);
            Livewire.emit('current-percentage-donwload', data);
        });
        var privateChannelDownloadAPIAll = pusher.subscribe('downloadAllAPI');
        privateChannelDownloadAPIAll.bind('downloadAllAPIEvent', function(data) {
            localStorage.setItem('error-api', false);
            Livewire.emit('current-all-percentage-donwload', data);
        });

        var privateChannelDownloadAPIReload = pusher.subscribe('reloadBecauseExistErrorOnAPI');
        privateChannelDownloadAPIReload.bind('reloadBecauseExistErrorOnAPIEvent', function(data) {
            localStorage.setItem('error-api', true);
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        });

        document.addEventListener('DOMContentLoaded', () => {
            let errorOnAPI = localStorage.getItem('error-api');
            errorOnAPI = errorOnAPI == 'true' ? true : false;
            //console.log(errorOnAPI);
            if (errorOnAPI) {
                setTimeout(() => {
                    console.log('Error on API');
                    Livewire.emit('reloadProcessExportAll');
                }, 2000);
            }
        })
    </script>
@endsection
