<div class="card shadow-sm mb-4">
  <div class="card-header bg-light">
    <h5 class="mb-0">Status History</h5>
  </div>
  <div class="card-body">
    <ul class="timeline list-unstyled">
      @foreach($complaint->actions as $action)
      <li class="mb-4 position-relative ps-4">
        <span class="position-absolute top-0 start-0 translate-middle p-2 bg-{{ $action->action === 'resolved' ? 'success' : ($action->action === 'reverted' ? 'warning' : 'primary') }} border border-light rounded-circle" style="margin-top: 11px;"></span>
        <div class="ms-3">
          <h6 class="mb-1">{{ ucfirst($action->action) }}</h6>
          <div class="text-muted small mb-1">
            <i class="bi bi-person"></i>
            @if ($action->user && $action->user_id != 0)
            {{ $action->user->full_name }}
            @else
            Guest User
            @endif
            &nbsp;|&nbsp;
            <i class="bi bi-clock"></i> {{ $action->created_at->format('M d, Y h:i A') }}
          </div>
          <div>{{ $action->description }}</div>
          @if($action->action === 'resolved' && $action->resolution)
          <div class="mt-2">
            <strong>Resolution:</strong>
            <div class="alert alert-success mb-0">{{ $action->resolution }}</div>
          </div>
          @endif
        </div>
      </li>
      @endforeach
    </ul>
  </div>
</div>