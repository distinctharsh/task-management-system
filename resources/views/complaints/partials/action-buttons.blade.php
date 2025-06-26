@if(auth()->user()->isManager() || auth()->user()->isVM())
@if($complaint->isUnassigned())
<button type="button" class="btn btn-primary me-2 mb-2" data-bs-toggle="modal" data-bs-target="#assignModal">
  <i class="bi bi-person-plus"></i> Assign
</button>
@endif
@endif
@if(auth()->user()->isVM() && $complaint->assigned_to === auth()->id())
<button type="button" class="btn btn-warning me-2 mb-2" data-bs-toggle="modal" data-bs-target="#revertModal">
  <i class="bi bi-arrow-counterclockwise"></i> Revert to Manager
</button>
@endif
@if(auth()->user()->isNFO() && $complaint->assigned_to === auth()->id())
<button type="button" class="btn btn-success me-2 mb-2" data-bs-toggle="modal" data-bs-target="#resolveModal">
  <i class="bi bi-check-circle"></i> Resolve
</button>
<button type="button" class="btn btn-primary me-2 mb-2" data-bs-toggle="modal" data-bs-target="#assignModal">
  <i class="bi bi-person-plus"></i> Reassign
</button>
@endif