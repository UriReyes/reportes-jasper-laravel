@extends('layouts.layout')
{{-- @section('title')
    Exportar Todo
@endsection --}}
@section('content')
    @livewire('downloaded-information-m-s-p-files')
@endsection

@section('scripts')
    <script>
        let pusher_key = "{{ env('PUSHER_APP_KEY') }}";
        let pusher_cluster = "{{ env('PUSHER_APP_CLUSTER') }}";
        let PUSHER_DOWNLOAD_GENERATE_INDIVIDUAL_PROGRESS_CHANNEL = env(
            'PUSHER_DOWNLOAD_GENERATE_INDIVIDUAL_PROGRESS_CHANNEL');
        let PUSHER_DOWNLOAD_GENERATE_INDIVIDUAL_PROGRESS_EVENT = env(
            'PUSHER_DOWNLOAD_GENERATE_INDIVIDUAL_PROGRESS_EVENT');
        Pusher.logToConsole = false;
        var pusher = new Pusher(pusher_key, {
            cluster: pusher_cluster
        });
        var channel = pusher.subscribe(PUSHER_DOWNLOAD_GENERATE_INDIVIDUAL_PROGRESS_CHANNEL);
        channel.bind(PUSHER_DOWNLOAD_GENERATE_INDIVIDUAL_PROGRESS_EVENT, function(data) {
            Livewire.emit('current-percentage', data);
        });
    </script>
@endsection
