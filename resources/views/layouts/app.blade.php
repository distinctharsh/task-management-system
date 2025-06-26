<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket Management System</title>
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">


    <!-- CSS -->
<link href="{{ asset('css/tom-select.bootstrap5.min.css') }}" rel="stylesheet">

<!-- JS -->
<script src="{{ asset('js/tom-select.complete.min.js') }}"></script>

<link rel="stylesheet" href="{{ asset('css/all.min.css') }}">




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


        #usersTable_filter{
            float: right;
        }
    </style>
</head>

<body>
    @include('layouts.navbar')


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

    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('js/jquery-3.6.0.min.js') }}"></script>
    @stack('scripts')
</body>

</html>