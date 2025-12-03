<!-- Include Bootstrap CSS & JS (if not already in your layout) -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>



<style>
  /* Center card container */
  .centered-container {
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    background-color: #f8f9fa;
    padding: 15px;
  }
</style>

<!-- Centered card container and form -->
<div class="centered-container">
  <div class="card shadow-sm" style="max-width: 420px; width: 100%;">
    <div class="card-body p-4">
      <h3 class="card-title mb-4 text-center">Reset Password</h3>

      <form action="{{ route('customer.password.email') }}" method="POST" novalidate>
        @csrf
        <div class="mb-3">
          <label for="email" class="form-label">Email Address</label>
          <input
            type="email"
            id="email"
            name="email"
            placeholder="Enter your email"
            required
            class="form-control"
            autocomplete="email"
            autofocus
          >
        </div>
        <button type="submit" class="btn btn-primary w-100">
          Send Password Reset Link
        </button>
      </form>
    </div>
  </div>
</div>

<!-- Modal for messages -->
<div class="modal fade" id="messageModal" tabindex="-1" aria-labelledby="messageModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">

      @if (session('status'))
        <div class="modal-header bg-success text-white">
          <h5 class="modal-title" id="messageModalLabel">Success</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <p>Successfully sent.</p> <!-- Fixed success message -->
        </div>
      @elseif ($errors->any())
        <div class="modal-header bg-danger text-white">
          <h5 class="modal-title" id="messageModalLabel">Error</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body" style="max-height: 300px; overflow-y: auto;">
          <ul class="mb-0">
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>

    </div>
  </div>
</div>

<!-- Trigger Modal on page load if message or errors exist -->
@if(session('status') || $errors->any())
<script>
  document.addEventListener('DOMContentLoaded', function () {
    var myModal = new bootstrap.Modal(document.getElementById('messageModal'));
    myModal.show();
  });
</script>
@endif
