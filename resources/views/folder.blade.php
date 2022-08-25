@extends('layouts.layout')
{{-- @section('title')
    Exportar Todo
@endsection --}}
@section('content')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">

    <link rel="stylesheet" href="{{ asset('vendor/file-manager/css/file-manager.css') }}">
    <style>
        a {
            text-decoration: none !important;
            color: #3c4e6d;
        }

        a:hover {
            color: #3c4e6d;
        }

        aside {
            z-index: 10000 !important;
        }

    </style>
    <div class="w-full" style="background: white;padding: 10px;border-radius: 8px;">
        <h5 class="mb-2 text-xl font-bold tracking-tight text-gray-900 dark:text-white">
            <i class="fas fa-folder-open"></i> Gestor de Archivos
        </h5>
        <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded relative text-xs" role="alert">
            <strong class="font-bold">Importante!</strong>
            <span class="block sm:inline">Todo la estructura de archivos puede ser visualizada desde el siguiente gestor,
                tenga cuidado al eliminar archivos ya que deberán ser generados nuevamente.</span>
        </div>
        <div id="fm" style="height: 450px;"></div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('vendor/file-manager/js/file-manager.js') }}"></script>
@endsection
