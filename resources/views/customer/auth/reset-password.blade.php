<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Reset Password</title>
  <!-- Bootstrap CSS CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body class="bg-light">

<div class="d-flex justify-content-center align-items-center vh-100">
  <div class="card shadow-sm" style="min-width: 350px; max-width: 400px; width: 100%;">
    <div class="card-body">
      <h4 class="card-title mb-4 text-center">Reset Password</h4>

      <form action="{{ route('customer.password.update') }}" method="POST" novalidate>
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <div class="mb-3">
          <label for="email" class="form-label">Email address</label>
          <input
            type="email"
            id="email"
            name="email"
            value="{{ old('email', $email ?? '') }}"
            class="form-control @error('email') is-invalid @enderror"
            placeholder="Enter your email"
            required
            autofocus
          >
          @error('email')
            <div class="invalid-feedback">
              {{ $message }}
            </div>
          @enderror
        </div>

        <div class="mb-3">
          <label for="password" class="form-label">New Password</label>
          <input
            type="password"
            id="password"
            name="password"
            class="form-control @error('password') is-invalid @enderror"
            placeholder="New password"
            required
          >
          @error('password')
            <div class="invalid-feedback">
              {{ $message }}
            </div>
          @enderror
        </div>

        <div class="mb-4">
          <label for="password_confirmation" class="form-label">Confirm Password</label>
          <input
            type="password"
            id="password_confirmation"
            name="password_confirmation"
            class="form-control"
            placeholder="Confirm password"
            required
          >
        </div>

        <button type="submit" class="btn btn-primary w-100">Reset Password</button>
      </form>
    </div>
  </div>
</div>

<!-- Bootstrap JS Bundle (optional for interactive components) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
