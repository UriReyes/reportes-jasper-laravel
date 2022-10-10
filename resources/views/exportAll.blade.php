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

        @livewire('export-all-reports')
    </div>
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
            if (data.progress == 100) {
                document.getElementById('loaderIndicator').style.display = "none";
                document.getElementById('loaderText').style.display = "none";
                document.getElementById('startAllProcessPlay').style.display = "block";
                document.getElementById('startAllProcessCharge').style.display = "none";
            } else {
                document.getElementById('startAllProcessPlay').style.display = "none";
                document.getElementById('startAllProcessCharge').style.display = "block";
                document.getElementById('loaderIndicator').style.display = "flex";
                document.getElementById('loaderText').style.display = "block";
                document.getElementById('mspName').innerHTML = data.customer;
                document.getElementById('percentageMsp').innerHTML = data.progress;
                document.getElementById('totalMonitorMsp').innerHTML = data.total_monitors;
                document.getElementById('completedMonitorMsp').innerHTML = data.completed_reports;
                let completedMSP = $(`#${data.zaaid}_customer`).prevAll('li').addClass("mspCompleted");

            }
        });
        var privateChannelDownloadAPIAll = pusher.subscribe(PUSHER_DOWNLOAD_INFORMATION_MASIVE_PROGRESS_CHANNEL);
        privateChannelDownloadAPIAll.bind(PUSHER_DOWNLOAD_INFORMATION_MASIVE_PROGRESS_EVENT, function(data) {
            localStorage.setItem('error-api', false);
            Livewire.emit('current-all-percentage-donwload', data);
        });

        var privateChannelDownloadAPIReload = pusher.subscribe(PUSHER_RELOAD_BECAUSE_ERROR_CHANNEL);
        privateChannelDownloadAPIReload.bind(PUSHER_RELOAD_BECAUSE_ERROR_EVENT, function(data) {
            localStorage.setItem('error-api', true);
            // setTimeout(() => {
            //     window.location.reload();
            // }, 1000);
            sendReloadEvent();
        });

        document.addEventListener('DOMContentLoaded', () => {
            sendReloadEvent();
        })

        function sendReloadEvent() {
            let errorOnAPI = localStorage.getItem('error-api');
            errorOnAPI = errorOnAPI == 'true' ? true : false;
            //console.log(errorOnAPI);
            if (errorOnAPI) {
                setTimeout(() => {
                    console.log('Error on API');
                    Livewire.emit('reloadProcessExportAll');
                }, 2000);
            }
        }
    </script>
@endsection
