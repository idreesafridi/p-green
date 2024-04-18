<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
  
    <title>
        @if (Session::has('construction_id') || $pageTitle != null)
            @if ($pageTitle == 'users' || $pageTitle == 'admin' || $pageTitle == 'technician' || $pageTitle == 'business' || $pageTitle == 'businessconsultant' || $pageTitle == 'photovoltaic' || $pageTitle == 'user' || $pageTitle == 'worker')
                Lista Utenti
            @elseif ($pageTitle == 'Cliente' || $pageTitle == 'Cantiere' || $pageTitle == 'Materiali' || $pageTitle == 'Assistenze' || $pageTitle == 'Stato' || $pageTitle == 'Note' || $pageTitle == 'Immagini')
                @php
                    $constructionId = Session::get('construction_id');
                    $data = App\Models\ConstructionSite::find($constructionId);
                    $name = $data->surename . ' ' . $data->name
                @endphp
                {{ $name }}
            @elseif ($pageTitle == 'Assistence')
                Assistenze
            @elseif ($pageTitle == 'All reports')
                Rapporti
            @elseif ($pageTitle == 'Construction')
                Nuovo Cantiere
            @elseif ($pageTitle == 'Add technician')
                Nuovo Tecnico
            @elseif ($pageTitle == 'Add business')
                Nuovo Impresa
            @elseif ($pageTitle == 'Add user')
                Nuovo Utenti
            @elseif ($pageTitle == 'Add worker')
                Nuovo Operaio
            @elseif ($pageTitle == 'Add businessconsultant')
                Nuovo Commercialista
            @elseif ($pageTitle == 'Add photovoltaic')
                Nuovo Ingegnere Fotovoltaico
            @elseif ($pageTitle == 'Nuovo Materiale')
                Nuovo Materiale
            @else
                PORTALE GREENGEN
            @endif
        @else
            PORTALE GREENGEN
        @endif
        

    </title>

    <link rel="icon" href="{{ asset('assets/images/favicon.png') }}" type="image/x-icon">

    {{-- bootstrap --}}
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">

    {{-- Font Icons --}}
    <link rel="stylesheet" href="{{ asset('assets/css/all.css') }}">

    {{-- Custom Style --}}
    <link rel="stylesheet" href="{{ asset('assets/css/main.css') }}">

    <link href="{{ asset('assets/gallery-assets/css/lightgallery.css') }}" rel="stylesheet">

    <style>
        .normal_body {
            background-color: #53584d40 !important;
        }
    </style>

    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="https://code.jquery.com/ui/1.10.4/themes/ui-lightness/jquery-ui.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.1/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.4.0/css/responsive.bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.css" />
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />

    @yield('styles')
</head>

<body class="{{ request()->route() != null? (request()->route()->getName() == 'login'? 'body-log_in': 'normal_body'): '' }}">