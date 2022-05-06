@extends('layouts.layout')

@section('content')
    @livewire('reportes-site-jasper', ['customers_its'=>$customers_its])
@endsection
@section('scripts')
    <script>
        Pusher.logToConsole = false;
        var pusher = new Pusher('97627475d5972b5f156a', {
            cluster: 'us2'
        });
        var channel = pusher.subscribe('progress-report');
        channel.bind('process-report', function(data) {
            Livewire.emit('current-percentage', data);
        });
    </script>
@endsection
