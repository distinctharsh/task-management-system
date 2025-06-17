<div class="modal fade" id="revertModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="{{ route('complaints.revert', $complaint) }}" method="POST">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title">Revert to Manager</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label for="description" class="form-label">Reason for Reverting</label>
            <textarea class="form-control" name="description" rows="3" required></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-warning">Revert</button>
        </div>
      </form>
    </div>
  </div>
</div>