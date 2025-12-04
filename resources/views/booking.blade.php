<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Ferry Booking</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body class="bg-light">

<!-- ================= NAVBAR ================= -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold" href="#">
            <i class="bi bi-ship"></i> Ferry Booking
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">

                <li class="nav-item">
                    <a class="nav-link active" href="/booking">
                        <i class="bi bi-ticket-perforated"></i> Book Ferry
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="/booking/history">
                        <i class="bi bi-clock-history"></i> Booking History
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="/">
                        <i class="bi bi-house"></i> Home
                    </a>
                </li>

            </ul>
        </div>
    </div>
</nav>
<!-- =============== END NAVBAR ================= -->

<!-- ================= MAIN CONTENT ================= -->
<section id="booking-form" class="py-5 bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-lg p-5 border-0 rounded-4">
                    <h3 class="text-center mb-4 text-primary fw-bold">
                        <i class="bi bi-ship"></i> Online Ferry Booking
                    </h3>

                    @if(session('success'))
                        <div class="alert alert-success text-center fw-bold">{{ session('success') }}</div>
                    @endif

                    <form action="{{ route('booking.submit') }}" method="POST" id="ferryBookingForm">
                        @csrf
                        <div class="row g-4">

                            <!-- From -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-secondary">From</label>
                                <select name="from_branch" id="fromBranch" class="form-select" required>
                                    <option value="">-- Select Departure --</option>
                                    @foreach($branches as $branch)
                                        <option value="{{ $branch->id }}">{{ $branch->branch_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- To -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-secondary">To</label>
                                <select name="to_branch" id="toBranch" class="form-select" required>
                                    <option value="">-- Select Destination --</option>
                                </select>
                            </div>

                            <!-- Items -->
                            <div class="col-md-12">
                                <label class="form-label fw-bold text-secondary">Items Description</label>
                                <input type="text" name="items" class="form-control" placeholder="e.g. Car, Bikes, Goods" required>
                            </div>

                            <!-- Date -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-secondary">Travel Date</label>
                                <input type="date" name="date" class="form-control" required>
                            </div>

                        </div>

                        <div class="text-center mt-5">
                            <button type="submit" class="btn btn-primary btn-lg px-5 py-2 rounded-pill shadow">
                                <i class="bi bi-send"></i> Submit Booking
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</section>

<!-- JS for dynamic dropdown -->
<script>
document.getElementById('fromBranch').addEventListener('change', function() {
    const branchId = this.value;
    const toBranchSelect = document.getElementById('toBranch');

    toBranchSelect.innerHTML = '<option value="">Loading...</option>';

    fetch(`/booking/to-branches/${branchId}`)
        .then(response => response.json())
        .then(data => {
            toBranchSelect.innerHTML = '';
            data.forEach(branch => {
                const option = document.createElement('option');
                option.value = branch.id;
                option.textContent = branch.branch_name;
                toBranchSelect.appendChild(option);
            });
        });
});
</script>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
