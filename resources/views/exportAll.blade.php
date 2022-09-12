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
        let PUSHER_RELOAD_BECAUSE_ERROR_CHANNEL = "{{ env('PUSHER_RELOAD_BECAUSE_ERROR_CHANNEL') }}";
        let PUSHER_RELOAD_BECAUSE_ERROR_EVENT = "{{ env('PUSHER_RELOAD_BECAUSE_ERROR_EVENT') }}";
        let PUSHER_DOWNLOAD_GENERATE_INDIVIDUAL_PROGRESS_CHANNEL =
            "{{ env('PUSHER_DOWNLOAD_GENERATE_INDIVIDUAL_PROGRESS_CHANNEL') }}";
        let PUSHER_DOWNLOAD_GENERATE_INDIVIDUAL_PROGRESS_EVENT =
            "{{ env('PUSHER_DOWNLOAD_GENERATE_INDIVIDUAL_PROGRESS_EVENT') }}";
        let PUSHER_DOWNLOAD_GENERATE_MASIVE_PROGRESS_CHANNEL =
            "{{ env('PUSHER_DOWNLOAD_GENERATE_MASIVE_PROGRESS_CHANNEL') }}";
        let PUSHER_DOWNLOAD_GENERATE_MASIVE_PROGRESS_EVENT = "{{ env('PUSHER_DOWNLOAD_GENERATE_MASIVE_PROGRESS_EVENT') }}";
        let PUSHER_DOWNLOAD_INFORMATION_INDIVIDUAL_PROGRESS_CHANNEL =
            "{{ env('PUSHER_DOWNLOAD_INFORMATION_INDIVIDUAL_PROGRESS_CHANNEL') }}";
        let PUSHER_DOWNLOAD_INFORMATION_INDIVIDUAL_PROGRESS_EVENT =
            "{{ env('PUSHER_DOWNLOAD_INFORMATION_INDIVIDUAL_PROGRESS_EVENT') }}";
        let PUSHER_DOWNLOAD_INFORMATION_MASIVE_PROGRESS_CHANNEL =
            "{{ env('PUSHER_DOWNLOAD_INFORMATION_MASIVE_PROGRESS_CHANNEL') }}";
        let PUSHER_DOWNLOAD_INFORMATION_MASIVE_PROGRESS_EVENT =
            "{{ env('PUSHER_DOWNLOAD_INFORMATION_MASIVE_PROGRESS_EVENT') }}";

        Pusher.logToConsole = false;
        var pusher = new Pusher(pusher_key, {
            cluster: pusher_cluster
        });
        var channel = pusher.subscribe(PUSHER_DOWNLOAD_GENERATE_INDIVIDUAL_PROGRESS_CHANNEL);
        channel.bind(PUSHER_DOWNLOAD_GENERATE_INDIVIDUAL_PROGRESS_EVENT, function(data) {
            localStorage.setItem('error-api', false);
            Livewire.emit('current-percentage', data);
        });

        var channel2 = pusher.subscribe(PUSHER_DOWNLOAD_GENERATE_MASIVE_PROGRESS_CHANNEL);
        channel2.bind(PUSHER_DOWNLOAD_GENERATE_MASIVE_PROGRESS_EVENT, function(data) {
            localStorage.setItem('error-api', false);
            Livewire.emit('current-percentage-customers', data);
        });

        var privateChannelDownloadAPI = pusher.subscribe(PUSHER_DOWNLOAD_INFORMATION_INDIVIDUAL_PROGRESS_CHANNEL);
        privateChannelDownloadAPI.bind(PUSHER_DOWNLOAD_INFORMATION_INDIVIDUAL_PROGRESS_EVENT, function(data) {
            localStorage.setItem('error-api', false);
            Livewire.emit('current-percentage-donwload', data);
        });
        var privateChannelDownloadAPIAll = pusher.subscribe(PUSHER_DOWNLOAD_INFORMATION_MASIVE_PROGRESS_CHANNEL);
        privateChannelDownloadAPIAll.bind(PUSHER_DOWNLOAD_INFORMATION_MASIVE_PROGRESS_EVENT, function(data) {
            localStorage.setItem('error-api', false);
            Livewire.emit('current-all-percentage-donwload', data);
        });

        var privateChannelDownloadAPIReload = pusher.subscribe(PUSHER_RELOAD_BECAUSE_ERROR_CHANNEL);
        privateChannelDownloadAPIReload.bind(PUSHER_RELOAD_BECAUSE_ERROR_EVENT, function(data) {
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
