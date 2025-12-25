@extends('layouts.admin')

@section('title', 'Ticket Entry')
@section('page-title', 'Ticket Entry')

@section('content')
<style>
/* Modern styling overrides */
.ticket-card {
    background: white;
    border-radius: 1rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    border: 1px solid #e2e8f0;
    overflow: hidden;
}

.ticket-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 1rem 1.5rem;
    font-weight: 700;
    font-size: 1.25rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.form-strip {
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
    padding: 1.25rem;
}

.form-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
}

.form-group label {
    display: block;
    font-size: 0.75rem;
    font-weight: 600;
    color: #64748b;
    margin-bottom: 0.375rem;
    text-transform: uppercase;
    letter-spacing: 0.025em;
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
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.form-control:read-only {
    background: #f1f5f9;
}

/* Table styling */
.items-table-wrap {
    overflow-x: auto;
}

.items-table {
    width: 100%;
    border-collapse: collapse;
}

.items-table thead th {
    background: #f1f5f9;
    padding: 0.75rem 0.625rem;
    font-size: 0.75rem;
    font-weight: 600;
    color: #475569;
    text-transform: uppercase;
    letter-spacing: 0.025em;
    text-align: left;
    border-bottom: 2px solid #e2e8f0;
    white-space: nowrap;
}

.items-table tbody td {
    padding: 0.5rem 0.375rem;
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

/* Summary section */
.summary-section {
    background: #f8fafc;
    border-top: 1px solid #e2e8f0;
    padding: 1.25rem;
}

.summary-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1rem;
    max-width: 500px;
    margin-left: auto;
}

.summary-row {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.summary-label {
    font-size: 0.875rem;
    font-weight: 600;
    color: #475569;
    min-width: 100px;
}

.summary-box {
    background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
    color: #4ade80;
    font-weight: 700;
    font-size: 1.25rem;
    padding: 0.625rem 1rem;
    border-radius: 0.5rem;
    text-align: right;
    min-width: 120px;
    font-family: 'Courier New', monospace;
}

/* Footer */
.ticket-footer {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 1rem 1.5rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.print-checkbox {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: white;
    font-size: 0.875rem;
}

.print-checkbox input[type="checkbox"] {
    width: 1.125rem;
    height: 1.125rem;
    accent-color: #4ade80;
}

/* Buttons */
.btn-add-row {
    background: white;
    border: 1px solid #e2e8f0;
    padding: 0.5rem 1rem;
    border-radius: 0.5rem;
    font-size: 0.875rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
    display: inline-flex;
    align-items: center;
    gap: 0.375rem;
}

.btn-add-row:hover {
    background: #f8fafc;
    border-color: #cbd5e1;
}

.btn-save {
    background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
    color: white;
    border: none;
    padding: 0.75rem 1.5rem;
    border-radius: 0.625rem;
    font-weight: 600;
    font-size: 0.9375rem;
    cursor: pointer;
    transition: all 0.2s;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    box-shadow: 0 4px 14px rgba(34, 197, 94, 0.3);
}

.btn-save:hover {
    transform: translateY(-1px);
    box-shadow: 0 6px 20px rgba(34, 197, 94, 0.4);
}

.btn-remove {
    background: #fee2e2;
    color: #dc2626;
    border: none;
    width: 2rem;
    height: 2rem;
    border-radius: 0.375rem;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s;
}

.btn-remove:hover {
    background: #fecaca;
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
    border-radius: 1rem;
    max-width: 420px;
    width: 90%;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    overflow: hidden;
}

.modal-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 1rem 1.5rem;
    font-weight: 600;
    font-size: 1.125rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.modal-close {
    background: rgba(255,255,255,0.2);
    border: none;
    color: white;
    width: 2rem;
    height: 2rem;
    border-radius: 0.375rem;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
}

.modal-body {
    padding: 1.5rem;
}

.modal-footer {
    padding: 1rem 1.5rem;
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

/* Guest Modal specific */
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

/* Success alert */
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

    <div class="ticket-card">
        <div class="ticket-header">
            <i data-lucide="ticket" class="w-6 h-6"></i>
            Ticket Entry
        </div>

        <!-- Form Fields -->
        <div class="form-strip">
            <div class="form-grid">
                <!-- Branch -->
                <div class="form-group">
                    <label>Branch Name</label>
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
                        <input type="hidden" name="branch_id" value="{{ $branchId }}">
                    @endif
                </div>

                <!-- Ferry Boat -->
                <div class="form-group">
                    <label>Ferry Boat</label>
                    <select name="ferry_boat_id" class="form-control" required id="ferryBoatSelect">
                        @foreach($ferryboatsBranch as $fb)
                            <option value="{{ $fb->id }}" @selected(old('ferry_boat_id')==$fb->id)>
                                {{ $fb->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Customer Name -->
                <div class="form-group">
                    <label>Customer Name</label>
                    <input class="form-control" type="text" name="customer_name" value="{{ old('customer_name') }}" placeholder="Enter name">
                </div>

                <!-- Mobile -->
                <div class="form-group">
                    <label>Mobile</label>
                    <input class="form-control" type="tel" name="customer_mobile" value="{{ old('customer_mobile') }}" placeholder="+91XXXXXXXXXX">
                </div>

                <!-- Ferry Time -->
                @if(in_array($user->role_id, [1,2]))
                    <div class="form-group">
                        <label>Ferry Time</label>
                        <select id="ferryTimeSelect" class="form-control">
                            <option value="">-- Select Schedule --</option>
                            @foreach($ferrySchedulesPerBranch[$branchId] ?? [] as $fs)
                                <option value="{{ $fs['time'] }}">{{ $fs['time'] }}</option>
                            @endforeach
                        </select>
                        <input type="hidden" name="ferry_time" id="ferryTimeInput" value="">
                        <input type="hidden" name="ferry_type" value="REGULAR">
                    </div>
                @else
                    @if(!$hideFerryTime)
                        <div class="form-group">
                            <label>Ferry Time</label>
                            @php($ferryIso = old('ferry_time', $nextFerryTime))
                            <input type="datetime-local" class="form-control" value="{{ $ferryIso }}" disabled>
                            <input type="hidden" name="ferry_time" value="{{ $ferryIso }}">
                            <input type="hidden" name="ferry_type" value="REGULAR">
                        </div>
                    @else
                        <input type="hidden" name="ferry_type" value="REGULAR">
                    @endif
                @endif
            </div>
        </div>

        <!-- Add Row Button -->
        <div style="padding: 0.75rem 1.25rem; text-align: right; background: white;">
            <button type="button" id="btnAddRow" class="btn-add-row">
                <i data-lucide="plus" class="w-4 h-4"></i>
                Add Row
            </button>
        </div>

        <!-- Items Table -->
        <div class="items-table-wrap">
            <table class="items-table" id="itemsGrid">
                <thead>
                    <tr>
                        <th style="width: 100px;">Item ID</th>
                        <th>Item Name</th>
                        <th style="width: 90px;">Qty</th>
                        <th style="width: 100px;">Rate</th>
                        <th style="width: 100px;">Levy</th>
                        <th style="width: 110px;">Amount</th>
                        <th style="width: 140px;">Vehicle Name</th>
                        <th style="width: 120px;">Vehicle No</th>
                        <th style="width: 50px;"></th>
                    </tr>
                </thead>
                <tbody id="ticketLinesBody">
                    <tr>
                        <td><input class="form-control" name="lines[0][item_id]" placeholder=""></td>
                        <td><input class="form-control" name="lines[0][item_name]" placeholder="" readonly></td>
                        <td><input class="form-control num-input" name="lines[0][qty]" type="number" step="1" min="0"></td>
                        <td><input class="form-control num-input" name="lines[0][rate]" type="number" step="0.01" min="0"></td>
                        <td><input class="form-control num-input" name="lines[0][levy]" type="number" step="0.01" min="0"></td>
                        <td><input class="form-control num-input" name="lines[0][amount]" type="number" step="0.01" min="0" readonly></td>
                        <td><input class="form-control" name="lines[0][vehicle_name]"></td>
                        <td><input class="form-control" name="lines[0][vehicle_no]"></td>
                        <td><button type="button" class="btn-remove btn-remove-row">✕</button></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Summary Section -->
        <div class="summary-section">
            <div class="summary-grid">
                <div class="summary-row">
                    <span class="summary-label">Discount %</span>
                    <input class="form-control num-input" name="discount_pct" type="number" step="0.01" min="0" style="max-width: 100px;">
                </div>
                <div class="summary-row">
                    <span class="summary-label">Total</span>
                    <div class="summary-box" id="totalBox">0.00</div>
                </div>
                <div class="summary-row">
                    <span class="summary-label">Discount ₹</span>
                    <input class="form-control num-input" name="discount_rs" type="number" step="0.01" min="0" style="max-width: 100px;">
                </div>
                <div class="summary-row">
                    <span class="summary-label">Net</span>
                    <div class="summary-box" id="netBox">0.00</div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="ticket-footer">
            <label class="print-checkbox">
                <input type="checkbox" id="printAfterSave">
                Print after save
            </label>
            <button class="btn-save" type="button" id="openPaymentModal">
                <i data-lucide="save" class="w-5 h-5"></i>
                Save Ticket
            </button>
        </div>
    </div>
</form>

<!-- Payment Modal -->
<div id="paymentModal" class="modal-overlay">
    <div class="modal-content">
        <div class="modal-header">
            <span>Confirm Payment</span>
        </div>
        <div class="modal-body">
            <div class="form-group" style="margin-bottom: 1rem;">
                <label>Net Total</label>
                <input type="text" id="modalNetTotal" class="form-control" readonly>
            </div>
            <div class="form-group" style="margin-bottom: 1rem;">
                <label>Payment Mode</label>
                <select id="paymentMode" class="form-control" onchange="handlePaymentModeChange(this.value)">
                    <option value="Cash">Cash</option>
                    <option value="Guest Pass">Guest Pass</option>
                    <option value="GPay">GPay</option>
                </select>
            </div>
            <div class="form-group" style="margin-bottom: 1rem;">
                <label>Given Amount</label>
                <input type="number" id="modalGivenAmount" class="form-control" placeholder="Enter given amount">
            </div>
            <div class="form-group">
                <label>Change to Return</label>
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
                <label>Search by Guest ID</label>
                <input type="text" id="guestId" class="form-control" placeholder="Enter Guest ID">
            </div>
            <div class="divider-text">OR</div>
            <div class="form-group">
                <label>Search by Guest Name</label>
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

const branchSelect = document.getElementById('branchSelect');

ferryTimeSelect?.addEventListener('change', function() {
    const selectedTime = this.value;
    if (!selectedTime) {
        ferryTimeInput.value = '';
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

    ferryTimeInput.value = `${yyyy}-${mm}-${dd}T${hh}:${min}`;
});

document.getElementById('branchSelect')?.addEventListener('change', function() {
    const branchId = this.value;
    const schedules = ferrySchedulesPerBranch[branchId] || [];
    const scheduleSelect = document.getElementById('ferryTimeSelect');
    scheduleSelect.innerHTML = '';

    const defaultOpt = document.createElement('option');
    defaultOpt.value = '';
    defaultOpt.textContent = '-- Select Schedule --';
    scheduleSelect.appendChild(defaultOpt);

    schedules.forEach(fs => {
        let opt = document.createElement('option');
        opt.value = fs.time;
        opt.textContent = fs.time;
        scheduleSelect.appendChild(opt);
    });

    const ferrySelect = document.getElementById('ferryBoatSelect');
    ferrySelect.innerHTML = '';

    const defaultBoatOpt = document.createElement('option');
    defaultBoatOpt.value = '';
    defaultBoatOpt.textContent = '-- Select Boat --';
    ferrySelect.appendChild(defaultBoatOpt);

    if (branchFerryMap[branchId]) {
        branchFerryMap[branchId].forEach(fb => {
            const opt = document.createElement('option');
            opt.value = fb.id;
            opt.textContent = fb.name;
            ferrySelect.appendChild(opt);
        });
    }
    ferryTimeInput.value = '';
});

document.getElementById('ticketForm').addEventListener('submit', function (e) {
    e.preventDefault();

    let form = e.target;
    let formData = new FormData(form);

    const shouldPrint = document.getElementById('printAfterSave')?.checked === true;
    formData.append('print', shouldPrint ? 1 : 0);

    axios.post(form.action, formData)
        .then(res => {
            if (res.data.ok) {
                if (shouldPrint && res.data.ticket_id) {
                    fetch(`{{ url('/tickets') }}/${res.data.ticket_id}/print`)
                        .then(res => res.text())
                        .then(html => {
                            const printWindow = window.open('', '', 'width=400,height=600');
                            printWindow.document.write(html);
                            printWindow.document.close();
                            printWindow.focus();
                            printWindow.print();
                            setTimeout(() => {
                                printWindow.close();
                            }, 1000);
                        });
                }

                Swal.fire({
                    icon: 'success',
                    title: 'Ticket Confirmed',
                    html: `<b>Total:</b> ${res.data.total}`,
                    confirmButtonColor: '#22c55e'
                });

                form.reset();
                document.getElementById('totalBox').textContent = '0.00';
                document.getElementById('netBox').textContent = '0.00';
                document.getElementById('ticketLinesBody').innerHTML = '';

                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td><input class="form-control" name="lines[0][item_id]" placeholder=""></td>
                    <td><input class="form-control" name="lines[0][item_name]" placeholder="" readonly></td>
                    <td><input class="form-control num-input" name="lines[0][qty]" type="number" step="1" min="0"></td>
                    <td><input class="form-control num-input" name="lines[0][rate]" type="number" step="0.01" min="0"></td>
                    <td><input class="form-control num-input" name="lines[0][levy]" type="number" step="0.01" min="0"></td>
                    <td><input class="form-control num-input" name="lines[0][amount]" type="number" step="0.01" min="0" readonly></td>
                    <td><input class="form-control" name="lines[0][vehicle_name]"></td>
                    <td><input class="form-control" name="lines[0][vehicle_no]"></td>
                    <td><button type="button" class="btn-remove btn-remove-row">✕</button></td>`;
                document.getElementById('ticketLinesBody').appendChild(tr);
            }
        })
        .catch(err => {
            if (err.response && err.response.data.errors) {
                let errors = Object.values(err.response.data.errors).flat().join("<br>");
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    html: errors,
                    confirmButtonColor: '#dc2626'
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Something went wrong while saving the ticket.',
                    confirmButtonColor: '#dc2626'
                });
            }
        });
});

(function() {
    const apiUrl = "{{ route('ajax.item-rates.find') }}";
    const tbody = document.querySelector('#ticketLinesBody');

    function clearRow(tr, opts = {}) {
        if (opts.clearId) tr.querySelector('input[name$="[item_id]"]').value = '';
        tr.querySelector('input[name$="[item_name]"]').value = '';
        tr.querySelector('input[name$="[rate]"]').value = '';
        tr.querySelector('input[name$="[levy]"]').value = '';
        tr.querySelector('input[name$="[amount]"]').value = '';
        tr.querySelector('input[name$="[item_id]"]').dataset.lastLookup = '';
        computeTotals();
    }

    async function lookupAndFill(tr, q) {
        if (!q) { clearRow(tr); return false; }

        const idInput = tr.querySelector('input[name$="[item_id]"]');

        let branchId = '';
        const branchSelect = document.getElementById('branchSelect');
        if (branchSelect) {
            branchId = branchSelect.value;
        } else {
            branchId = document.getElementById('branch_id')?.value || '';
        }

        try {
            const url = new URL(apiUrl, window.location.origin);
            url.searchParams.set('q', q);
            if (branchId) url.searchParams.set('branch_id', branchId);

            const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
            if (!res.ok) throw new Error('Lookup failed');
            const data = await res.json();

            tr.querySelector('input[name$="[item_name]"]').value = data.item_name ?? '';
            tr.querySelector('input[name$="[rate]"]').value = (data.item_rate ?? 0).toFixed(2);
            tr.querySelector('input[name$="[levy]"]').value = (data.item_lavy ?? 0).toFixed(2);

            idInput.dataset.lastLookup = q;
            computeRow(tr);
            return true;
        } catch (e) {
            clearRow(tr);
            const msg = 'Item not found';
            idInput.setCustomValidity(msg);
            idInput.reportValidity();
            setTimeout(() => idInput.setCustomValidity(''), 1500);
            return false;
        }
    }

    tbody.addEventListener('input', (e) => {
        if (!e.target.name?.endsWith('[item_id]')) return;
        const tr = e.target.closest('tr');
        const hasValues =
            tr.querySelector('input[name$="[item_name]"]').value ||
            tr.querySelector('input[name$="[rate]"]').value ||
            tr.querySelector('input[name$="[levy]"]').value;
        if (hasValues) clearRow(tr);
    });

    tbody.addEventListener('keydown', async (e) => {
        const input = e.target;
        if (!input.name?.endsWith('[item_id]')) return;

        if (e.key === 'Enter' || e.key === 'Tab') {
            e.preventDefault();
            const tr = input.closest('tr');
            const ok = await lookupAndFill(tr, input.value.trim());
            const qty = tr.querySelector('input[name$="[qty]"]');
            (ok ? qty : input).focus();
            if (ok && qty) qty.select();
        }
    });

    tbody.addEventListener('focusout', async (e) => {
        const input = e.target;
        if (!input.name?.endsWith('[item_id]')) return;
        const tr = input.closest('tr');
        await lookupAndFill(tr, input.value.trim());
    });

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
        const dPct = parseFloat(discountPct.value) || 0;
        const dRs = parseFloat(discountRs.value) || 0;
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

    discountPct.addEventListener('input', computeTotals);
    discountRs.addEventListener('input', computeTotals);
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

function buildRowHTML(idx) {
    return `
        <tr>
            <td><input class="form-control" name="lines[${idx}][item_id]" placeholder=""></td>
            <td><input class="form-control" name="lines[${idx}][item_name]" placeholder="" readonly></td>
            <td><input class="form-control num-input" name="lines[${idx}][qty]" type="number" step="1" min="0"></td>
            <td><input class="form-control num-input" name="lines[${idx}][rate]" type="number" step="0.01" min="0"></td>
            <td><input class="form-control num-input" name="lines[${idx}][levy]" type="number" step="0.01" min="0"></td>
            <td><input class="form-control num-input" name="lines[${idx}][amount]" type="number" step="0.01" min="0" readonly></td>
            <td><input class="form-control" name="lines[${idx}][vehicle_name]"></td>
            <td><input class="form-control" name="lines[${idx}][vehicle_no]"></td>
            <td><button type="button" class="btn-remove btn-remove-row">✕</button></td>
        </tr>
    `;
}

tbodyEl.addEventListener('click', (e) => {
    if (e.target.classList.contains('btn-remove-row')) {
        const tr = e.target.closest('tr');
        tr.remove();
        computeTotals();
    }
});

function addRow(focusItemId = true) {
    const idx = nextRowIndex();
    const tr = document.createElement('tr');
    tr.innerHTML = buildRowHTML(idx);
    tbodyEl.appendChild(tr);
    if (focusItemId) {
        const idInput = tr.querySelector(`input[name="lines[${idx}][item_id]"]`);
        idInput && idInput.focus();
    }
}

btnAddRow?.addEventListener('click', () => addRow(true));

tbodyEl.addEventListener('keydown', (e) => {
    const isAmount = e.target.name?.endsWith('[amount]');
    if (isAmount && e.key === 'Enter') {
        e.preventDefault();
        addRow(true);
    }
});

// Payment Modal logic
const openPaymentModal = document.getElementById('openPaymentModal');
const paymentModal = document.getElementById('paymentModal');
const modalPaymentMode = document.getElementById('paymentMode');
const modalGivenAmount = document.getElementById('modalGivenAmount');
const modalNetTotal = document.getElementById('modalNetTotal');
const modalReturnChange = document.getElementById('modalReturnChange');
const modalConfirm = document.getElementById('modalConfirm');
const modalCancel = document.getElementById('modalCancel');
const netBox = document.getElementById('netBox');

openPaymentModal.addEventListener('click', () => {
    modalNetTotal.value = netBox.textContent;
    paymentModal.style.display = 'flex';
});

modalCancel.addEventListener('click', () => {
    paymentModal.style.display = 'none';
});

modalGivenAmount.addEventListener('input', () => {
    const given = parseFloat(modalGivenAmount.value) || 0;
    const net = parseFloat(modalNetTotal.value) || 0;
    modalReturnChange.value = (given - net).toFixed(2);
});

// Guest Modal
const guestModalEl = document.getElementById('guestModal');
const closeGuestModal = document.getElementById('closeGuestModal');

closeGuestModal?.addEventListener('click', () => {
    guestModalEl.style.display = 'none';
});

function showGuestModal() {
    guestModalEl.style.display = 'flex';
}

function hideGuestModal() {
    guestModalEl.style.display = 'none';
}

function showPaymentModal() {
    paymentModal.style.display = 'flex';
}

function hidePaymentModal() {
    paymentModal.style.display = 'none';
}

function handlePaymentModeChange(value) {
    const paymentModeInput = document.getElementById('payment_mode');

    if (!paymentModeInput) {
        const hidden = document.createElement('input');
        hidden.type = 'hidden';
        hidden.name = 'payment_mode';
        hidden.id = 'payment_mode';
        document.getElementById('ticketForm').appendChild(hidden);
    }

    const input = document.getElementById('payment_mode');

    if (value === 'Guest Pass') {
        input.value = 'Cash';
        hidePaymentModal();
        setTimeout(() => {
            showGuestModal();
        }, 200);
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
        Swal.fire({
            icon: 'warning',
            title: 'Missing Guest Details',
            text: 'Please enter Guest ID or Name.',
        });
        return;
    }

    $('#guest_id_hidden').val(id || name);
    hideGuestModal();

    setTimeout(() => {
        $('#payment_mode').val('Cash');
        $('#ticketForm').submit();
    }, 300);
}

document.getElementById('modalConfirm').addEventListener('click', function () {
    const paymentMode = document.getElementById('paymentMode').value;
    const givenAmount = parseFloat(document.getElementById('modalGivenAmount').value) || 0;
    const netTotal = parseFloat(document.getElementById('modalNetTotal').value) || 0;

    document.getElementById('payment_mode').value = paymentMode;
    document.getElementById('paymentModal').style.display = 'none';

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
                    text: 'Saved successfully!',
                    confirmButtonColor: '#22c55e'
                }).then(() => {
                    form.reset();
                    document.getElementById('totalBox').textContent = '0.00';
                    document.getElementById('netBox').textContent = '0.00';
                    document.getElementById('ticketLinesBody').innerHTML = `
                        <tr>
                            <td><input class="form-control" name="lines[0][item_id]" placeholder=""></td>
                            <td><input class="form-control" name="lines[0][item_name]" placeholder="" readonly></td>
                            <td><input class="form-control num-input" name="lines[0][qty]" type="number" step="1" min="0"></td>
                            <td><input class="form-control num-input" name="lines[0][rate]" type="number" step="0.01" min="0"></td>
                            <td><input class="form-control num-input" name="lines[0][levy]" type="number" step="0.01" min="0"></td>
                            <td><input class="form-control num-input" name="lines[0][amount]" type="number" step="0.01" min="0" readonly></td>
                            <td><input class="form-control" name="lines[0][vehicle_name]"></td>
                            <td><input class="form-control" name="lines[0][vehicle_no]"></td>
                            <td><button type="button" class="btn-remove btn-remove-row">✕</button></td>
                        </tr>`;
                });
            }
        })
        .catch(err => {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Something went wrong while saving the ticket.'
            });
        });
});
</script>
@endpush
@endsection
