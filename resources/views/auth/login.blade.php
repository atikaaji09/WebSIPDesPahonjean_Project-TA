@extends('layouts.app')

@php
$hideMenu = true;
@endphp

@section('css')
<link rel="stylesheet" href="{{ asset('css/auth/login.css') }}">
<style>
    .password-group {
        position: relative;
    }

    .password-group input {
        width: 100%;
        padding-right: 40px;
    }

    .toggle-password {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        cursor: pointer;
        user-select: none;
        width: 20px;
        height: 20px;
    }
</style>
@endsection

@section('content')
<div class="login-section">
    <div class="login-card">
        <h2>Login</h2>

        {{-- Flash data untuk SweetAlert (dibaca oleh initFlashAlert di admin.js) --}}
        <div id="flash-data"
            data-success='@json(session()->get("success"))'
            data-error='@json(session()->get("error"))'
            data-errors='@json($errors->all())'>
        </div>

        <form action="/login" method="POST">
            @csrf

            <div class="input-group">
                <img src="{{ asset('images/person-fill.svg') }}" alt="Username">
                <input type="text" name="username" placeholder="Masukkan username" required>
            </div>

            <div class="input-group password-group">
                <img src="{{ asset('images/lock-fill.svg') }}" alt="Password">
                <input type="password" id="password" name="password" placeholder="Masukkan password" required>
                <img src="{{ asset('images/eye-fill.svg') }}" alt="Toggle Password" id="togglePassword" class="toggle-password">
            </div>

            <button type="submit" class="login-btn">Login</button>
        </form>
    </div>
</div>
@endsection

@section('js')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const toggleIcon = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');
        const eyeOpen = "{{ asset('images/eye-fill.svg') }}";
        const eyeClosed = "{{ asset('images/eye-slash-fill.svg') }}";

        if (toggleIcon && passwordInput) {
            toggleIcon.addEventListener('click', function() {
                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    toggleIcon.src = eyeClosed;
                } else {
                    passwordInput.type = 'password';
                    toggleIcon.src = eyeOpen;
                }
            });
        }

        if (typeof initFlashAlert === 'function') {
            initFlashAlert();
        }
    });
</script>
@endsection