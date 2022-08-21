@extends('layouts.layout')

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
            Livewire.emit('current-percentage', data);
        });

        var channel2 = pusher.subscribe('progress-reportDs');
        channel2.bind('process-reports', function(data) {
            Livewire.emit('current-percentage-customers', data);
        });
    </script>
@endsection
