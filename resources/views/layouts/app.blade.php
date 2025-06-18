<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            padding-top: 100px;
        }


        .timeline {
            position: relative;
            padding-left: 1.5rem;
        }

        /* Main vertical line */
        .timeline::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #e9ecef;
            /* Solid line for the main timeline */
            margin-left: -1px;
        }

        /* Dotted connecting lines between circles */
        .timeline-item {
            position: relative;
            padding-bottom: 1.5rem;
        }

        .timeline-item:not(:last-child)::after {
            content: '';
            position: absolute;
            left: -1.5rem;
            top: 24px;
            /* Adjust based on your circle size */
            bottom: -1.5rem;
            width: 2px;
            background: linear-gradient(to bottom, #adb5bd 10%, transparent 0%);
            background-size: 2px 8px;
            /* Adjust dotted line pattern */
            background-repeat: repeat-y;
        }

        /* Circle styling (keep your existing classes) */
        .position-absolute.start-0.translate-middle {
            left: -1.5rem !important;
            z-index: 2;
            border: 2px solid white;
            /* Creates nice border effect */
            box-shadow: 0 0 0 2px #e9ecef;
            /* Matches timeline color */
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        <div class="container">
            <a class="navbar-brand" href="{{ route('dashboard') }}">TMS</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    @auth
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('complaints.index') }}">Tickets</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('complaints.history') }}">History</a>
                    </li>
                    @if(auth()->user()->isAdmin())
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('users.index') }}">Users</a>
                    </li>
                    @endif
                    @endauth
                </ul>
                <ul class="navbar-nav">
                    @guest
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">Login</a>
                    </li>
                    <!-- <li class="nav-item">
                            <a class="nav-link" href="{{ route('register') }}">Register</a>
                        </li> -->
                    @else
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            {{ auth()->user()->full_name }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="dropdown-item">Logout</button>
                                </form>
                            </li>
                        </ul>
                    </li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>

    <main class="container py-4">
        @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
        @endif

        @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
        @endif

        @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>

</html>