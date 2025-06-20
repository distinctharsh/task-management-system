<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm fixed-top">
  <div class="container">
    <div class="d-flex align-items-center" style="height: 80px;">
      <img src="{{ asset('images/emblem-dark.png') }}" alt="Emblem" class="h-100" style="object-fit: contain;">
      <img src="{{ asset('images/nic-main.png') }}" alt="NIC Logo" class="h-100 ms-2" style="object-fit: contain;">
    </div>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        @auth
        <li class="nav-item mt-1">
          <a class="nav-link" href="{{ route('dashboard') }}">Dashboard</a>
        </li>
        <li class="nav-item mt-1">
          <a class="nav-link" href="{{ route('complaints.index') }}">Tickets</a>
        </li>
        <li class="nav-item mt-1">
          <a class="nav-link" href="{{ route('complaints.history') }}">History</a>
        </li>
        @if(auth()->user()->isAdmin())
        <li class="nav-item ">
          <a class="nav-link" href="{{ route('users.index') }}">Users</a>
        </li>
        @endif
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
            <div class="rounded-circle bg-primary text-white d-flex justify-content-center align-items-center" style="width: 36px; height: 36px; font-size: 1rem;">
              {{ strtoupper(substr(auth()->user()->full_name, 0, 1)) }}
            </div>
          </a>
          <ul class="dropdown-menu dropdown-menu-end">
            <li class="dropdown-header text-center">
              <strong>{{ auth()->user()->full_name }}</strong><br>
              <small class="text-muted">{{ ucfirst(auth()->user()->role) }}</small>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>
            <!-- <li><a class="dropdown-item">Change Password</a></li> -->
            <li>
              <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="dropdown-item text-danger">Logout</button>
              </form>
            </li>
          </ul>
        </li>

        @else
        <li class="nav-item">
          <a class="nav-link" href="{{ route('login') }}">Login</a>
        </li>
        @endauth
      </ul>
    </div>
  </div>
</nav>





<!-- 
<div class="bg-white shadow-sm py-2 border-bottom fixed-top">
    <div class="container d-flex align-items-center" style="height: 70px;">
        <img src="{{ asset('images/emblem-dark.png') }}" alt="Emblem" class="h-100" style="object-fit: contain;">
        <img src="{{ asset('images/nic-main.png') }}" alt="NIC Logo" class="h-100 ms-2" style="object-fit: contain;">
    </div>
</div>


<nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm" style="margin-top: 70px;">
    <div class="container">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                @auth
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('dashboard') }}">Dashboard</a>
                </li>
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
                <li class="nav-item">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn btn-link nav-link">Logout</button>
                    </form>
                </li>
                @else
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('login') }}">Login</a>
                </li>
                @endauth
            </ul>
        </div>
    </div>
</nav> -->