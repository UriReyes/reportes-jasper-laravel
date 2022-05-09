@extends('layouts.layout')

@section('content')
    @livewire('reportes-site-jasper', ['customers_its'=>$customers_its])
@endsection
@section('scripts')
    <script>
        Pusher.logToConsole = false;
        var pusher = new Pusher('1b717ef2209c3ce46729', {
      cluster: 'us2'
    });
        var channel = pusher.subscribe('progress-report');
        channel.bind('process-report', function(data) {
            Livewire.emit('current-percentage', data);
        });
    </script>
@endsection
