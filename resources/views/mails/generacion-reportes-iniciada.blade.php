@extends('layouts.email')
@section('content')
    <p>Buen día estimado,</p>
    <p>Se informa que la generación masiva de reportes se ha iniciado a {{ now()->format('d-m-Y H:i:s') }}</p>
    <p>Usted seguirá recibiendo a lo largo del proceso cuando cada MSP sea finalizado y los reportes estén disponibles</p>
@endsection
