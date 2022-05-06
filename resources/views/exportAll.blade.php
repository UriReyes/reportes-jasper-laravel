@extends('layouts.layout')

@section('content')
    @livewire('export-all-reports')
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

        var channel2 = pusher.subscribe('progress-reports');
        channel2.bind('process-reports', function(data) {
            Livewire.emit('current-percentage-customers', data);
        });
    </script>
@endsection
