<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Ferry Booking</title>
    {{-- change for office pc --}}
    <!-- SELECT2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<!-- SELECT2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
</head>
<style>
    /* Attractive Select2 Style */
    .select2-container .select2-selection--single {
        height: 45px !important;
        padding: 8px 12px;
        border-radius: 8px;
        border: 1px solid #ced4da;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 45px !important;
    }

    .select2-selection__rendered {
        line-height: 30px !important;
        font-size: 14px;
    }
</style>
<body class="bg-light">

<!-- ================= NAVBAR =================changes -->
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

                   <li class="nav-item">
    <form action="{{ route('customer.logout') }}" method="POST" class="d-inline">
        @csrf
        <button type="submit" class="btn nav-link text-white border-0 bg-transparent">
            <i class="bi bi-box-arrow-right"></i> Logout
        </button>
    </form>
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
            <div class="col-lg-12">

                <div class="card shadow-lg border-0 rounded-4 p-4 p-md-5">

                    <!-- Title -->
                    <h3 class="text-center mb-4 text-primary fw-bold">
                        <i class="bi bi-ship"></i> Online Ferry Booking
                    </h3>

                    <!-- Success Message -->
                    @if(session('success'))
                        <div class="alert alert-success text-center fw-bold">
                            {{ session('success') }}
                        </div>
                    @endif

                    <!-- Booking Form -->
               <form action="{{ route('booking.submit') }}" method="POST" id="ferryBookingForm">
@csrf

<div class="row g-4">

    <!-- From Branch -->
    <div class="col-md-6">
        <label class="form-label fw-bold text-secondary">From</label>
        <select name="from_branch" id="fromBranch" class="form-select" required>
            <option value="">-- Select Departure --</option>
            @foreach($branches as $branch)
                <option value="{{ $branch->id }}">{{ $branch->branch_name }}</option>
            @endforeach
        </select>
    </div>

    <!-- To Branch -->
    <div class="col-md-6">
        <label class="form-label fw-bold text-secondary">To</label>
        <select name="to_branch" id="toBranch" class="form-select" required>
            <option value="">-- Select Destination --</option>
        </select>
    </div>

    <!-- Travel Date -->
    <div class="col-md-6">
        <label class="form-label fw-bold text-secondary">Travel Date</label>
        <input type="text" disabled name="date" id="travelDate" class="form-control" required>
    </div>

</div>

<hr class="my-4">

<!-- ===================== ITEMS CARD ===================== -->
<div class="card shadow-sm border-0 rounded-4 p-4 mb-4">
    <h5 class="fw-bold text-primary mb-3">Items</h5>

    <div id="itemsContainer"></div>

    <button type="button" class="btn btn-success rounded-pill mt-3" onclick="addItemRow()">
        <i class="bi bi-plus-circle"></i> Add More Item
    </button>

</div>

<!-- GRAND TOTAL -->
<div class="text-end mb-4">
    <h4 class="fw-bold text-primary">Grand Total: ₹ <span id="grandTotal">0</span></h4>
</div>

<!-- Submit Button -->
<div class="text-center mt-4">
   <button type="button" class="btn btn-primary btn-lg px-5 py-2 rounded-pill shadow"
        onclick="showPreviewModal()">
    <i class="bi bi-send"></i> Submit Booking
</button>

</div>

</form>
<!-- ========================= PREVIEW MODAL ========================= -->
<div class="modal fade" id="previewModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content rounded-4 shadow-lg">

            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-eye"></i> Review Your Booking
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body p-4">
                
                <h5 class="fw-bold text-secondary">Ferry Route</h5>
                <p id="previewRoute" class="mb-3"></p>

                <h5 class="fw-bold text-secondary">Travel Date</h5>
                <p id="previewDate" class="mb-3"></p>

                <h5 class="fw-bold text-secondary">Items</h5>
                <table class="table table-bordered table-striped">
                    <thead class="table-light">
                        <tr>
                            <th>Description</th>
                            <th>Qty</th>
                            <th>Rate</th>
                            <th>Lavy</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody id="previewItems"></tbody>
                </table>

                <h4 class="text-end text-primary fw-bold">
                    Grand Total: ₹ <span id="previewGrandTotal"></span>
                </h4>

            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Edit</button>

                <!-- PAYMENT BUTTON -->
                <button class="btn btn-success px-4" onclick="proceedToPayment()">
                    <i class="bi bi-wallet2"></i> Proceed to Pay
                </button>
            </div>

        </div>
    </div>
</div>



                    <!-- End Form -->

                </div>
            </div>
        </div>
    </div>
</section>
<!-- =============== END MAIN CONTENT ================= -->


<!-- JS for dynamic dropdown -->
<script>
/* ============================================================
   GLOBAL INITIALIZATION
============================================================ */
let rowIndex = 0;

/* Set today's date */
document.getElementById('travelDate').value =
    new Date().toISOString().split('T')[0];

/* When FROM branch changes → load TO branches + reset items */
document.getElementById('fromBranch').addEventListener('change', function() {
    const branchId = this.value;

    loadToBranches(branchId);
    resetItemsAndAddFirstRow();
});

/* ============================================================
   LOAD DESTINATION BRANCHES
============================================================ */
function loadToBranches(branchId) {
    const toBranchSelect = document.getElementById('toBranch');
    toBranchSelect.innerHTML = '<option>Loading...</option>';

    fetch(`/booking/to-branches/${branchId}`)
        .then(response => response.json())
        .then(data => {
            toBranchSelect.innerHTML = '';
            data.forEach(branch => {
                toBranchSelect.innerHTML +=
                    `<option value="${branch.id}">${branch.branch_name}</option>`;
            });
        });
}

/* ============================================================
   LOAD ITEMS FOR A BRANCH
============================================================ */
function loadItemsIntoDropdown(index) {
    const dropdown = document.querySelector(
        `[name="items[${index}][item_rate_id]"]`
    );

    const branchId = document.getElementById('fromBranch').value;
    if (!branchId || !dropdown) return;

    dropdown.innerHTML = '<option>Loading...</option>';

    fetch(`/booking/items/${branchId}`)
        .then(res => res.json())
        .then(data => {
            dropdown.innerHTML = '<option value="">-- Select Item --</option>';
            data.forEach(item => {
                dropdown.innerHTML += `<option value="${item.id}">${item.item_name}</option>`;
            });
        });
}

/* ============================================================
   ITEM ROW HANDLING
============================================================ */

/* Reset all items + add one blank row */
function resetItemsAndAddFirstRow() {
    document.getElementById("itemsContainer").innerHTML = "";
    rowIndex = 0;
    addItemRow();
}

/* Add a new dynamic row */
function addItemRow() {
    rowIndex++;

    const html = `
    <div class="row g-3 align-items-end item-row mb-3" data-index="${rowIndex}">

        <div class="col-md-4">
            <label class="form-label text-secondary">Description</label>
            <select name="items[${rowIndex}][item_rate_id]"
                    class="form-select itemDescription" required>
                <option value="">-- Select Item --</option>
            </select>
        </div>

        <div class="col-md-2">
            <label class="form-label text-secondary">Qty</label>
            <input type="number" name="items[${rowIndex}][quantity]" 
                class="form-control qty" min="1" value="1" required>
        </div>

        <div class="col-md-2">
            <label class="form-label text-secondary">Rate</label>
            <input type="number" name="items[${rowIndex}][rate]" 
                class="form-control rate" readonly>
        </div>

        <div class="col-md-2">
            <label class="form-label text-secondary">Lavy</label>
            <input type="number" name="items[${rowIndex}][lavy]" 
                class="form-control lavy" readonly>
        </div>

        <div class="col-md-1">
            <label class="form-label text-secondary">Total</label>
            <input type="number" class="form-control itemTotal" readonly>
        </div>

        <div class="col-md-1 text-center">
            <button type="button" class="btn btn-danger btn-sm mt-4"
                    onclick="removeItemRow(this)">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

    </div>`;

    document.getElementById("itemsContainer")
        .insertAdjacentHTML("beforeend", html);

    loadItemsIntoDropdown(rowIndex);
}

/* Remove a row */
function removeItemRow(btn) {
    btn.closest(".item-row").remove();
    calculateGrandTotal();
}

/* ============================================================
   AUTO FILL RATE / LAVY / TOTAL
============================================================ */

document.addEventListener("change", function(e) {
    if (e.target.classList.contains("itemDescription")) {

        const row = e.target.closest(".item-row");

        fetch(`/booking/item-rate/${e.target.value}`)
            .then(res => res.json())
            .then(data => {
                row.querySelector(".rate").value = data.item_rate;
                row.querySelector(".lavy").value = data.item_lavy;

                calculateItemTotal(row);
            });
    }
});

/* When quantity changes → recalc total */
document.addEventListener("input", function(e) {
    if (e.target.classList.contains("qty")) {
        const row = e.target.closest(".item-row");
        calculateItemTotal(row);
    }
});

/* Calculate single row total */
function calculateItemTotal(row) {
    const qty = parseFloat(row.querySelector(".qty").value) || 0;
    const rate = parseFloat(row.querySelector(".rate").value) || 0;
    const lavy = parseFloat(row.querySelector(".lavy").value) || 0;

    const total = (qty * rate) + lavy;

    row.querySelector(".itemTotal").value = total;

    calculateGrandTotal();
}

/* Calculate grand total */
function calculateGrandTotal() {
    let sum = 0;

    document.querySelectorAll(".itemTotal").forEach(el => {
        sum += parseFloat(el.value) || 0;
    });

    document.getElementById("grandTotal").innerText = sum.toFixed(2);
}


function showPreviewModal() {

    const from = document.querySelector("#fromBranch option:checked").textContent;
    const to   = document.querySelector("#toBranch option:checked").textContent;
    const date = document.getElementById("travelDate").value;

    document.getElementById("previewRoute").innerHTML = `<b>${from}</b> → <b>${to}</b>`;
    document.getElementById("previewDate").innerText = date;

    let rows = "";
    document.querySelectorAll(".item-row").forEach(row => {
        rows += `
            <tr>
                <td>${row.querySelector(".itemDescription option:checked").textContent}</td>
                <td>${row.querySelector(".qty").value}</td>
                <td>${row.querySelector(".rate").value}</td>
                <td>${row.querySelector(".lavy").value}</td>
                <td>${row.querySelector(".itemTotal").value}</td>
            </tr>`;
    });

    document.getElementById("previewItems").innerHTML = rows;

    document.getElementById("previewGrandTotal").innerText =
        document.getElementById("grandTotal").innerText;

    new bootstrap.Modal(document.getElementById('previewModal')).show();
}


function proceedToPayment() {
    // Submit hidden form for payment
    document.getElementById("ferryBookingForm").submit();
}

</script>


<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
