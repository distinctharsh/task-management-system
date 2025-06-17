<div class="modal fade" id="resolveModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="{{ route('complaints.resolve', $complaint) }}" method="POST">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title">Resolve Ticket</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label for="resolution" class="form-label">Resolution</label>
            <textarea class="form-control" name="resolution" rows="3" required></textarea>
          </div>
          <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea class="form-control" name="description" rows="3" required></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-success">Resolve</button>
        </div>
      </form>
    </div>
  </div>
</div>