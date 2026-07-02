@extends('layouts.kadus')

@section('title', 'Dashboard')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/index.css') }}">
<link rel="stylesheet" href="{{ asset('css/header.css') }}">
@endsection

@section('content')

<div class="dashboard-container">

    <div class="dashboard-grid">

        {{-- TOTAL USULAN --}}
        <div class="dashboard-card card-total">

            <div class="card-label">SUMMARY</div>

            <div class="card-icon-right">📋</div>

            <div class="dashboard-number">{{ $totalUsulan }}</div>
            <div class="dashboard-title">Total Usulan</div>

            <div class="card-decor"></div>

        </div>


        {{-- DIPROSES --}}
        <div class="dashboard-card card-proses">

            <div class="card-label">STATUS</div>

            <div class="card-icon-right">⏳</div>

            <div class="dashboard-number">{{ $diproses }}</div>
            <div class="dashboard-title">Diproses</div>

            <div class="card-decor"></div>

        </div>


        {{-- LANJUTAN --}}
        <div class="dashboard-card card-lanjutan">

            <div class="card-label">STATUS</div>

            <div class="card-icon-right">📌</div>

            <div class="dashboard-number">{{ $lanjutan }}</div>
            <div class="dashboard-title">Lanjutan</div>

            <div class="card-decor"></div>

        </div>


        {{-- SELESAI --}}
        <div class="dashboard-card card-selesai">

            <div class="card-label">STATUS</div>

            <div class="card-icon-right">✅</div>

            <div class="dashboard-number">{{ $selesai }}</div>
            <div class="dashboard-title">Selesai</div>

            <div class="card-decor"></div>

        </div>

    </div>

</div>

@endsection