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
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <script src="{{ asset('js/main.js') }}"></script>


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
                            <div class="mt-3">
                                <button type="button" class="btn btn-outline-primary btn-sm"
                                    id="trackProgressBtn"
                                    data-dashboard-url="{{ route('dashboard') }}"
                                    data-history-url="{{ route('complaints.history') }}">
                                    Track Ticket
                                </button>
                            </div>
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

    <!-- Search Ticket Modal -->
    <div class="modal fade" id="searchTicketModal" tabindex="-1" aria-labelledby="searchTicketModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="searchTicketModalLabel">Search Ticket</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="searchTicketForm" autocomplete="off">
                        <div class="mb-3">
                            <label for="reference_number" class="form-label">Ticket Reference Number</label>
                            <input type="text" class="form-control" id="reference_number" name="reference_number" required>
                        </div>
                        <div id="searchError" class="alert alert-danger d-none"></div>
                        <button type="submit" class="btn btn-primary w-100">Search</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Ticket Details Modal -->
    <div class="modal fade" id="complaintDetailsModal" tabindex="-1" aria-labelledby="complaintDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="complaintDetailsModalLabel">Ticket Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="complaintDetailsBody">
                    <!-- Details will be loaded here -->
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script>
        window.ALLOWED_IPS = @json(config('app.allowed_ips', []));
        window.USER_IP = '{{ $user_ip ?? request()->ip() }}';
        // console.log('window.USER_IP:', window.USER_IP);
        // alert('window.USER_IP: ' + window.USER_IP);
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const trackProgressBtn = document.getElementById('trackProgressBtn');
            const searchTicketModalElement = document.getElementById('searchTicketModal');
            const complaintDetailsModalElement = document.getElementById('complaintDetailsModal');
            const complaintDetailsBody = document.getElementById('complaintDetailsBody');
            const searchTicketForm = document.getElementById('searchTicketForm');
            const searchError = document.getElementById('searchError');
            let searchTicketModal, complaintDetailsModal;
            if (searchTicketModalElement) {
                searchTicketModal = new bootstrap.Modal(searchTicketModalElement);

                // Always remove backdrop when searchTicketModal is closed
                searchTicketModalElement.addEventListener('hidden.bs.modal', function() {
                    // Remove any remaining modal backdrop
                    const backdrop = document.querySelector('.modal-backdrop');
                    if (backdrop) {
                        backdrop.remove();
                    }
                });
            }
            if (complaintDetailsModalElement) {
                complaintDetailsModal = new bootstrap.Modal(complaintDetailsModalElement);
            }
            trackProgressBtn.addEventListener('click', function() {
                // alert('Your IP address is: ' + window.USER_IP);
                if (window.ALLOWED_IPS.includes(window.USER_IP)) {
                    window.location.href = trackProgressBtn.dataset.historyUrl;
                } else {
                    if (searchTicketModal) {
                        searchTicketModal.show();
                    }
                }
            });
            if (searchTicketForm) {
                searchTicketForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    searchError.classList.add('d-none');
                    const refInput = document.getElementById('reference_number');
                    if (!refInput) {
                        searchError.textContent = 'Reference number input not found.';
                        searchError.classList.remove('d-none');
                        return;
                    }
                    const ref = refInput.value.trim();
                    if (!ref) {
                        searchError.textContent = 'Please enter a complaint reference number.';
                        searchError.classList.remove('d-none');
                        return;
                    }
                    fetch(`/api/complaints/lookup?reference_number=${encodeURIComponent(ref)}`)
                        .then(async res => {
                            let data;
                            try {
                                data = await res.json();
                            } catch {
                                data = {};
                            }
                            // Format created_at to 12-hour format with AM/PM
                            let createdAt = data.complaint.created_at || '';
                            if (createdAt) {
                                const d = new Date(createdAt.replace(/-/g, '/'));
                                if (!isNaN(d.getTime())) {
                                    const options = { year: 'numeric', month: 'short', day: '2-digit', hour: '2-digit', minute: '2-digit', hour12: true };
                                    createdAt = d.toLocaleString('en-US', options);
                                }
                            }
                            if (res.ok && data.success) {
                                let html = `
                                <div class=\"lookup-modal-unique-v2\">
                                    <div class=\"lookup-ribbon mb-4\">
                                        <span><i class=\"bi bi-ticket-detailed me-2\"></i>Reference: <b>${data.complaint.reference_number}</b></span>
                                    </div>
                                    <div class=\"lookup-infobar d-flex justify-content-between align-items-center mb-4 p-3 rounded shadow-sm\">
                                        <div class=\"d-flex align-items-center gap-2\">
                                            <i class=\"bi bi-info-circle-fill text-primary fs-5\"></i>
                                            <span class=\"fw-bold\">Status:</span>
                                            <span class=\"badge rounded-pill px-3 py-2\" style=\"background:#e3f0ff; color:#0d6efd; font-weight:600;\">${data.complaint.status}</span>
                                        </div>
                                        <div class=\"d-flex align-items-center gap-2\">
                                            <i class=\"bi bi-lightning-charge-fill text-warning fs-5\"></i>
                                            <span class=\"fw-bold\">Priority:</span>
                                            <span class=\"badge rounded-pill px-3 py-2\" style=\"background:#fff8e1; color:#ff9800; font-weight:600;\">${data.complaint.priority}</span>
                                        </div>
                                    </div>
                                    <div class=\"row g-4 mb-3\">
                                        <div class=\"col-12 col-md-6\">
                                            <div class=\"section-heading mb-2\"><i class=\"bi bi-person-lines-fill me-1\"></i>User Info</div>
                                            <div class=\"card border-0 shadow-sm h-100\">
                                                <div class=\"card-body p-3\">
                                                    <div class=\"mb-2\"><span class=\"fw-semibold\"><i class=\"bi bi-person-circle me-2\"></i>Name:</span> <span class=\"text-dark\">${data.complaint.name || 'N/A'}</span></div>
                                                    <div class=\"mb-2\"><span class=\"fw-semibold\"><i class=\"bi bi-telephone me-2\"></i>Intercom:</span> <span class=\"text-dark\">${data.complaint.intercom || 'N/A'}</span></div>
                                                    <div class=\"mb-2\"><span class=\"fw-semibold\"><i class=\"bi bi-calendar-event me-2\"></i>Created:</span> <span class=\"text-dark\">${createdAt}</span></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class=\"col-12 col-md-6\">
                                            <div class=\"section-heading mb-2\"><i class=\"bi bi-clipboard-data me-1\"></i>Ticket Info</div>
                                            <div class=\"card border-0 shadow-sm h-100\">
                                                <div class=\"card-body p-3\">
                                                    <div class=\"mb-2\"><span class=\"fw-semibold\"><i class=\"bi bi-hdd-network me-2\"></i>Issue:</span> <span class=\"text-dark\">${data.complaint.network || 'N/A'}</span></div>
                                                    <div class=\"mb-2\"><span class=\"fw-semibold\"><i class=\"bi bi-geo-alt me-2\"></i>Section:</span> <span class=\"text-dark\">${data.complaint.section || 'N/A'}</span></div>
                                                    <div class=\"mb-2\"><span class=\"fw-semibold\"><i class=\"bi bi-layers me-2\"></i>Vertical:</span> <span class=\"text-dark\">${data.complaint.vertical || 'N/A'}</span></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class=\"section-heading mt-4 mb-2\"><i class=\"bi bi-card-text me-1\"></i>Description</div>
                                    <div class=\"card border-0 shadow-sm mb-2\">
                                        <div class=\"card-body p-3\">
                                            <div class=\"ps-2 text-muted\">${data.complaint.description || ''}</div>
                                        </div>
                                    </div>
                                </div>
                                <style>
                                    .lookup-modal-unique-v2 { max-width: 900px; margin: 0 auto; }
                                    .lookup-ribbon {
                                        background: linear-gradient(90deg,#0d6efd,#0a58ca);
                                        color: #fff;
                                        font-size: 1.1rem;
                                        font-weight: 600;
                                        border-radius: 0 0 16px 16px;
                                        padding: 0.75rem 1.5rem;
                                        box-shadow: 0 2px 8px rgba(13,110,253,0.10);
                                        text-align: left;
                                    }
                                    .lookup-infobar {
                                        background: #f8fafc;
                                        border: 1px solid #e3e8ef;
                                    }
                                    .section-heading { position: relative; z-index: 2; background: #fff; display: inline-block; padding-right: 8px; font-size: 1rem; font-weight: 700; color: #0d6efd; letter-spacing: 0.5px; }
                                    .lookup-modal-unique-v2 .card { transition: box-shadow 0.2s; border-radius: 12px; }
                                    .lookup-modal-unique-v2 .card:hover { box-shadow: 0 0.5rem 1.5rem rgba(13,110,253,0.10); }
                                    @media (max-width: 1100px) {
                                        .lookup-modal-unique-v2 { max-width: 99vw; }
                                    }
                                </style>
                                `;
                                complaintDetailsBody.innerHTML = html;
                                // Hide search modal and cleanup
                                const searchModal = bootstrap.Modal.getInstance(document.getElementById('searchTicketModal'));
                                if (searchModal) {
                                    searchModal.hide();
                                    // Wait for modal to be hidden
                                    searchModal._element.addEventListener('hidden.bs.modal', function() {
                                        // Remove backdrop
                                        const backdrop = document.querySelector('.modal-backdrop');
                                        if (backdrop) {
                                            backdrop.remove();
                                        }
                                        // Show complaint details modal
                                        complaintDetailsModal.show();
                                    });
                                }
                            } else {
                                searchError.textContent = (data && data.error) ? data.error : 'Ticket not found.';
                                searchError.classList.remove('d-none');
                            }
                        })
                        .catch(() => {
                            searchError.textContent = 'Ticket not found.';
                            searchError.classList.remove('d-none');
                        });
                });
            }
        });
    </script>
    @if($errors->has('username') || $errors->has('password'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var loginModal = new bootstrap.Modal(document.getElementById('loginModal'));
            loginModal.show();
        });
    </script>
    @endif
</body>

</html>