@extends('layouts.app')

@section('content')
<style>
  .btn-light{background:#fff;border:1px solid rgba(0,0,0,.15);border-radius:4px;padding:8px 12px;font-size:13px;}


  :root{
    --win-border:#a9a9a9;
    --title-red:#b22222;
    --panel-bg:#f5f7fa;
    --grid-head:#f0f0f0;
    --grid-bg:#ffffff;
    --grid-alt:#f8f9fb;
    --grid-border:#cdd9c5;
    --footer-red:#b3262e;
    --save-green:#49aa3d;
  }
  .irs-window{max-width:1120px;margin:18px auto 32px;border:1px solid var(--win-border);border-radius:6px;background:#fff;box-shadow:0 2px 10px rgba(0,0,0,.04);overflow:hidden;}
  .irs-title{text-align:center;font-weight:700;font-size:22px;color:var(--title-red);padding:12px 14px 10px;border-bottom:1px solid var(--win-border);}
  .irs-strip{background:#fff;border-bottom:1px solid var(--win-border);padding:10px 12px;}
  .bar{background:var(--panel-bg);border:1px solid var(--win-border);border-radius:4px;padding:10px 12px;}
  .row-grid{display:grid;grid-template-columns: 140px 1fr 160px 1fr;gap:12px;align-items:center;}
  .label{font-size:13px;color:#333;font-weight:700;}
  .ctrl{width:100%;border:1px solid #c9c9c9;border-radius:4px;background:#fff;padding:8px 10px;font-size:14px;}
  .logo{display:flex;align-items:center;justify-content:center;}
  .logo img{height:64px;opacity:.9}

  .grid-wrap{background:#f5f5f7;border-top:1px solid var(--win-border);border-bottom:1px solid var(--win-border);padding:0;}
  table.grid{width:100%;border-collapse:collapse;}
  .grid thead th{background:var(--grid-head);font-size:13px;font-weight:700;color:#3a3a3a;border-bottom:1px solid var(--grid-border);padding:8px 10px;white-space:nowrap;}
  .grid tbody td{border-bottom:1px solid var(--grid-border);padding:7px 8px;font-size:13px;white-space:nowrap;background:#fff;}
  .grid tbody tr:nth-child(even) td{background:var(--grid-alt);}
  .num{width:100px;text-align:right;}
  .w-120{width:120px}
  .w-140{width:140px}
  .w-180{width:180px}

  .ticket-summary{padding:12px;border-top:1px solid var(--win-border);background:#fff;}
  .sum-grid{display:grid;grid-template-columns: 120px 140px 1fr 120px;gap:10px;align-items:center;}
  .sum-box{background:#000;color:#0f0;font-weight:700;font-size:18px;border-radius:4px;padding:6px 10px;text-align:right;}

  .footer{display:flex;align-items:center;justify-content:space-between;background:var(--footer-red);padding:10px 12px;}
  .btn-save{background:var(--save-green);color:#fff;font-weight:700;border:none;border-radius:4px;padding:9px 14px;}
  .btn-ghost{background:#fff;border:1px solid rgba(0,0,0,.15);border-radius:6px;width:34px;height:28px;display:inline-flex;align-items:center;justify-content:center;}
</style>

<form id="ticketForm" method="post" action="{{ route('ticket-entry.store') }}">
  @csrf
 

<div class="irs-window">
  <div class="irs-title">Ticket Entry</div>

  {{-- TOP STRIP --}}
  <div class="irs-strip">
    <div class="bar">
      <div class="row-grid">
       <div class="label">Branch Name :</div>
       <div>
@if(in_array($user->role_id, [1,2]))
  @php($selectedBranch = (string) old('branch_id', request('branch_id', '')))
  <select name="branch_id" class="ctrl" id="branchSelect">
      <option value="" {{ $selectedBranch === '' ? 'selected' : '' }}>-- Select Branch --</option>
      @foreach($branches as $branch)
        <option value="{{ $branch->id }}"
                {{ $selectedBranch === (string) $branch->id ? 'selected' : '' }}>
          {{ $branch->branch_name }}
        </option>
      @endforeach
  </select>
@else
  <input class="ctrl" value="{{ $branchName }}" readonly>
  <input type="hidden" name="branch_id" value="{{ $branchId }}">
@endif


       </div>

       <div class="label">Ferry Boat :</div>
       <div>
        <select name="ferry_boat_id" class="ctrl" required id="ferryBoatSelect">
            @foreach($ferryboatsBranch as $fb)
                <option value="{{ $fb->id }}" @selected(old('ferry_boat_id')==$fb->id)>
                    {{ $fb->name }}
                </option>
            @endforeach
        </select>
       </div>

        <div class="label">Payment Mode :</div>
        <div>
          <select name="payment_mode" class="ctrl" required>
            @foreach($paymentModes as $pm)
              <option value="{{ $pm }}" @selected(old('payment_mode')==$pm)>{{ $pm }}</option>
            @endforeach
          </select>
        </div>

        <div class="label">Customer Name :</div>
<div>
  <input class="ctrl" type="text" name="customer_name" value="{{ old('customer_name') }}" placeholder="Enter name">
</div>

<div class="label">Mobile :</div>
<div>
  <input class="ctrl" type="tel" name="customer_mobile" value="{{ old('customer_mobile') }}" placeholder="+91XXXXXXXXXX">
</div>


    {{-- @if(!$hideFerryTime)
<div class="label">Ferry Time :</div>
<div>
    @if(in_array($user->role_id, [1,2]))
        <select id="ferryTimeSelect" class="form-select">
            <option value="">-- Select Schedule --</option>
            @foreach($ferrySchedulesPerBranch[$branchId] ?? [] as $fs)
                <option value="{{ $fs['time'] }}">{{ $fs['time'] }}</option>
            @endforeach
        </select>
        <input type="hidden" name="ferry_time" id="ferryTimeInput" value="">
    @else
        @php($ferryIso = old('ferry_time', $nextFerryTime))
        <input type="datetime-local" class="ctrl" value="{{ $ferryIso }}" disabled>
        <input type="hidden" name="ferry_time" value="{{ $ferryIso }}">
    @endif
</div>
@else
<input type="hidden" name="ferry_time" value="{{ now('Asia/Kolkata')->format('Y-m-d\TH:i') }}">
<input type="hidden" name="ferry_type" value="SPECIAL">

@endif


    @if($beforeFirstFerry)
<div class="label">Ferry Time :</div>
<div>
    @if(in_array($user->role_id, [1,2]))
        <select id="ferryTimeSelect" class="form-select">
            <option value="">-- Select Schedule --</option>
            @foreach($ferrySchedulesPerBranch[$branchId] ?? [] as $fs)
                <option value="{{ $fs['time'] }}">{{ $fs['time'] }}</option>
            @endforeach
        </select>
        <input type="hidden" name="ferry_time" id="ferryTimeInput" value="">
    @else
        @php($ferryIso = old('ferry_time', $nextFerryTime))
        <input type="datetime-local" class="ctrl" value="{{ $ferryIso }}" disabled>
        <input type="hidden" name="ferry_time" value="{{ $ferryIso }}">
        <input type="hidden" name="ferry_type" value="REGULAR">
    @endif
</div>
@endif --}}

{{-- Ferry Time --}}
@if(in_array($user->role_id, [1,2]))
    <div class="label">Ferry Time :</div>
    <div>
        <select id="ferryTimeSelect" class="form-select">
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
        <div class="label">Ferry Time :</div>
        <div>
            @php($ferryIso = old('ferry_time', $nextFerryTime))
            <input type="datetime-local" class="ctrl" value="{{ $ferryIso }}" disabled>
            <input type="hidden" name="ferry_time" value="{{ $ferryIso }}">
             <input type="hidden" name="ferry_type" value="REGULAR">
        </div>
         @else
        {{-- Hide Ferry Time (Special case) --}}
        <input type="hidden" name="ferry_type" value="SPECIAL">
    @endif

    {{-- @if($beforeFirstFerry)
        <div class="label">Ferry Time :</div>
        <div>
            @php($ferryIso = old('ferry_time', $nextFerryTime))
            <input type="datetime-local" class="ctrl" value="{{ $ferryIso }}" disabled>
            <input type="hidden" name="ferry_time" value="{{ $ferryIso }}">
            <input type="hidden" name="ferry_type" value="REGULAR">
        </div>
    @endif --}}
@endif





      </div>
    </div>
  </div>

<div style="max-width:1120px;margin:6px auto 8px;text-align:right;">
  <button type="button" id="btnAddRow" class="btn-light">+ Add Row</button>
</div>

  {{-- GRID --}}
  <div class="grid-wrap">
    <table class="grid">
     <thead>
      <tr>
        <th class="w-120">Item ID</th>
        <th>Item Name</th>
        <th class="w-120">Quantity</th>
        <th class="w-120">Rate</th>
        <th class="w-120">Levy</th>
        <th class="w-120">Amount</th>
        <th class="w-180">Vehicle Name</th>
        <th class="w-140">Vehicle No</th>
        <th class="w-80">Action</th>
      </tr>
     </thead>

    <tbody id="ticketLinesBody">
      <tr>
        <td><input class="ctrl" name="lines[0][item_id]" placeholder=""></td>
        <td><input class="ctrl" name="lines[0][item_name]" placeholder="" readonly></td>
        <td><input class="ctrl num" name="lines[0][qty]" type="number" step="1" min="0"></td>
        <td><input class="ctrl num" name="lines[0][rate]" type="number" step="0.01" min="0"></td>
        <td><input class="ctrl num" name="lines[0][levy]" type="number" step="0.01" min="0"></td>
        <td><input class="ctrl num" name="lines[0][amount]" type="number" step="0.01" min="0" readonly></td>
        <td><input class="ctrl" name="lines[0][vehicle_name]"></td>
        <td><input class="ctrl" name="lines[0][vehicle_no]"></td>
        <td><button type="button" class="btn-ghost btn-remove">✖</button></td>
      </tr>
    </tbody>
    </table>
  </div>

  {{-- TICKET SUMMARY --}}
  <div class="ticket-summary">
    <div class="sum-grid">
      <div class="label">Ticket</div><div></div><div></div><div></div>

      <div class="label">Discount %</div>
      <div><input class="ctrl num" name="discount_pct" type="number" step="0.01" min="0"></div>
      <div class="label" style="text-align:right;">Total</div>
      <div class="sum-box" id="totalBox">0.00</div>

      <div class="label">Discount Rs</div>
      <div><input class="ctrl num" name="discount_rs" type="number" step="0.01" min="0"></div>
      <div class="label" style="text-align:right;">Net</div>
      <div class="sum-box" id="netBox">0.00</div>
    </div>
  </div>

  {{-- FOOTER --}}
 <div class="footer">
  <label class="flex items-center gap-2 text-white">
    <input type="checkbox" id="printAfterSave">
    Print after save
  </label>
  <button class="btn-save" type="submit">Save Ticket</button>
</div>
</div>
</form>

@if(session('ok'))
  <div class="container mt-3 alert alert-success">{{ session('ok') }}</div>
@endif

<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
const branchFerryMap = @json($ferryBoatsPerBranch ?? []);
const ferrySchedulesPerBranch = @json($ferrySchedulesPerBranch ?? []);
const ferryTimeInput = document.getElementById('ferryTimeInput');
const ferryTimeSelect = document.getElementById('ferryTimeSelect');
   
    const branchSelect = document.getElementById('branchSelect');
ferryTimeSelect?.addEventListener('change', function() {
    const selectedTime = this.value; // HH:mm
    if (!selectedTime) {
        ferryTimeInput.value = '';
        return;
    }

    // Convert to full datetime string: YYYY-MM-DDTHH:mm
    const today = new Date();
    const [hours, minutes] = selectedTime.split(':');
    today.setHours(hours, minutes, 0, 0);

    // Format as HTML datetime-local compatible
    const yyyy = today.getFullYear();
    const mm   = String(today.getMonth()+1).padStart(2,'0');
    const dd   = String(today.getDate()).padStart(2,'0');
    const hh   = String(today.getHours()).padStart(2,'0');
    const min  = String(today.getMinutes()).padStart(2,'0');

    ferryTimeInput.value = `${yyyy}-${mm}-${dd}T${hh}:${min}`;
});
document.getElementById('branchSelect')?.addEventListener('change', function() {
  const branchId = this.value;
// alert(branchId);
    const schedules = ferrySchedulesPerBranch[branchId] || [];
        const scheduleSelect = document.getElementById('ferryTimeSelect');
        scheduleSelect.innerHTML = '';
        // Default option
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
   // Default option
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
    e.preventDefault(); // stop normal submit

    let form = e.target;
    let formData = new FormData(form);


     // include checkbox state
  const shouldPrint = document.getElementById('printAfterSave')?.checked === true;
  formData.append('print', shouldPrint ? 1 : 0);


  

    axios.post(form.action, formData)
        .then(res => {
            if (res.data.ok) {

              // open printable page if requested
        if (shouldPrint && res.data.ticket_id) {
          window.open(`{{ url('/tickets') }}/${res.data.ticket_id}/print`, '_blank');
        }

//         if (shouldPrint && res.data.ticket_id) {
//   window.open(`{{ url('/tickets') }}/${res.data.ticket_id}/print?w=80`, '_blank');
// }



                Swal.fire({
                    icon: 'success',
                    title: 'Ticket Confirmed',
                    html: `
                   
                   
                    <b>Total:</b> ${res.data.total}`,
                    confirmButtonColor: '#49aa3d'
                });

                // ✅ reset form & totals
                form.reset();
                document.getElementById('totalBox').textContent = '0.00';
                document.getElementById('netBox').textContent = '0.00';
                document.getElementById('ticketLinesBody').innerHTML = '';
                // Add one empty row back
                const tr = document.createElement('tr');
                tr.innerHTML = `
                  <td><input class="ctrl" name="lines[0][item_id]" placeholder=""></td>
                  <td><input class="ctrl" name="lines[0][item_name]" placeholder="" readonly></td>
                  <td><input class="ctrl num" name="lines[0][qty]" type="number" step="1" min="0"></td>
                  <td><input class="ctrl num" name="lines[0][rate]" type="number" step="0.01" min="0"></td>
                  <td><input class="ctrl num" name="lines[0][levy]" type="number" step="0.01" min="0"></td>
                  <td><input class="ctrl num" name="lines[0][amount]" type="number" step="0.01" min="0" readonly></td>
                  <td><input class="ctrl" name="lines[0][vehicle_name]"></td>
                  <td><input class="ctrl" name="lines[0][vehicle_no]"></td>
                  <td><button type="button" class="btn-ghost btn-remove">✖</button></td>`;
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
                    confirmButtonColor: '#b3262e'
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Something went wrong while saving the ticket.',
                    confirmButtonColor: '#b3262e'
                });
            }
        });
});



(function() {
  const apiUrl = "{{ route('ajax.item-rates.find') }}";
  console.log("API URL:", "{{ route('ajax.item-rates.find') }}");
  const branchId = document.getElementById('branch_id')?.value || '';
  const tbody = document.querySelector('table.grid tbody');

  function clearRow(tr, opts = {}) {
    if (opts.clearId) tr.querySelector('input[name$="[item_id]"]').value = '';
    tr.querySelector('input[name$="[item_name]"]').value = '';
    tr.querySelector('input[name$="[rate]"]').value = '';
    tr.querySelector('input[name$="[levy]"]').value = '';
    tr.querySelector('input[name$="[amount]"]').value = '';
    // drop cache so next lookup will run
    tr.querySelector('input[name$="[item_id]"]').dataset.lastLookup = '';
    computeTotals();
  }

 async function lookupAndFill(tr, q) {
    if (!q) { clearRow(tr); return false; }

    const idInput = tr.querySelector('input[name$="[item_id]"]');
    
    // dynamically get branch_id
    let branchId = '';
    const branchSelect = document.getElementById('branchSelect');
    if (branchSelect) {
        branchId = branchSelect.value; // dynamic branch
    } else {
        branchId = document.getElementById('branch_id')?.value || '';
    }

    try {
        const url = new URL(apiUrl, window.location.origin);
        url.searchParams.set('q', q);
        if (branchId) url.searchParams.set('branch_id', branchId); // pass selected branch

        const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
        if (!res.ok) throw new Error('Lookup failed');
        const data = await res.json();

        tr.querySelector('input[name$="[item_name]"]').value = data.item_name ?? '';
        tr.querySelector('input[name$="[rate]"]').value      = (data.item_rate ?? 0).toFixed(2);
        tr.querySelector('input[name$="[levy]"]').value      = (data.item_lavy ?? 0).toFixed(2);

        idInput.dataset.lastLookup = q;
        computeRow(tr); // fill amount after rate/levy
        return true;
    } catch (e) {
        clearRow(tr); 
        const msg = 'Item not found';
        idInput.setCustomValidity(msg); idInput.reportValidity();
        setTimeout(()=> idInput.setCustomValidity(''), 1500);
        return false;
    }
}

  // Clear dependent fields as soon as ID changes (prevents stale display)
  tbody.addEventListener('input', (e) => {
    if (!e.target.name?.endsWith('[item_id]')) return;
    const tr = e.target.closest('tr');
    // if user edits ID and there were values shown, clear them immediately
    const hasValues =
      tr.querySelector('input[name$="[item_name]"]').value ||
      tr.querySelector('input[name$="[rate]"]').value ||
      tr.querySelector('input[name$="[levy]"]').value;
    if (hasValues) clearRow(tr); // optimistic clear
  });

  // ENTER or TAB on Item ID → fetch, then focus qty (or stay on id if failed)
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

  // Also fetch on blur (mouse click away)
  tbody.addEventListener('focusout', async (e) => {
    const input = e.target;
    if (!input.name?.endsWith('[item_id]')) return;
    const tr = input.closest('tr');
    await lookupAndFill(tr, input.value.trim());
  });

  // --- totals/math ---
  function computeRow(tr) {
    const qty   = parseFloat(tr.querySelector('input[name$="[qty]"]').value)  || 0;
    const rate  = parseFloat(tr.querySelector('input[name$="[rate]"]').value) || 0;
    const levy  = parseFloat(tr.querySelector('input[name$="[levy]"]').value) || 0;
    const amtEl = tr.querySelector('input[name$="[amount]"]');
    const amt   = qty * (rate + levy);
    amtEl.value = amt ? amt.toFixed(2) : '';
    computeTotals();
  }

  const totalBox = document.getElementById('totalBox');
  const netBox   = document.getElementById('netBox');
  const discountPct = document.querySelector('input[name="discount_pct"]');
  const discountRs  = document.querySelector('input[name="discount_rs"]');

  function computeTotals() {
    let total = 0;
    tbody.querySelectorAll('input[name$="[amount]"]').forEach(i => {
      total += parseFloat(i.value) || 0;
    });
    totalBox.textContent = total.toFixed(2);

    let net = total;
    const dPct = parseFloat(discountPct.value) || 0;
    const dRs  = parseFloat(discountRs.value)  || 0;
    if (dPct > 0) net -= (total * dPct / 100);
    if (dRs  > 0) net -= dRs;
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


// --- Add Row logic ---
  const tbodyEl = document.getElementById('ticketLinesBody');
  const btnAddRow = document.getElementById('btnAddRow');

  function nextRowIndex() {
    // safest: find max index from existing input name attributes
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
        <td><input class="ctrl" name="lines[${idx}][item_id]" placeholder=""></td>
        <td><input class="ctrl" name="lines[${idx}][item_name]" placeholder="" readonly></td>
        <td><input class="ctrl num" name="lines[${idx}][qty]" type="number" step="1" min="0"></td>
        <td><input class="ctrl num" name="lines[${idx}][rate]" type="number" step="0.01" min="0"></td>
        <td><input class="ctrl num" name="lines[${idx}][levy]" type="number" step="0.01" min="0"></td>
        <td><input class="ctrl num" name="lines[${idx}][amount]" type="number" step="0.01" min="0" readonly></td>
        <td><input class="ctrl" name="lines[${idx}][vehicle_name]"></td>
        <td><input class="ctrl" name="lines[${idx}][vehicle_no]"></td>
        <td><button type="button" class="btn-ghost btn-remove">✖</button></td>
      </tr>
    `;
  }
// ✅ delegate remove click to tbody
tbodyEl.addEventListener('click', (e) => {
  if (e.target.classList.contains('btn-remove')) {
    const tr = e.target.closest('tr');
    tr.remove();
    // recompute totals after removal
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

  // convenience: when user hits Enter on the last Amount field, add another row
  tbodyEl.addEventListener('keydown', (e) => {
    const isAmount = e.target.name?.endsWith('[amount]');
    if (isAmount && e.key === 'Enter') {
      e.preventDefault();
      addRow(true);
    }
  });

  setInterval(() => {
    window.location.reload();
  }, 60000);
</script>


  
@endsection
