@extends('layouts.email')
@section('content')
    <p>Buen día estimado,</p>
    <p>Se informa que la generación masiva de reportes se ha finalizado a {{ now()->format('d-m-Y H:i:s') }}</p>
@endsection
