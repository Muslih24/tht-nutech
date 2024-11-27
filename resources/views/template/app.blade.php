<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="referrer" content="no-referrer-when-downgrade">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/2.1.8/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://tht-nutech-production.up.railway.app/assets/css/style.css')" />
    <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</head>
<meta name="csrf-token" content="{{ csrf_token() }}">

<body>
        @if (session('success'))
            <script>
                Swal.fire({
                    title: 'Berhasil!',
                    text: '{{ session('success') }}',
                    icon: 'success',
                    confirmButtonText: 'OK'
                });
            </script>
        @endif
        @if (session('error'))
            <script>
                Swal.fire({
                    title: 'Kesalahan!',
                    text: '{{ session('error') }}',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            </script>
        @endif
        @if ($errors->any())
            <script>
                Swal.fire({
                    title: 'Kesalahan!',
                    text: '{{ $errors->first() }}',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            </script>
        @endif
    <div class="d-flex">
        <div class="sidebar bg-danger text-white p-3">
            <h2 class="text-center">Admin</h2>
            <ul class="list-unstyled">
                @php
                $role = $auth->role_id;
                $url = Request::segment(1);
                @endphp

                <li class="mb-3 {{ $url == 'dashboard' ? 'active' : '' }}">
                    <a href="{{ route('dashboard') }}" class="text-white">
                        <i class="me-3 fas fa-table-cells"></i> Dashboard
                    </a>
                </li>

                @if ($role == 1)
                <li class="mb-3 {{ $url == 'category' ? 'active' : '' }}">
                    <a href="{{ route('category') }}" class="text-white">
                        <i class="me-3 fas fa-list"></i> Kategori
                    </a>
                </li>
                @endif

                <li class="mb-3 {{ $url == 'product' ? 'active' : '' }}">
                     <a href="{{ route('product') }}" class="text-white">
                    <i class="me-3 fas fa-box"></i> Produk
                    </a> 
                </li>

                <li class="mb-3 {{ $url == 'profile' ? 'active' : '' }}">
                    <a href="{{ route('profile') }}" class="text-white">
                        <i class="me-3 fas fa-user-gear"></i> Profile
                    </a> 
                </li>

                @if ($role == 1)
                <li class="mb-3 {{ $url == 'user' ? 'active' : '' }}">
                     <a href="{{ route('user') }}" class="text-white">
                    <i class="me-3 fas fa-user-circle"></i> User
                    </a>
                </li>

                <li class="mb-3 {{ $url == 'role' ? 'active' : '' }}">
                     <a href="{{ route('role') }}" class="text-white">
                    <i class="me-3 fas fa-universal-access"></i> Role
                    </a> 
                </li>
                @endif

                <li class="mb-3">
                    <a href="#" id="logout" class="text-white">
                        <i class="me-3 fas fa-sign-out-alt"></i> Logout
                    </a>
                </li>
            </ul>
        </div>


        @yield('content')
        
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/2.1.8/js/dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/2.1.8/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $('#logout').click(function() {
                Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Anda akan keluar dari aplikasi!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Logout!',
                cancelButtonText: 'Cancel',
                buttons: true,
                dangerMode: true,
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "{{ route('logout') }}";
                }
            });
        });
    </script>
    @yield('script')
</body>

</html>