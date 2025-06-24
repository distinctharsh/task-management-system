<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'TMS') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Styles -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])


    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa;
        }

        .hero-section {
            background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
            color: white;
            padding: 4rem 0;
        }

        .feature-card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            transition: transform 0.3s ease;
        }

        .feature-card:hover {
            transform: translateY(-5px);
        }

        .btn-primary {
            background-color: #0d6efd;
            border-color: #0d6efd;
            padding: 0.5rem 1.5rem;
        }

        .btn-primary:hover {
            background-color: #0b5ed7;
            border-color: #0a58ca;
        }

        .btn-outline-light {
            border-width: 2px;
        }

        body {
            padding-top: 100px;
        }
    </style>
</head>

<body>
    <!-- Navigation -->
    @include('layouts.navbar')


    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container text-center">
            <h1 class="display-4 mb-4">Welcome to Ticket Management System</h1>
            <img src="{{ asset('images/flow-diagram.png') }}" alt="Work Flow" class="img-fluid mb-4 d-block mx-auto" style="max-height: 300px;">

            <p class="lead mb-4">Generate your ticket and track their progress easily</p>
            <div class="d-flex justify-content-center gap-3">
                <a href="{{ route('complaints.create') }}" class="btn btn-light btn-lg">Create Ticket</a>
                @auth
<a href="{{ route('dashboard') }}" class="btn btn-outline-light btn-lg">Dashboard</a>
@endauth

                @guest
                <button type="button" class="btn btn-outline-light btn-lg" data-bs-toggle="modal" data-bs-target="#loginModal">
                    Login
                </button>
                @endguest
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-5">
        <div class="container">
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card feature-card h-100">
                        <div class="card-body text-center p-4">
                            <h3 class="h5 mb-3">Easy Submission</h3>
                            <p class="text-muted mb-0">Submit your tickets quickly and easily without any login required.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card feature-card h-100">
                        <div class="card-body text-center p-4">
                            <h3 class="h5 mb-3">Track Progress</h3>
                            <p class="text-muted mb-0">Monitor the status of your tickets in real-time.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card feature-card h-100">
                        <div class="card-body text-center p-4">
                            <h3 class="h5 mb-3">Quick Resolution</h3>
                            <p class="text-muted mb-0">Our team works efficiently to resolve your tickets.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-white py-4 mt-auto">



        <div class="container">
            <div class="row">
                <div class="col-6 d-flex">


                    <img src="{{ asset('images/nic.png') }}" alt="NIC">
                    <div class="div mt-1">
                        <p class="text-muted mb-0">&copy; {{ date('Y') }} {{ config('app.name', 'TMS') }}. All rights reserved.</p>

                    </div>

                </div>

                <div class="col-6 pt-1" style="text-align: right;">
                    <p class="text-muted mb-0">Last updated 16/06/2025 - 5:03:44</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Login Modal -->
    <div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="loginModalLabel">Login</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control @error('username') is-invalid @enderror" id="username" name="username" value="{{ old('username') }}" required>
                            @error('username')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required>
                            @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Login</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Close modal on successful login
        document.addEventListener('DOMContentLoaded', function() {
            const loginForm = document.querySelector('form[action="{{ route('login') }}"]');
            if (loginForm) {
                loginForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    // Get form data
                    const formData = new FormData(loginForm);
                    
                    // Send AJAX request
                    fetch(loginForm.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => {
                        // Handle redirects
                        if (response.redirected) {
                            // Close the modal
                            const modal = bootstrap.Modal.getInstance(document.getElementById('loginModal'));
                            if (modal) {
                                modal.hide();
                            }
                            // Redirect to dashboard
                            window.location.href = response.url;
                        } else if (response.ok) {
                            // Close the modal
                            const modal = bootstrap.Modal.getInstance(document.getElementById('loginModal'));
                            if (modal) {
                                modal.hide();
                            }
                            // Redirect to dashboard
                            window.location.href = '{{ route('dashboard') }}';
                        } else {
                            // Handle errors
                            return response.json();
                        }
                    })
                    .then(data => {
                        if (data && data.errors) {
                            // Show validation errors
                            const errorMessages = Object.values(data.errors).flat().join('\n');
                            alert(errorMessages);
                        } else if (data && data.message) {
                            alert(data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        if (error.message.includes('redirect')) {
                            // If error is due to redirect, don't show alert
                            return;
                        }
                        alert('An error occurred during login');
                    });
                });
            }
        });
    </script>
</body>

</html>