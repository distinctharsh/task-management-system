@php
    $isLoggedIn = auth()->check();
    $homeLabel = $isLoggedIn ? 'Dashboard' : 'Home';
    $homeUrl = $isLoggedIn ? route('dashboard') : route('home');
@endphp
<nav aria-label="breadcrumb" class="mb-3">
  <ol class="breadcrumb stylish-breadcrumb">
    <li class="breadcrumb-item">
      <a href="{{ $homeUrl }}">
        <i class="bi bi-house-door-fill"></i>
        {{ $homeLabel }}
      </a>
    </li>
    @foreach(array_slice($breadcrumbs, 1) as $index => $crumb)
      @php $label = $crumb['label'] === 'Complaints' ? 'Tickets' : $crumb['label']; @endphp
      @if($loop->last)
        <li class="breadcrumb-item active stylish-active" aria-current="page">{{ $label }}</li>
      @else
        <li class="breadcrumb-item">
          <a href="{{ $crumb['url'] }}">{{ $label }}</a>
        </li>
      @endif
    @endforeach
  </ol>
</nav>
<style>
.stylish-breadcrumb {
  background: #f8f9fa;
  border-radius: 0.5rem;
  box-shadow: 0 2px 8px rgba(13,110,253,0.07);
  padding: 0.75rem 1.25rem;
  font-size: 1.05rem;
}
.stylish-breadcrumb .breadcrumb-item + .breadcrumb-item::before {
  content: '›';
  color: #0d6efd;
  font-weight: bold;
  margin: 0 0.5rem;
}
.stylish-breadcrumb .breadcrumb-item a {
  color: #0d6efd;
  text-decoration: none;
  transition: color 0.2s;
}
.stylish-breadcrumb .breadcrumb-item a:hover {
  color: #084298;
  text-decoration: underline;
}
.stylish-breadcrumb .stylish-active {
  color: #212529;
  font-weight: 600;
  background: #e7f1ff;
  border-radius: 0.3rem;
  padding: 0.1rem 0.5rem;
}
</style> 