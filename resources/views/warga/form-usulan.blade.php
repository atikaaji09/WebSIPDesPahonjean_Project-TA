@extends('layouts.app')

@section('content')

<div class="form-section">

    <div class="form-container">

        <div class="form-title">
            Formulir Pengajuan Usulan
        </div>

        <div class="form-body">

            @if(!$periodeAktif)
            <div class="form-disable">
                Periode pengajuan usulan sedang ditutup
            </div>
            @endif

            @if(session('success'))
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
            <script>
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: "{{ session('success') }}",
                    confirmButtonText: 'OK'
                });
            </script>
            @endif

            <form action="{{ route('form.usulan.step1') }}" method="POST">
                @csrf

                <fieldset {{ !$periodeAktif ? 'disabled' : '' }}>

                    <h3>Identitas Pengusul</h3>

                    <div class="form-group">
                        <label>No Usulan</label>
                        <input type="text" value="{{ $noUsulan }}" readonly>
                        <small class="text-note">
                            *Catat nomor usulan ini untuk mengecek status usulan Anda nanti.
                        </small>
                    </div>

                    <div class="form-group">
                        <label>Nama Lengkap*</label>
                        <input type="text" name="nama_lengkap" required>
                    </div>

                    <div class="form-group">
                        <label>Dusun*</label>
                        <select name="dusun_id" id="dusunSelect" required>
                            <option value="">Pilih Dusun</option>
                            @foreach($dusun as $d)
                            <option value="{{ $d->id }}">{{ $d->nama_dusun }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>RT/RW*</label>
                        <select name="rt_rw_id" id="rtRwSelect" required>
                            <option value="">Pilih RT / RW</option>
                        </select>
                    </div>

                </fieldset>

                <div class="form-button">
                    <button type="submit" {{ !$periodeAktif ? 'disabled' : '' }}>
                        Selanjutnya
                    </button>
                </div>

            </form>

        </div>

    </div>

</div>

@endsection