@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/warga/status-usulan.css') }}">
@endsection

@section('content')

<section class="form-section">

    <div class="form-container">

        <div class="form-title">
            Cek Status Usulan
        </div>

        <div class="form-body">

            <h3>Cek Status Usulan Anda</h3>

            <p style="text-align:center; margin-bottom:25px;">
                Silahkan masukkan no usulan yang anda dapatkan ketika melakukan pengajuan usulan.
            </p>

            @if(session('error'))
            <div style="color:red; text-align:center; margin-bottom:15px;">
                {{ session('error') }}
            </div>
            @endif

            <form action="{{ route('status.usulan.cek') }}" method="GET">

                <div class="form-group">
                    <label>No Usulan*</label>
                    <input type="text" name="kode_usulan" placeholder="Masukkan no usulan anda" required>
                </div>

                <div class="form-button">
                    <button type="submit">Kirim</button>
                </div>

            </form>

        </div>

    </div>

</section>

@endsection