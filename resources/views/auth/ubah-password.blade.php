@extends('layouts.app')

@php
$hideMenu = true;
@endphp

@section('content')
<div class="login-section">

    <div class="login-card">
        <h2>Ubah Password</h2>

        {{-- Flash data untuk SweetAlert (dibaca oleh initFlashAlert di admin.js) --}}
        <div id="flash-data"
            data-success='@json(session()->get("success"))'
            data-error='@json(session()->get("error"))'
            data-errors='@json($errors->all())'>
        </div>

        <form method="POST" action="{{ route('password.update') }}">
            @csrf

            <div class="input-group password-group">
                <input type="password" id="password_lama" name="password_lama" placeholder="Password Lama" required>
                <img src="{{ asset('images/eye-fill.svg') }}" class="toggle-password"
                    data-target="password_lama">
            </div>

            <div class="input-group password-group">
                <input type="password" id="password_baru" name="password_baru" placeholder="Password Baru" required>
                <img src="{{ asset('images/eye-fill.svg') }}" class="toggle-password"
                    data-target="password_baru">
            </div>

            <div class="input-group password-group">
                <input type="password" id="password_baru_confirmation" name="password_baru_confirmation"
                    placeholder="Konfirmasi Password" required>
                <img src="{{ asset('images/eye-fill.svg') }}" class="toggle-password"
                    data-target="password_baru_confirmation">
            </div>

            <button type="submit" class="login-btn">Simpan</button>

        </form>
    </div>

</div>
@endsection

@section('js')
<script>
    document.addEventListener("DOMContentLoaded", function() {

        // Toggle visibility password
        const eyeOpen = "{{ asset('images/eye-fill.svg') }}";
        const eyeClosed = "{{ asset('images/eye-slash-fill.svg') }}";

        document.querySelectorAll(".toggle-password").forEach(function(icon) {
            icon.addEventListener("click", function() {
                const targetId = this.getAttribute("data-target");
                const input = document.getElementById(targetId);
                if (input.type === "password") {
                    input.type = "text";
                    this.src = eyeClosed;
                } else {
                    input.type = "password";
                    this.src = eyeOpen;
                }
            });
        });

        // Panggil initFlashAlert dari admin.js (sudah di-load oleh layouts.app)
        if (typeof initFlashAlert === 'function') {
            initFlashAlert();
        }

    });
</script>
@endsection