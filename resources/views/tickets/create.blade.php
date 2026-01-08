@extends('layouts.admin')
{{-- VIEW VERSION: v2.0-modern-2026-01-08 --}}

@section('title', 'Ticket Entry')
@section('page-title', 'Ticket Entry / Create New Booking')

@section('content')
<style>
/* Two Column Layout */
.main-grid {
    display: grid;
    grid-template-columns: 1fr 380px;
    gap: 1.5rem;
    margin-bottom: 1.5rem;
}

@media (max-width: 1200px) {
    .main-grid {
        grid-template-columns: 1fr;
    }
}

/* Card Styles */
.card {
    background: white;
    border-radius: 0.75rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.08);
    border: 1px solid #e2e8f0;
    overflow: hidden;
}

.card-header {
    padding: 1rem 1.25rem;
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.card-title {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-weight: 600;
    font-size: 1rem;
    color: #1e293b;
}

.card-title i {
    color: #6366f1;
    width: 1.25rem;
    height: 1.25rem;
}

.card-body {
    padding: 1.25rem;
}

/* Badge Styles */
.badge {
    display: inline-flex;
    align-items: center;
    padding: 0.25rem 0.75rem;
    border-radius: 9999px;
    font-size: 0.75rem;
    font-weight: 500;
}

.badge-primary {
    background: #eff6ff;
    color: #2563eb;
}

.badge-success {
    background: #dcfce7;
    color: #16a34a;
}

/* Form Controls */
.form-group {
    margin-bottom: 1rem;
}

.form-group:last-child {
    margin-bottom: 0;
}

.form-label {
    display: flex;
    align-items: center;
    gap: 0.375rem;
    font-size: 0.8125rem;
    font-weight: 500;
    color: #475569;
    margin-bottom: 0.375rem;
}

.form-label i {
    width: 0.875rem;
    height: 0.875rem;
    color: #94a3b8;
}

.form-control {
    width: 100%;
    padding: 0.625rem 0.875rem;
    border: 1px solid #e2e8f0;
    border-radius: 0.5rem;
    font-size: 0.875rem;
    background: white;
    transition: all 0.2s;
    outline: none;
}

.form-control:focus {
    border-color: #6366f1;
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
}

.form-control:read-only,
.form-control:disabled {
    background: #f8fafc;
    color: #64748b;
}

.form-control::placeholder {
    color: #94a3b8;
}

/* Trip Info Grid */
.trip-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}

@media (max-width: 640px) {
    .trip-grid {
        grid-template-columns: 1fr;
    }
}

/* Schedule Section */
.schedule-section {
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 1px solid #e2e8f0;
}

.schedule-row {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.schedule-row .form-control {
    flex: 1;
}

/* Note Box */
.note-box {
    background: #fefce8;
    border: 1px solid #fde047;
    border-radius: 0.5rem;
    padding: 0.75rem 1rem;
    margin-top: 1rem;
}

.note-title {
    font-weight: 600;
    color: #b45309;
    font-size: 0.8125rem;
}

.note-text {
    color: #92400e;
    font-size: 0.8125rem;
    margin-top: 0.125rem;
}

/* Line Items Card */
.line-items-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1rem 1.25rem;
    border-bottom: 1px solid #e2e8f0;
}

.btn-add-row {
    display: inline-flex;
    align-items: center;
    gap: 0.375rem;
    padding: 0.5rem 1rem;
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 0.5rem;
    font-size: 0.875rem;
    font-weight: 500;
    color: #374151;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-add-row:hover {
    background: #f8fafc;
    border-color: #cbd5e1;
}

/* Items Table */
.items-table-wrap {
    overflow-x: auto;
}

.items-table {
    width: 100%;
    border-collapse: collapse;
}

.items-table thead th {
    background: #f8fafc;
    padding: 0.75rem 0.625rem;
    font-size: 0.7rem;
    font-weight: 600;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    text-align: left;
    border-bottom: 1px solid #e2e8f0;
    white-space: nowrap;
}

.items-table tbody td {
    padding: 0.5rem 0.5rem;
    border-bottom: 1px solid #f1f5f9;
    vertical-align: middle;
}

.items-table tbody tr:hover {
    background: #f8fafc;
}

.items-table .form-control {
    padding: 0.5rem 0.625rem;
    font-size: 0.8125rem;
}

.num-input {
    text-align: right;
}

.row-number {
    color: #94a3b8;
    font-weight: 500;
    font-size: 0.8125rem;
    text-align: center;
}

.btn-remove {
    background: #fee2e2;
    color: #dc2626;
    border: none;
    width: 1.75rem;
    height: 1.75rem;
    border-radius: 0.375rem;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s;
    font-size: 0.875rem;
}

.btn-remove:hover {
    background: #fecaca;
}

/* Footer Section */
.footer-section {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 2rem;
    padding: 1.25rem;
    background: #f8fafc;
    border-top: 1px solid #e2e8f0;
}

@media (max-width: 900px) {
    .footer-section {
        flex-direction: column;
    }
}

.print-checkbox {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: #475569;
    font-size: 0.875rem;
    cursor: pointer;
}

.print-checkbox input[type="checkbox"] {
    width: 1.125rem;
    height: 1.125rem;
    accent-color: #6366f1;
}

/* Summary & Actions */
.summary-actions {
    display: flex;
    align-items: flex-start;
    gap: 2rem;
}

@media (max-width: 900px) {
    .summary-actions {
        flex-direction: column;
        width: 100%;
    }
}

.summary-box {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.summary-row {
    display: flex;
    align-items: center;
    gap: 1rem;
    justify-content: flex-end;
}

.summary-label {
    font-size: 0.8125rem;
    color: #64748b;
    min-width: 85px;
    text-align: right;
}

.summary-value {
    font-weight: 700;
    font-size: 1rem;
    color: #1e293b;
    min-width: 80px;
    text-align: right;
}

.summary-value.net-total {
    color: #16a34a;
    font-size: 1.125rem;
}

.summary-input {
    width: 80px;
    padding: 0.375rem 0.5rem;
    border: 1px solid #e2e8f0;
    border-radius: 0.375rem;
    font-size: 0.8125rem;
    text-align: right;
    background: white;
    outline: none;
}

.summary-input:focus {
    border-color: #6366f1;
    box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.1);
}

/* Action Buttons */
.action-buttons {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.btn-save {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    padding: 0.75rem 1.5rem;
    background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
    color: white;
    border: none;
    border-radius: 0.5rem;
    font-weight: 600;
    font-size: 0.9375rem;
    cursor: pointer;
    transition: all 0.2s;
    box-shadow: 0 4px 14px rgba(99, 102, 241, 0.3);
}

.btn-save:hover {
    transform: translateY(-1px);
    box-shadow: 0 6px 20px rgba(99, 102, 241, 0.4);
}

.btn-pay {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    padding: 0.75rem 1.5rem;
    background: white;
    color: #374151;
    border: 1px solid #e2e8f0;
    border-radius: 0.5rem;
    font-weight: 500;
    font-size: 0.9375rem;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-pay:hover {
    background: #f8fafc;
    border-color: #cbd5e1;
}

/* Modals */
.modal-overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    backdrop-filter: blur(4px);
    align-items: center;
    justify-content: center;
    z-index: 9999;
}

.modal-content {
    background: white;
    border-radius: 0.75rem;
    max-width: 420px;
    width: 90%;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    overflow: hidden;
}

.modal-header {
    background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
    color: white;
    padding: 1rem 1.25rem;
    font-weight: 600;
    font-size: 1rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.modal-close {
    background: rgba(255,255,255,0.2);
    border: none;
    color: white;
    width: 1.75rem;
    height: 1.75rem;
    border-radius: 0.375rem;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
}

.modal-body {
    padding: 1.25rem;
}

.modal-footer {
    padding: 1rem 1.25rem;
    background: #f8fafc;
    display: flex;
    justify-content: flex-end;
    gap: 0.75rem;
}

.btn-cancel {
    background: white;
    border: 1px solid #e2e8f0;
    padding: 0.625rem 1.25rem;
    border-radius: 0.5rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-cancel:hover {
    background: #f8fafc;
}

.btn-confirm {
    background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
    color: white;
    border: none;
    padding: 0.625rem 1.25rem;
    border-radius: 0.5rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-confirm:hover {
    opacity: 0.9;
}

/* Guest Modal */
.divider-text {
    text-align: center;
    color: #94a3b8;
    font-size: 0.875rem;
    margin: 0.75rem 0;
}

.guest-list {
    list-style: none;
    padding: 0;
    margin: 0.5rem 0 0;
    max-height: 150px;
    overflow-y: auto;
    border: 1px solid #e2e8f0;
    border-radius: 0.5rem;
    display: none;
}

.guest-list li {
    padding: 0.625rem 0.875rem;
    cursor: pointer;
    border-bottom: 1px solid #f1f5f9;
    transition: background 0.2s;
}

.guest-list li:hover {
    background: #f1f5f9;
}

.guest-list li:last-child {
    border-bottom: none;
}

/* Alert */
.alert-success {
    background: #dcfce7;
    border: 1px solid #86efac;
    color: #166534;
    padding: 1rem;
    border-radius: 0.5rem;
    margin-top: 1rem;
}
</style>

<form id="ticketForm" method="post" action="{{ route('ticket-entry.store') }}">
    @csrf
    <input type="hidden" name="guest_id" id="guest_id_hidden">
    <input type="hidden" name="payment_mode" id="payment_mode" value="">

    <!-- Main Two Column Grid -->
    <div class="main-grid">
        <!-- Left Column - Trip Information -->
        <div class="card">
            <div class="card-header">
                <div class="card-title">
                    <i data-lucide="ship"></i>
                    Trip Information
                </div>
                <span class="badge badge-primary">Active Schedule</span>
            </div>
            <div class="card-body">
                <div class="trip-grid">
                    <!-- Branch Name -->
                    <div class="form-group">
                        <label class="form-label">
                            <i data-lucide="map-pin"></i>
                            Branch Name
                        </label>
                        @if(in_array($user->role_id, [1,2]))
                            @php($selectedBranch = (string) old('branch_id', request('branch_id', '')))
                            <select name="branch_id" class="form-control" id="branchSelect">
                                <option value="" {{ $selectedBranch === '' ? 'selected' : '' }}>-- Select Branch --</option>
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}" {{ $selectedBranch === (string) $branch->id ? 'selected' : '' }}>
                                        {{ $branch->branch_name }}
                                    </option>
                                @endforeach
                            </select>
                        @else
                            <input class="form-control" value="{{ $branchName }}" readonly>
                            <input type="hidden" name="branch_id" id="branch_id" value="{{ $branchId }}">
                        @endif
                    </div>

                    <!-- Ferry Boat -->
                    <div class="form-group">
                        <label class="form-label">
                            <i data-lucide="anchor"></i>
                            Ferry Boat
                        </label>
                        <select name="ferry_boat_id" class="form-control" required id="ferryBoatSelect">
                            @foreach($ferryboatsBranch as $fb)
                                <option value="{{ $fb->id }}" @selected(old('ferry_boat_id')==$fb->id)>
                                    {{ $fb->name }} (Capacity: {{ $fb->pax_capacity ?? 'N/A' }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Schedule & Time -->
                <div class="schedule-section">
                    <div class="form-group">
                        <label class="form-label">
                            <i data-lucide="clock"></i>
                            Schedule & Time
                        </label>
                        <div class="schedule-row">
                            @if(in_array($user->role_id, [1,2]))
                                <select id="ferryTimeSelect" class="form-control">
                                    <option value="">-- Select Schedule --</option>
                                    @foreach($ferrySchedulesPerBranch[$branchId] ?? [] as $fs)
                                        <option value="{{ $fs['time'] }}">{{ $fs['time'] }}</option>
                                    @endforeach
                                </select>
                                <input type="hidden" name="ferry_time" id="ferryTimeInput" value="">
                                <input type="hidden" name="ferry_type" value="REGULAR">
                            @else
                                @if(!$hideFerryTime)
                                    @php($ferryIso = old('ferry_time', $nextFerryTime))
                                    <select class="form-control" disabled>
                                        <option>{{ \Carbon\Carbon::parse($ferryIso)->format('h:i A') }}</option>
                                    </select>
                                    <input type="hidden" name="ferry_time" value="{{ $ferryIso }}">
                                    <input type="hidden" name="ferry_type" value="REGULAR">
                                @else
                                    <select class="form-control">
                                        <option value="">-- Select Schedule --</option>
                                    </select>
                                    <input type="hidden" name="ferry_type" value="REGULAR">
                                @endif
                            @endif
                            <span class="badge badge-success">Status: On Time</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column - Passenger Details -->
        <div class="card">
            <div class="card-header">
                <div class="card-title">
                    <i data-lucide="user"></i>
                    Passenger Details
                </div>
            </div>
            <div class="card-body">
                <!-- Customer Name -->
                <div class="form-group">
                    <label class="form-label">Customer Name</label>
                    <input class="form-control" type="text" name="customer_name" value="{{ old('customer_name') }}" placeholder="Enter name">
                </div>

                <!-- Mobile Number -->
                <div class="form-group">
                    <label class="form-label">Mobile Number</label>
                    <input class="form-control" type="tel" name="customer_mobile" value="{{ old('customer_mobile') }}" placeholder="+91 XXXXX XXXXX">
                </div>

                <!-- Note -->
                <div class="note-box">
                    <div class="note-title">Note:</div>
                    <div class="note-text">Verify ID proof for non-local passengers before issuing tickets.</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Line Items Section -->
    <div class="card">
        <div class="line-items-header">
            <div class="card-title">Line Items</div>
            <button type="button" id="btnAddRow" class="btn-add-row">
                <i data-lucide="plus" class="w-4 h-4"></i>
                Add Row
            </button>
        </div>

        <div class="items-table-wrap">
            <table class="items-table" id="itemsGrid">
                <thead>
                    <tr>
                        <th style="width: 40px; text-align: center;">#</th>
                        <th style="width: 80px;">Item ID</th>
                        <th style="width: 200px;">Item Name</th>
                        <th style="width: 70px;">Qty</th>
                        <th style="width: 80px;">Rate</th>
                        <th style="width: 70px;">Levy</th>
                        <th style="width: 90px;">Amount</th>
                        <th style="width: 120px;">Vehicle Name</th>
                        <th style="width: 110px;">Vehicle No</th>
                        <th style="width: 60px; text-align: center;">Actions</th>
                    </tr>
                </thead>
                <tbody id="ticketLinesBody">
                    <tr>
                        <td><span class="row-number">1</span></td>
                        <td>
                            <select class="form-control item-select" name="lines[0][item_id]">
                                <option value="">--</option>
                            </select>
                            <input type="hidden" name="lines[0][item_name]">
                        </td>
                        <td><input class="form-control" name="lines[0][item_name_display]" readonly placeholder="Select item"></td>
                        <td><input class="form-control num-input" name="lines[0][qty]" type="number" step="1" min="1" value="1"></td>
                        <td><input class="form-control num-input" name="lines[0][rate]" type="number" step="0.01" min="0" readonly></td>
                        <td><input class="form-control num-input" name="lines[0][levy]" type="number" step="0.01" min="0" readonly></td>
                        <td><input class="form-control num-input" name="lines[0][amount]" type="number" step="0.01" min="0" readonly></td>
                        <td><input class="form-control" name="lines[0][vehicle_name]" placeholder="Optional"></td>
                        <td><input class="form-control" name="lines[0][vehicle_no]" placeholder="Optional"></td>
                        <td style="text-align: center;"><button type="button" class="btn-remove btn-remove-row">✕</button></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Footer with Summary and Buttons -->
        <div class="footer-section">
            <label class="print-checkbox">
                <input type="checkbox" id="printAfterSave">
                <i data-lucide="printer" class="w-4 h-4"></i>
                Print Receipt automatically
            </label>

            <div class="summary-actions">
                <div class="summary-box">
                    <div class="summary-row">
                        <span class="summary-label">Subtotal</span>
                        <span class="summary-value" id="totalBox">0.00</span>
                    </div>
                    <div class="summary-row">
                        <span class="summary-label">Discount %</span>
                        <input class="summary-input" name="discount_pct" type="number" step="0.01" min="0" value="0">
                    </div>
                    <div class="summary-row">
                        <span class="summary-label">Discount Amt</span>
                        <input class="summary-input" name="discount_rs" type="number" step="0.01" min="0" value="0">
                    </div>
                    <div class="summary-row">
                        <span class="summary-label">Net Total</span>
                        <span class="summary-value net-total" id="netBox">0.00</span>
                    </div>
                </div>

                <div class="action-buttons">
                    <button class="btn-save" type="button" id="openPaymentModal">
                        <i data-lucide="save" class="w-5 h-5"></i>
                        Save Ticket
                    </button>
                    <button class="btn-pay" type="button" id="openPayAndSave">
                        <i data-lucide="credit-card" class="w-5 h-5"></i>
                        Pay & Save
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>

<!-- Payment Modal -->
<div id="paymentModal" class="modal-overlay">
    <div class="modal-content">
        <div class="modal-header">
            <span>Confirm Payment</span>
            <button type="button" class="modal-close" id="closePaymentModal">✕</button>
        </div>
        <div class="modal-body">
            <div class="form-group" style="margin-bottom: 1rem;">
                <label class="form-label">Net Total</label>
                <input type="text" id="modalNetTotal" class="form-control" readonly>
            </div>
            <div class="form-group" style="margin-bottom: 1rem;">
                <label class="form-label">Payment Mode</label>
                <select id="paymentMode" class="form-control" onchange="handlePaymentModeChange(this.value)">
                    <option value="Cash">Cash</option>
                    <option value="Guest Pass">Guest Pass</option>
                    <option value="GPay">GPay</option>
                </select>
            </div>
            <div class="form-group" style="margin-bottom: 1rem;">
                <label class="form-label">Given Amount</label>
                <input type="number" id="modalGivenAmount" class="form-control" placeholder="Enter given amount">
            </div>
            <div class="form-group">
                <label class="form-label">Change to Return</label>
                <input type="text" id="modalReturnChange" class="form-control" readonly>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" id="modalCancel" class="btn-cancel">Cancel</button>
            <button type="button" id="modalConfirm" class="btn-confirm">Store</button>
        </div>
    </div>
</div>

<!-- Guest Modal -->
<div id="guestModal" class="modal-overlay">
    <div class="modal-content">
        <div class="modal-header">
            <span>Guest Details</span>
            <button type="button" class="modal-close" id="closeGuestModal">✕</button>
        </div>
        <div class="modal-body">
            <div class="form-group" style="margin-bottom: 1rem;">
                <label class="form-label">Search by Guest ID</label>
                <input type="text" id="guestId" class="form-control" placeholder="Enter Guest ID">
            </div>
            <div class="divider-text">OR</div>
            <div class="form-group">
                <label class="form-label">Search by Guest Name</label>
                <input type="text" id="guestName" class="form-control" placeholder="Enter Guest Name">
                <ul id="guestList" class="guest-list"></ul>
            </div>
            <div style="text-align: right; margin-top: 0.75rem;">
                <button id="addGuestBtn" class="btn-add-row" style="display: none;">Add New Guest</button>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn-confirm" onclick="selectGuest()">Continue</button>
        </div>
    </div>
</div>

@if(session('ok'))
    <div class="alert-success">{{ session('ok') }}</div>
@endif

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
lucide.createIcons();

const branchFerryMap = @json($ferryBoatsPerBranch ?? []);
const ferrySchedulesPerBranch = @json($ferrySchedulesPerBranch ?? []);
const ferryTimeInput = document.getElementById('ferryTimeInput');
const ferryTimeSelect = document.getElementById('ferryTimeSelect');

ferryTimeSelect?.addEventListener('change', function() {
    const selectedTime = this.value;
    if (!selectedTime) {
        if (ferryTimeInput) ferryTimeInput.value = '';
        return;
    }

    const today = new Date();
    const [hours, minutes] = selectedTime.split(':');
    today.setHours(hours, minutes, 0, 0);

    const yyyy = today.getFullYear();
    const mm   = String(today.getMonth()+1).padStart(2,'0');
    const dd   = String(today.getDate()).padStart(2,'0');
    const hh   = String(today.getHours()).padStart(2,'0');
    const min  = String(today.getMinutes()).padStart(2,'0');

    if (ferryTimeInput) ferryTimeInput.value = `${yyyy}-${mm}-${dd}T${hh}:${min}`;
});

document.getElementById('branchSelect')?.addEventListener('change', function() {
    const branchId = this.value;
    const schedules = ferrySchedulesPerBranch[branchId] || [];
    const scheduleSelect = document.getElementById('ferryTimeSelect');

    if (scheduleSelect) {
        scheduleSelect.innerHTML = '<option value="">-- Select Schedule --</option>';
        schedules.forEach(fs => {
            let opt = document.createElement('option');
            opt.value = fs.time;
            opt.textContent = fs.time;
            scheduleSelect.appendChild(opt);
        });
    }

    const ferrySelect = document.getElementById('ferryBoatSelect');
    ferrySelect.innerHTML = '<option value="">-- Select Boat --</option>';

    if (branchFerryMap[branchId]) {
        branchFerryMap[branchId].forEach(fb => {
            const opt = document.createElement('option');
            opt.value = fb.id;
            opt.textContent = `${fb.name} (Capacity: ${fb.pax_capacity || 'N/A'})`;
            ferrySelect.appendChild(opt);
        });
    }

    if (ferryTimeInput) ferryTimeInput.value = '';
});

// Item dropdown and calculation logic
(function() {
    const listItemsUrl = "{{ route('ajax.item-rates.list') }}";
    const tbody = document.querySelector('#ticketLinesBody');
    let cachedItems = [];

    async function loadItemsForBranch(branchId) {
        if (!branchId) return;

        try {
            const url = new URL(listItemsUrl, window.location.origin);
            url.searchParams.set('branch_id', branchId);

            const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
            if (!res.ok) throw new Error('Failed to load items');
            cachedItems = await res.json();

            document.querySelectorAll('.item-select').forEach(populateDropdown);
        } catch (e) {
            console.error('Error loading items:', e);
            cachedItems = [];
        }
    }

    function populateDropdown(select) {
        const currentValue = select.value;
        select.innerHTML = '<option value="">--</option>';

        const passengers = cachedItems.filter(i => !i.is_vehicle);
        const vehicles = cachedItems.filter(i => i.is_vehicle);

        if (passengers.length) {
            const group = document.createElement('optgroup');
            group.label = 'Passengers';
            passengers.forEach(item => {
                const opt = document.createElement('option');
                opt.value = item.id;
                opt.textContent = `${item.item_id || item.id} - ${item.item_name}`;
                opt.dataset.rate = item.item_rate;
                opt.dataset.levy = item.item_lavy;
                opt.dataset.name = item.item_name;
                opt.dataset.itemId = item.item_id || item.id;
                opt.dataset.isVehicle = '0';
                group.appendChild(opt);
            });
            select.appendChild(group);
        }

        if (vehicles.length) {
            const group = document.createElement('optgroup');
            group.label = 'Vehicles';
            vehicles.forEach(item => {
                const opt = document.createElement('option');
                opt.value = item.id;
                opt.textContent = `${item.item_id || item.id} - ${item.item_name}`;
                opt.dataset.rate = item.item_rate;
                opt.dataset.levy = item.item_lavy;
                opt.dataset.name = item.item_name;
                opt.dataset.itemId = item.item_id || item.id;
                opt.dataset.isVehicle = '1';
                group.appendChild(opt);
            });
            select.appendChild(group);
        }

        if (currentValue) select.value = currentValue;
    }

    tbody.addEventListener('change', (e) => {
        if (!e.target.classList.contains('item-select')) return;

        const select = e.target;
        const tr = select.closest('tr');
        const selectedOpt = select.options[select.selectedIndex];

        if (selectedOpt && selectedOpt.value) {
            tr.querySelector('input[name$="[item_name]"]').value = selectedOpt.dataset.name || '';
            tr.querySelector('input[name$="[item_name_display]"]').value = selectedOpt.dataset.name || '';
            tr.querySelector('input[name$="[rate]"]').value = parseFloat(selectedOpt.dataset.rate || 0).toFixed(2);
            tr.querySelector('input[name$="[levy]"]').value = parseFloat(selectedOpt.dataset.levy || 0).toFixed(2);
        } else {
            tr.querySelector('input[name$="[item_name]"]').value = '';
            tr.querySelector('input[name$="[item_name_display]"]').value = '';
            tr.querySelector('input[name$="[rate]"]').value = '';
            tr.querySelector('input[name$="[levy]"]').value = '';
        }

        computeRow(tr);
    });

    function getCurrentBranchId() {
        const branchSelect = document.getElementById('branchSelect');
        if (branchSelect) return branchSelect.value;
        return document.getElementById('branch_id')?.value || '';
    }

    const branchSelect = document.getElementById('branchSelect');
    if (branchSelect) {
        branchSelect.addEventListener('change', () => loadItemsForBranch(branchSelect.value));
    }

    const initialBranch = getCurrentBranchId();
    if (initialBranch) loadItemsForBranch(initialBranch);

    window.populateItemDropdown = populateDropdown;

    function computeRow(tr) {
        const qty = parseFloat(tr.querySelector('input[name$="[qty]"]').value) || 0;
        const rate = parseFloat(tr.querySelector('input[name$="[rate]"]').value) || 0;
        const levy = parseFloat(tr.querySelector('input[name$="[levy]"]').value) || 0;
        const amtEl = tr.querySelector('input[name$="[amount]"]');
        const amt = qty * (rate + levy);
        amtEl.value = amt ? amt.toFixed(2) : '';
        computeTotals();
    }

    const totalBox = document.getElementById('totalBox');
    const netBox = document.getElementById('netBox');
    const discountPct = document.querySelector('input[name="discount_pct"]');
    const discountRs = document.querySelector('input[name="discount_rs"]');

    window.computeTotals = function() {
        let total = 0;
        tbody.querySelectorAll('input[name$="[amount]"]').forEach(i => {
            total += parseFloat(i.value) || 0;
        });
        totalBox.textContent = total.toFixed(2);

        let net = total;
        const dPct = parseFloat(discountPct?.value) || 0;
        const dRs = parseFloat(discountRs?.value) || 0;
        if (dPct > 0) net -= (total * dPct / 100);
        if (dRs > 0) net -= dRs;
        netBox.textContent = Math.max(net, 0).toFixed(2);
    }

    tbody.addEventListener('input', (e) => {
        const nm = e.target.name || '';
        if (/\[(qty|rate|levy)\]$/.test(nm)) {
            computeRow(e.target.closest('tr'));
        }
    });

    discountPct?.addEventListener('input', computeTotals);
    discountRs?.addEventListener('input', computeTotals);
})();

// Add Row logic
const tbodyEl = document.getElementById('ticketLinesBody');
const btnAddRow = document.getElementById('btnAddRow');

function nextRowIndex() {
    let maxIdx = -1;
    tbodyEl.querySelectorAll('input[name^="lines["]').forEach(inp => {
        const m = inp.name.match(/^lines\[(\d+)\]\[/);
        if (m) maxIdx = Math.max(maxIdx, parseInt(m[1], 10));
    });
    return maxIdx + 1;
}

function updateRowNumbers() {
    tbodyEl.querySelectorAll('tr').forEach((row, index) => {
        const numEl = row.querySelector('.row-number');
        if (numEl) numEl.textContent = index + 1;
    });
}

function buildRowHTML(idx, rowNum) {
    return `
        <td><span class="row-number">${rowNum}</span></td>
        <td>
            <select class="form-control item-select" name="lines[${idx}][item_id]">
                <option value="">--</option>
            </select>
            <input type="hidden" name="lines[${idx}][item_name]">
        </td>
        <td><input class="form-control" name="lines[${idx}][item_name_display]" readonly placeholder="Select item"></td>
        <td><input class="form-control num-input" name="lines[${idx}][qty]" type="number" step="1" min="1" value="1"></td>
        <td><input class="form-control num-input" name="lines[${idx}][rate]" type="number" step="0.01" min="0" readonly></td>
        <td><input class="form-control num-input" name="lines[${idx}][levy]" type="number" step="0.01" min="0" readonly></td>
        <td><input class="form-control num-input" name="lines[${idx}][amount]" type="number" step="0.01" min="0" readonly></td>
        <td><input class="form-control" name="lines[${idx}][vehicle_name]" placeholder="Optional"></td>
        <td><input class="form-control" name="lines[${idx}][vehicle_no]" placeholder="Optional"></td>
        <td style="text-align: center;"><button type="button" class="btn-remove btn-remove-row">✕</button></td>
    `;
}

tbodyEl.addEventListener('click', (e) => {
    if (e.target.classList.contains('btn-remove-row')) {
        e.target.closest('tr').remove();
        updateRowNumbers();
        computeTotals();
    }
});

function addRow(focus = true) {
    const idx = nextRowIndex();
    const rowNum = tbodyEl.querySelectorAll('tr').length + 1;
    const tr = document.createElement('tr');
    tr.innerHTML = buildRowHTML(idx, rowNum);
    tbodyEl.appendChild(tr);

    const newSelect = tr.querySelector('.item-select');
    if (newSelect && window.populateItemDropdown) {
        window.populateItemDropdown(newSelect);
    }

    if (focus && newSelect) newSelect.focus();
}

btnAddRow?.addEventListener('click', () => addRow(true));

tbodyEl.addEventListener('keydown', (e) => {
    if (e.target.name?.endsWith('[amount]') && e.key === 'Enter') {
        e.preventDefault();
        addRow(true);
    }
});

// Payment Modal
const openPaymentModal = document.getElementById('openPaymentModal');
const openPayAndSave = document.getElementById('openPayAndSave');
const paymentModal = document.getElementById('paymentModal');
const modalGivenAmount = document.getElementById('modalGivenAmount');
const modalNetTotal = document.getElementById('modalNetTotal');
const modalReturnChange = document.getElementById('modalReturnChange');
const modalConfirm = document.getElementById('modalConfirm');
const modalCancel = document.getElementById('modalCancel');
const closePaymentModal = document.getElementById('closePaymentModal');
const netBox = document.getElementById('netBox');

function showPaymentModal() {
    modalNetTotal.value = netBox.textContent;
    modalGivenAmount.value = '';
    modalReturnChange.value = '';
    paymentModal.style.display = 'flex';
}

openPaymentModal?.addEventListener('click', showPaymentModal);
openPayAndSave?.addEventListener('click', showPaymentModal);

modalCancel?.addEventListener('click', () => paymentModal.style.display = 'none');
closePaymentModal?.addEventListener('click', () => paymentModal.style.display = 'none');

modalGivenAmount?.addEventListener('input', () => {
    const given = parseFloat(modalGivenAmount.value) || 0;
    const net = parseFloat(modalNetTotal.value) || 0;
    modalReturnChange.value = (given - net).toFixed(2);
});

// Guest Modal
const guestModalEl = document.getElementById('guestModal');
const closeGuestModal = document.getElementById('closeGuestModal');

closeGuestModal?.addEventListener('click', () => guestModalEl.style.display = 'none');

function showGuestModal() { guestModalEl.style.display = 'flex'; }
function hideGuestModal() { guestModalEl.style.display = 'none'; }
function hidePaymentModal() { paymentModal.style.display = 'none'; }

function handlePaymentModeChange(value) {
    let input = document.getElementById('payment_mode');
    if (!input) {
        input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'payment_mode';
        input.id = 'payment_mode';
        document.getElementById('ticketForm').appendChild(input);
    }

    if (value === 'Guest Pass') {
        input.value = 'Cash';
        hidePaymentModal();
        setTimeout(showGuestModal, 200);
    } else {
        input.value = value;
    }
}

$(document).ready(function() {
    $('#guestId').on('keyup', function() {
        let guestId = $(this).val().trim();
        if (guestId.length > 0) {
            $.ajax({
                url: '/ajax/search-guest-by-id',
                method: 'GET',
                data: { id: guestId },
                success: function(response) {
                    if (response) {
                        $('#guestName').val(response.name);
                        $('#addGuestBtn').hide();
                    } else {
                        $('#guestName').val('');
                        $('#addGuestBtn').show();
                    }
                }
            });
        }
    });

    $('#guestName').on('keyup', function() {
        let name = $(this).val().trim();
        if (name.length > 1) {
            $.ajax({
                url: '/ajax/search-guest-by-name',
                method: 'GET',
                data: { name },
                success: function(response) {
                    let list = $('#guestList');
                    list.empty().show();
                    if (response.length > 0) {
                        response.forEach(g => {
                            list.append(`<li onclick="selectGuestFromList('${g.id}', '${g.name}')">${g.name}</li>`);
                        });
                        $('#addGuestBtn').hide();
                    } else {
                        list.hide();
                        $('#addGuestBtn').show();
                    }
                }
            });
        } else {
            $('#guestList').hide();
        }
    });
});

function selectGuestFromList(id, name) {
    $('#guestId').val(id);
    $('#guestName').val(name);
    $('#guestList').hide();
}

function selectGuest() {
    const id = $('#guestId').val().trim();
    const name = $('#guestName').val().trim();

    if (!id && !name) {
        Swal.fire({ icon: 'warning', title: 'Missing Guest Details', text: 'Please enter Guest ID or Name.' });
        return;
    }

    $('#guest_id_hidden').val(id || name);
    hideGuestModal();

    setTimeout(() => {
        $('#payment_mode').val('Cash');
        submitTicket();
    }, 300);
}

function submitTicket() {
    const form = document.getElementById('ticketForm');
    const formData = new FormData(form);
    const shouldPrint = document.getElementById('printAfterSave')?.checked === true;
    formData.append('print', shouldPrint ? 1 : 0);

    axios.post(form.action, formData)
        .then(res => {
            if (res.data.ok) {
                if (shouldPrint && res.data.ticket_id) {
                    window.open(`{{ url('/tickets') }}/${res.data.ticket_id}/print`, '_blank');
                }

                Swal.fire({
                    icon: 'success',
                    title: 'Ticket Confirmed',
                    html: `<b>Total:</b> ${res.data.total}`,
                    confirmButtonColor: '#6366f1'
                }).then(() => {
                    form.reset();
                    document.getElementById('totalBox').textContent = '0.00';
                    document.getElementById('netBox').textContent = '0.00';
                    resetForm();
                });
            }
        })
        .catch(err => {
            if (err.response?.data?.errors) {
                let errors = Object.values(err.response.data.errors).flat().join("<br>");
                Swal.fire({ icon: 'error', title: 'Validation Error', html: errors, confirmButtonColor: '#dc2626' });
            } else {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Something went wrong while saving the ticket.', confirmButtonColor: '#dc2626' });
            }
        });
}

function resetForm() {
    tbodyEl.innerHTML = `
        <tr>
            <td><span class="row-number">1</span></td>
            <td>
                <select class="form-control item-select" name="lines[0][item_id]">
                    <option value="">--</option>
                </select>
                <input type="hidden" name="lines[0][item_name]">
            </td>
            <td><input class="form-control" name="lines[0][item_name_display]" readonly placeholder="Select item"></td>
            <td><input class="form-control num-input" name="lines[0][qty]" type="number" step="1" min="1" value="1"></td>
            <td><input class="form-control num-input" name="lines[0][rate]" type="number" step="0.01" min="0" readonly></td>
            <td><input class="form-control num-input" name="lines[0][levy]" type="number" step="0.01" min="0" readonly></td>
            <td><input class="form-control num-input" name="lines[0][amount]" type="number" step="0.01" min="0" readonly></td>
            <td><input class="form-control" name="lines[0][vehicle_name]" placeholder="Optional"></td>
            <td><input class="form-control" name="lines[0][vehicle_no]" placeholder="Optional"></td>
            <td style="text-align: center;"><button type="button" class="btn-remove btn-remove-row">✕</button></td>
        </tr>`;

    const newSelect = tbodyEl.querySelector('.item-select');
    if (newSelect && window.populateItemDropdown) {
        window.populateItemDropdown(newSelect);
    }
}

modalConfirm?.addEventListener('click', function() {
    const paymentMode = document.getElementById('paymentMode').value;
    document.getElementById('payment_mode').value = paymentMode;
    paymentModal.style.display = 'none';
    submitTicket();
});

// Form submit handler
document.getElementById('ticketForm').addEventListener('submit', function(e) {
    e.preventDefault();
    submitTicket();
});
</script>
@endpush
@endsection
