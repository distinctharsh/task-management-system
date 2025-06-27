<div class="card shadow-sm mb-4">
  <div class="card-header bg-light">
    <h5 class="mb-0">Status History</h5>
  </div>
  <div class="card-body">
    <ul class="timeline list-unstyled">
      @foreach($complaint->actions as $action)
      @php
        $assignedUser = $action->assigned_to ? \App\Models\User::find($action->assigned_to) : null;
      @endphp
      <li class="mb-4 position-relative ps-4">
        @php
          // Circle color logic
          $circleColor = 'primary';
          if ($action->action === 'resolved') {
              $circleColor = 'success';
          } elseif ($action->action === 'reverted') {
              $circleColor = 'warning';
          } elseif ($action->action === 'pending_with_user' || $action->action === 'pending_with_vendor') {
              $circleColor = 'danger'; // red
          }
          // Status box color logic
          $statusBoxClass = '';
          if ($action->action === 'pending_with_user' || $action->action === 'pending_with_vendor') {
              $statusBoxClass = 'alert alert-danger mb-1 py-1 px-2 d-inline-block';
          }
        @endphp
        <span class="position-absolute top-0 start-0 translate-middle p-2 bg-{{ $circleColor }} border border-light rounded-circle" style="margin-top: 11px;"></span>
        <div class="ms-3">
          @if(in_array($action->action, ['assigned', 'reassigned']) && $action->assigned_to)
            {{-- Status Name and Assigned To --}}
            <div class="fw-semibold mb-1">
              {{ ucfirst($action->action) }}
              <span class="text-muted">to</span>
              <span class="text-primary">{{ $assignedUser ? $assignedUser->full_name : 'Unknown User' }}</span>
              @if($assignedUser && $assignedUser->role)
                <span class="text-muted">({{ ucfirst($assignedUser->role->name) }})</span>
              @endif
            </div>
            {{-- Description/Message --}}
            @if($action->description)
              <div class="mb-1">{{ $action->description }}</div>
            @endif
            {{-- Who assigned and when --}}
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
          @else
            {{-- Status Name/Box --}}
            @if($statusBoxClass)
              <span class="{{ $statusBoxClass }}">{{ ucfirst($action->action) }}</span>
            @else
              <h6 class="mb-1">{{ ucfirst($action->action) }}</h6>
            @endif
            {{-- Description (if any) --}}
            @if($action->description)
              <div class="mb-1">{{ $action->description }}</div>
            @endif
            {{-- Assigned User (for assigned/reassigned) --}}
            @if(in_array($action->action, ['assigned', 'reassigned']) && $action->assigned_to)
              <div class="text-muted small mb-1">
                <i class="bi bi-person-plus"></i> Assigned To:
                {{ $assignedUser ? $assignedUser->full_name : 'Unknown User' }}
                @if($assignedUser && $assignedUser->role)
                  ({{ ucfirst($assignedUser->role->name) }})
                @endif
              </div>
            @endif
            {{-- User and Time --}}
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
            {{-- Resolution (for resolved) --}}
            @if($action->action === 'resolved' && $action->resolution)
              <div class="mt-2">
                <strong>Resolution:</strong>
                <div class="alert alert-success mb-0">{{ $action->resolution }}</div>
              </div>
            @endif
          @endif
        </div>
      </li>
      @endforeach
    </ul>
  </div>
</div>