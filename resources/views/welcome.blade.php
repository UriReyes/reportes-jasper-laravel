@extends('layouts.layout')

@section('content')
    @livewire('reportes-site-jasper', ['customers_its' => $customers_its])
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
    </script>
@endsection
