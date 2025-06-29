<div class="modal fade" id="revertModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="{{ route('complaints.revert', $complaint) }}" method="POST" class="revert-form">
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
          <button type="submit" class="btn btn-warning revert-submit-btn">
            <span class="btn-text">Revert</span>
            <span class="btn-loading d-none">
                <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                Reverting...
            </span>
          </button>
        </div>
      </form>
    </div>
  </div>
</div>