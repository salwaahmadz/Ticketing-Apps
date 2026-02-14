<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticketing App | @yield('title', 'CMS')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa;
            /* Abu-abu sangat muda untuk background utama */
        }

        /* Opsional: Membuat navbar sedikit lebih tinggi agar lega */
        .navbar {
            padding-top: 0.75rem;
            padding-bottom: 0.75rem;
        }
    </style>

    @stack('styles')
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm mb-4">
        <div class="container">
            <a class="navbar-brand fw-bold text-primary" href="{{ route('dashboard.index') }}">Ticketing App</a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('dashboard.index') }}">Dashboard</a>
                    </li>
                    @if (
                            (auth()->user()->roles->isNotEmpty() && auth()->user()->roles[0]->name == 'Admin') ||
                            auth()->user()->canany([
                                'users-read',
                                'roles-read',
                                'tickets-read',
                            ])
                        )
                        @can('tickets-read')
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('tickets.index') }}">
                                    @if (auth()->user()->roles->isNotEmpty() && auth()->user()->roles[0]->name == 'Admin')
                                        Tickets
                                    @else
                                        MyTickets
                                    @endif
                                </a>
                            </li>
                        @endcan

                        @can('users-read')
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('users.index') }}">Users</a>
                            </li>
                        @endcan

                        @can('roles-read')
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('roles.index') }}">Roles</a>
                            </li>
                        @endcan
                    @endif
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('users.profile', Auth::user()->uuid) }}">Profile</a>
                    </li>
                </ul>

                <div class="d-flex align-items-center">
                    <form action="{{ route('logout') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger btn-sm"
                            onclick="return confirm('Are you sure you want to logout?')">
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <main>
        <div class="container">
            @yield('contents')
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous">
        </script>

    @stack('scripts')
</body>

</html>