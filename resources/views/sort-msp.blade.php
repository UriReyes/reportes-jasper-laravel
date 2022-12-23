@extends('layouts.layout')
{{-- @section('title')
    Exportar Todo
@endsection --}}
@section('content')
    <style>
        .mspCompleted {
            background: rgb(45 90 202);
            color: white;
        }

        .mspCompleted::before {
            font-family: "Font Awesome 5 Free";
            content: "\f00c";
            display: inline-block;
            padding-right: 3px;
            vertical-align: middle;
            font-weight: 900;
        }
    </style>
    <div style="position: relative;">
        @livewire('sort-msps')
    </div>
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
            if (data.progress == 100) {
                document.getElementById('loaderIndicator').style.display = "none";
            } else {
                document.getElementById('loaderIndicator').style.display = "flex";
            }
        });
    </script>
@endsection
