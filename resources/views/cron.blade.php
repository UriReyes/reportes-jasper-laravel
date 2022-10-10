@extends('layouts.layout')
{{-- @section('title')
    Exportar Todo
@endsection --}}
@section('content')
    <div class="w-full" style="background: white;padding: 10px;border-radius: 8px;">
        <h5 class="mb-2 text-xl font-bold tracking-tight text-gray-900 dark:text-white">
            <i class="fa-solid fa-clock"></i> Tarea Programada
        </h5>

        <div>
            @livewire('cron-job-settings')
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('vendor/file-manager/js/file-manager.js') }}"></script>
@endsection
