@extends('layouts.app')

@section('content')

<!-- Bootstrap 5 + Bootstrap-Select CSS/JS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css">

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js"></script>

<style>
  :root{
    --win-border:#a9a9a9;
    --title-red:#b22222;
    --panel-bg:#f5f7fa;
    --strip-bg:#eef2f6;
    --grid-bg:#ecfff1;
    --grid-border:#cdd9c5;
    --grid-head:#f0f0f0;
    --footer-red:#b3262e;
    --add-green:#49aa3d;
  }
  .irs-window{
    max-width: 1120px; margin: 18px auto 32px;
    border:1px solid var(--win-border); border-radius:6px; background:#fff;
    box-shadow:0 2px 10px rgba(0,0,0,.04); overflow:hidden;
  }
  .irs-title{
    text-align:center; font-weight:700; font-size:20px; color:var(--title-red);
    padding:12px 14px 10px; border-bottom:1px solid var(--win-border);
  }
  .irs-form-wrap{ background:var(--grid-bg); border-top:1px solid var(--win-border); border-bottom:1px solid var(--win-border); }
  .irs-form{
    padding: 12px 14px;
    display:grid; grid-template-columns: 1fr 1fr 1fr; gap:14px;
  }
  .irs-field{ background:#fff; border:1px solid var(--grid-border); border-radius:6px; padding:10px 10px 8px; }
  .irs-field label{ display:block; font-size:12px; color:#333; margin-bottom:6px; font-weight:700; }
  .irs-field .hint{ font-size:11px; color:#666; margin-top:4px; }
  .irs-errors{ padding:10px 16px; color:#721c24; background:#f8d7da; border-bottom:1px solid #f5c6cb; }
  .irs-footer{ display:flex; align-items:center; justify-content:space-between; background:var(--footer-red); padding:10px 12px; }
  .btn-save{ background:var(--add-green); color:#fff; font-weight:700; border:none; border-radius:4px; padding:9px 14px; }
  .btn-cancel{ background:#fff; border:1px solid rgba(0,0,0,.15); border-radius:4px; padding:8px 12px; }
</style>

<div class="irs-window">
  <div class="irs-title">Add Item Rate Slab</div>

  {{-- validation messages --}}
  @if ($errors->any())
    <div class="irs-errors">
      <strong>Fix the following:</strong>
      <ul class="mb-0">
        @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
      </ul>
    </div>
  @endif

  {{-- FORM AREA --}}
  <form method="post" action="{{ route('item-rates.store') }}">
    @csrf

    {{-- Branch Multi-Select --}}
   <div class="mb-3 row">
    <label class="col-sm-2 col-form-label">Route :</label>
    <div class="col-sm-10">
        <select id="routeSelect" name="route_id" class="form-control" required>
            <option value="">-- Select Route --</option>
            @foreach($routes as $r)
                <option value="{{ $r->route_id }}" data-branches="{{ $r->branch_ids }}">
                    {{ $r->branch_names }}
                </option>
            @endforeach
        </select>
        {{-- <input type="hidden" name="branch_id[]" id="branchHidden"> --}}
    </div>
</div>


    <div class="irs-form-wrap">
        <div class="irs-form">
            {{-- Item Name --}}
            <div class="irs-field" style="grid-column: span 2;">
                <label>Item Name</label>
                <input type="text" name="item_name" class="irs-input form-control"
                       value="{{ old('item_name') }}" required>
            </div>

            {{-- Item ID --}}
            <div class="irs-field">
                <label>Item ID</label>
                <input type="number" name="item_id" class="irs-input form-control" 
                       value="{{ old('item_id') }}" required min="1">
                <div class="hint">Enter the numeric ID of the item (e.g., 1, 2, 3...)</div>
            </div>

            {{-- Category --}}
            <div class="irs-field">
                <label>Item Category</label>
                <select name="item_category_id" class="irs-select form-control">
                  <option value="">— Select —</option>
                  @foreach($categories ?? [] as $c)
                    <option value="{{ $c->id }}" @selected(old('item_category_id')==$c->id)>
                      {{ $c->category_name }}
                    </option>
                  @endforeach
                </select>
            </div>

            {{-- Item Rate --}}
            <div class="irs-field">
                <label>Item Rate</label>
                <input type="number" step="0.01" min="0" name="item_rate" class="irs-input form-control"
                       value="{{ old('item_rate',0) }}" required>
            </div>

            {{-- Item Levy --}}
            <div class="irs-field">
                <label>Item Levy</label>
                <input type="number" step="0.01" min="0" name="item_lavy" class="irs-input form-control"
                       value="{{ old('item_lavy',0) }}" required>
            </div>

            {{-- Starting Date --}}
            <div class="irs-field">
                <label>Starting Date</label>
                <input type="date" name="starting_date" class="irs-input form-control"
                       value="{{ old('starting_date') }}" required>
                <div class="hint">Date from which this slab becomes effective.</div>
            </div>

            {{-- Ending Date --}}
            <div class="irs-field">
                <label>Ending Date (optional)</label>
                <input type="date" name="ending_date" class="irs-input form-control"
                       value="{{ old('ending_date') }}">
                <div class="hint">Leave blank if still effective.</div>
            </div>
        </div>
    </div>

    <div class="irs-footer">
        <a href="{{ route('item-rates.index') }}" class="btn-cancel">Cancel</a>
        <button class="btn-save" type="submit">Save Rate Slab</button>
    </div>
    <div id="branchHiddenContainer"></div>

  </form>
</div>
<script>
    $(document).ready(function () {
        // Initialize bootstrap-select
        $('.selectpicker').selectpicker();
    });
  document.addEventListener("DOMContentLoaded", function () {
    let routeSelect = document.getElementById('routeSelect');

    function populateBranches() {
        let selected = routeSelect.options[routeSelect.selectedIndex];
        let branchIds = selected.getAttribute('data-branches'); // e.g. "1,2"

        // Clear previous hidden inputs
        document.querySelectorAll('#branchHiddenContainer input').forEach(e => e.remove());

        if (branchIds) {
            branchIds.split(',').forEach(id => {
                let input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'branch_id[]';
                input.value = id;
                document.getElementById('branchHiddenContainer').appendChild(input);
            });
        }
    }

    // Run once on load (covers case when route is preselected or old() has a value)
    populateBranches();

    // Run again whenever the dropdown changes
    routeSelect.addEventListener('change', populateBranches);
});
</script>

@endsection
