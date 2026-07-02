@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/warga/data-usulan.css') }}">
@endsection

@section('content')

@php
$status = strtolower(trim($usulan->status));
@endphp

<div class="usulan-card">

    <div class="usulan-header">
        Data Usulan
    </div>

    <div class="usulan-grid">

        <div class="usulan-left">

            <div class="row">
                <span class="label">Kode Usulan</span>
                <span class="colon">:</span>
                <span class="value">{{ $usulan->no_usulan }}</span>
            </div>

            <div class="row">
                <span class="label">Nama Pengusul</span>
                <span class="colon">:</span>
                <span class="value">{{ $usulan->nama_lengkap }}</span>
            </div>

            <div class="row">
                <span class="label">Nama Dusun</span>
                <span class="colon">:</span>
                <span class="value">{{ $usulan->rtrw->dusun->nama_dusun ?? '-' }}</span>
            </div>

            <div class="row">
                <span class="label">RT</span>
                <span class="colon">:</span>
                <span class="value">{{ $usulan->rtrw->rt ?? '-' }}</span>
            </div>

            <div class="row">
                <span class="label">RW</span>
                <span class="colon">:</span>
                <span class="value">{{ $usulan->rtrw->rw ?? '-' }}</span>
            </div>

            <div class="row">
                <span class="label">Tanggal Pengajuan</span>
                <span class="colon">:</span>
                <span class="value">{{ $usulan->created_at->format('d M Y') }}</span>
            </div>

        </div>

        <div class="usulan-right">

            <div class="row">
                <span class="label">Gagasan Kegiatan</span>
                <span class="colon">:</span>
                <span class="value">{{ $usulan->gagasan_kegiatan }}</span>
            </div>

            <div class="row">
                <span class="label">Lokasi Kegiatan</span>
                <span class="colon">:</span>
                <span class="value">{{ $usulan->lokasi }}</span>
            </div>

            <div class="row">
                <span class="label">Perkiraan Volume</span>
                <span class="colon">:</span>
                <span class="value">{{ $usulan->volume }} {{ $usulan->satuan }}</span>
            </div>

            <div class="row">
                <span class="label">Penerima Manfaat</span>
                <span class="colon">:</span>
                <span class="value">
                    {{ $usulan->penerima_laki + $usulan->penerima_perempuan }}
                    Orang
                </span>
            </div>

        </div>

    </div>

</div>


<div class="usulan-card">

    <div class="usulan-header">
        Status Usulan
    </div>

    <div class="status">


        @php
        $status = strtolower(trim($usulan->status));

        // mapping status
        if ($status == 'masuk_rpjmdes') {
        $status = 'diterima';
        }
        @endphp
        <!-- STEP 1 -->
        <div class="step 
    {{ $status != 'diajukan' ? 'active' : '' }}
    {{ $status == 'ditolak' ? 'ditolak' : '' }}">
            <div class="circle">1</div>
            <span>Diajukan</span>
        </div>

        <!-- STEP 2 -->
        <div class="step 
    {{ $status == 'diterima' ? 'active' : '' }} 
    {{ $status == 'ditolak' ? 'active ditolak' : '' }}">
            <div class="circle">2</div>
            <span>
                {{ $status == 'diajukan' ? 'Diterima / Ditolak' : ucfirst($status) }}
            </span>
        </div>

    </div>

</div>

@endsection