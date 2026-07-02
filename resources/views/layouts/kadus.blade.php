<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>

    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/header.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    @yield('css')
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body>

    @include('partials.header', ['role' => 'kadus'])
    @include('partials.headericon')
    @include('partials.sidebarkadus')

    <div class="sidebar-overlay" onclick="toggleSidebar()"></div>

    <div class="main-content">
        <div class="content-body">
            @yield('content')
        </div>
    </div>

    @yield('js')

    <!-- Global Delete Confirmation Modal -->
    <div id="deleteModal" class="modal">
        <div class="popup-content">
            <div class="popup-icon">!</div>
            <div class="popup-message">Apakah Anda yakin ingin menghapus data ini?</div>
            <form id="deleteForm" method="POST" action="">
                @csrf
                @method('DELETE')
                <div style="display: flex; gap: 10px; justify-content: center;">
                    <button type="button" class="btn-cancel-modal" onclick="closeModal('deleteModal')">Batal</button>
                    <button type="submit" class="btn-delete">Hapus</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="{{ asset('js/kadus.js') }}"></script>
    <div id="flash-data"
        data-success='@json(session()->get("success"))'
        data-error='@json(session()->get("error"))'
        data-errors='@json($errors->all())'>
    </div>

</body>

</html>