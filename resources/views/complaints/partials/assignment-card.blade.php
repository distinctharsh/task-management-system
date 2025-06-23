<div class="card shadow-sm mb-4">
  <div class="card-header bg-light">
    <h5 class="mb-0">Assigned To</h5>
  </div>
  <div class="card-body">
    @if($complaint->assignedTo)
    <div class="d-flex align-items-center">
      <div class="avatar avatar-lg bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 56px; height: 56px; font-size: 2rem;">
        {{ substr($complaint->assignedTo->full_name, 0, 1) }}
      </div>
      <div class="ms-3">
        <h6 class="mb-1">{{ $complaint->assignedTo->full_name }}</h6>
        <p class="text-muted mb-0">{{ ucfirst($complaint->assignedTo->role->name) }}</p>
      </div>
    </div>
    @else
    <p class="text-muted mb-0">Not assigned yet</p>
    @endif
  </div>
</div>