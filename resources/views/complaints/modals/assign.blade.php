<div class="modal fade" id="assignModal{{ $complaint->id }}" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="{{ route('complaints.assign', $complaint) }}" method="POST">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title">Assign Ticket</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label for="assigned_to{{ $complaint->id }}" class="form-label">Assign To</label>
            <select class="form-select" name="assigned_to" id="assigned_to{{ $complaint->id }}" required>
              <option value="">Select User</option>
            </select>
          </div>
          <div class="mb-3">
            <label for="description" class="form-label">Remarks</label>
            <textarea class="form-control" name="description" rows="3" required></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Assign</button>
        </div>
      </form>
    </div>
  </div>
</div>