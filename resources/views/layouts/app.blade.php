<!DOCTYPE html>
<html>

<head>
    <title>Pengajuan Usulan RPJMDes</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="{{ asset('css/header.css') }}">
    <link rel="stylesheet" href="{{ asset('css/footer.css') }}">

    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- CSS Global -->
    <link rel="stylesheet" href="{{ asset('css/warga/home.css') }}">
    <link rel="stylesheet" href="{{ asset('css/warga/form-usulan.css') }}">

    <!-- CSS halaman tertentu -->
    @yield('css')

</head>

<body>

    @include('partials.header', ['hideMenu' => $hideMenu ?? false])

    <main class="main-content warga">
        @yield('content')
    </main>

    @include('partials.footer')
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- File JS utama -->
    <script src="{{ asset('js/admin.js') }}"></script>

    @yield('js')

    <script>
        document.addEventListener("DOMContentLoaded", function() {

            const lazyElements = document.querySelectorAll(".lazy-load");

            const observer = new IntersectionObserver(function(entries) {

                entries.forEach(function(entry) {

                    if (entry.isIntersecting) {
                        entry.target.classList.add("lazy-load-visible");
                    } else {
                        entry.target.classList.remove("lazy-load-visible");
                    }

                });

            }, {
                threshold: 0.2
            });

            lazyElements.forEach(function(el) {
                observer.observe(el);
            });

        });
    </script>

</body>

</html>
