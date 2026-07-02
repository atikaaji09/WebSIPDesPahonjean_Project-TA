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

            <form action="{{ route('form.usulan.store') }}" method="POST">
                @csrf

                <fieldset {{ !$periodeAktif ? 'disabled' : '' }}>

                    <h3>Data Usulan</h3>

                    <div class="form-group">
                        <label>Gagasan Kegiatan*</label>
                        <input type="text" name="gagasan_kegiatan" placeholder="Masukkan gagasan kegiatan" required>
                    </div>

                    <div class="form-group">
                        <label>Lokasi Kegiatan*</label>
                        <input type="text" name="lokasi" placeholder="Masukkan lokasi spesifik" required>
                    </div>

                    <div class="form-row">

                        <div class="form-group">
                            <label>Volume*</label>
                            <input type="text" name="volume" placeholder="Contoh: 5x2 atau 120" required>
                        </div>

                        <div class="form-group">
                            <label>Satuan*</label>
                            <select name="satuan" id="satuanSelect" required>
                                <option value="">-- Pilih Satuan --</option>
                                <option value="m">Meter (m)</option>
                                <option value="m2">Meter Persegi (m²)</option>
                                <option value="m3">Meter Kubik (m³)</option>
                                <option value="unit">Unit</option>
                                <option value="buah">Buah</option>
                                <option value="orang">Orang</option>
                                <option value="ekor">Ekor</option>
                                <option value="paket">Paket</option>
                                <option value="kg">Kg</option>
                                <option value="persen">%</option>
                            </select>
                        </div>

                    </div>

                    <div class="benefit-section">

                        <label class="benefit-title">Penerima Manfaat</label>

                        <div class="benefit-grid">

                            <div class="form-group">
                                <label>Laki Laki</label>
                                <input type="number" name="penerima_laki" placeholder="0" required>
                            </div>

                            <div class="form-group">
                                <label>Perempuan</label>
                                <input type="number" name="penerima_perempuan" placeholder="0" required>
                            </div>

                            <div class="form-group">
                                <label>RTM (Rumah Tangga Miskin)</label>
                                <input type="number" name="penerima_rtm" placeholder="0">
                            </div>

                        </div>

                    </div>

                </fieldset>

                <div class="form-button">
                    <button type="submit" {{ !$periodeAktif ? 'disabled' : '' }}>
                        Kirim Usulan
                    </button>
                </div>

            </form>

        </div>

    </div>

    <p class="form-info">
        ⓘ Pastikan seluruh data yang Anda masukkan sudah benar sebelum menekan tombol kirim.
    </p>

</div>

@endsection

@section('js')

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div id="flash-data"
    data-error="{{ session('error') }}"
    data-success="{{ session('success') }}">
</div>

<script src="{{ asset('js/warga/usulan.js') }}"></script>

@endsection