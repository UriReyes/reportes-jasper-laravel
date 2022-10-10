@extends('layouts.layout')
{{-- @section('title')
    Exportar Todo
@endsection --}}
@section('content')
    <div class="w-full" style="background: white;padding: 10px;border-radius: 8px;">
        <h5 class="mb-2 text-xl font-bold tracking-tight text-gray-900 dark:text-white">
            <i class="fa-solid fa-bell"></i> Notificaciones
        </h5>
        <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded mb-4 relative text-xs" role="alert">
            <strong class="font-bold">Importante!</strong>
            <span class="block sm:inline"> Debe ingresar un correo electrónico donde desee que se le notifique
                la finalización de la generación de
                Reportes por cada MSP (Solo para el proceso Masivo)
                <p class="m-0">PARA GUARDAR PRESIONE EL BOTÓN "GUARDAR"</p>
            </span>
        </div>

        <div>
            @livewire('notificaciones-settings')
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('vendor/file-manager/js/file-manager.js') }}"></script>
@endsection
