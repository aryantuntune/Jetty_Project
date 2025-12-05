@extends('layouts.app')

@section('content')
<style>
  :root{
    --win-border:#a9a9a9;
    --title-red:#b22222;
    --panel-bg:#f5f7fa;
    --strip-bg:#eef2f6;
    --grid-bg:#ecfff1;         /* pale green like screenshot */
    --grid-alt:#e2ffe9;
    --grid-border:#cdd9c5;
    --grid-head:#f0f0f0;
    --blue-select:#0b61d6;     /* row highlight blue */
    --footer-red:#b3262e;
    --add-green:#49aa3d;
  }
  .irs-window{
    max-width: 1120px;
    margin: 18px auto 32px;
    border: 1px solid var(--win-border);
    border-radius: 6px;
    background: #fff;
    box-shadow: 0 1px 0 #fff inset, 0 2px 10px rgba(0,0,0,.04);
    overflow: hidden;
  }
  .irs-title{
    text-align:center;
    font-weight:700;
    font-size:20px;
    color:var(--title-red);
    padding:12px 14px 10px;
    border-bottom:1px solid var(--win-border);
    background:#fff;
  }
  .irs-strip{
    background: var(--panel-bg);
    border-bottom:1px solid var(--win-border);
    padding: 14px 16px;
  }
  .irs-row{
    display:grid;
    grid-template-columns: 140px 1fr 120px 120px;
    gap:10px;
    align-items:center;
  }
  .irs-label{font-size:13px;color:#444;}
  .irs-input, .irs-select{
    width:100%;
    border:1px solid #c9c9c9;
    border-radius:4px;
    background:#fff;
    padding:8px 10px;
    font-size:14px;
  }
  .irs-grid-wrap{
    max-height: 520px; /* scroll like desktop app */
    overflow:auto;
    border-top:1px solid var(--win-border);
    border-bottom:1px solid var(--win-border);
  }
  table.irs-grid{
    width:100%;
    border-collapse:collapse;
    background: var(--grid-bg);
  }
  .irs-grid thead th{
    position: sticky; top:0;
    background: var(--grid-head);
    font-size:13px; font-weight:700; color:#3a3a3a;
    border-bottom:1px solid var(--grid-border);
    padding:8px 10px;
    text-transform:none;
  }
  .irs-grid tbody td{
    border-bottom:1px solid var(--grid-border);
    padding:8px 10px;
    font-size:13px; color:#222;
    white-space:nowrap;
  }
  .irs-grid tbody tr:nth-child(even){ background: var(--grid-alt); }
  .irs-grid tbody tr:hover{ outline:2px solid var(--blue-select); outline-offset:-2px; }
  .irs-footer{
    display:flex; align-items:center; justify-content:space-between;
    background: var(--footer-red);
    padding:10px 12px;
  }
  .btn-add{
    background: var(--add-green);
    color:#fff; font-weight:600; font-size:13px;
    border:none; border-radius:4px; padding:8px 12px;
  }
  .btn-add:hover{ filter:brightness(0.95); }
  .btn-ghost{
    background: rgba(255,255,255,.85);
    border:1px solid rgba(0,0,0,.15);
    border-radius:6px;
    width:34px;height:28px; display:inline-flex; align-items:center; justify-content:center;
  }
  .btn-small{ padding:4px 8px; font-size:12px; }
  .text-end{ text-align:right; }
</style>

<div class="irs-window">
  <div class="irs-title">Item Rate Slabs</div>

  {{-- Top strip (Branch Name dropdown + Branch ID display) --}}
  <div class="irs-strip">
    <form method="get">
      <div class="irs-row">
        <div class="irs-label">Branch Name :</div>
        <div>
         <select class="irs-select" name="branch_id" onchange="this.form.submit()">
    @if(in_array(auth()->user()->role_id, [1,2]))
        <option value="">— All Branches —</option>
    @endif
    @foreach($branches as $b)
        <option value="{{ $b->id }}" @selected(request('branch_id')==$b->id)>{{ $b->branch_name }}</option>
    @endforeach
</select>


        </div>
        {{-- <div class="irs-label text-end">Branch ID :</div>
        <div>
          <input class="irs-input" value="{{ request('branch_id') ?? ($branches?->firstWhere('id', request('branch_id'))?->id ?? '') }}" readonly>
        </div> --}}
      </div>
    </form>
  </div>

  {{-- Grid --}}
  <div class="irs-grid-wrap">
    <table class="irs-grid">
     <thead>
  <tr>
    <th style="width:140px">Starting Date</th>
    <th>Item Id</th>
    <th>Item Name</th>
    <th>Item Category Name</th>
    <th>Branch Name</th>
    <th style="width:120px" class="text-end">Item Rate</th>
    <th style="width:120px" class="text-end">Item Levy</th>
      @if(in_array(auth()->user()->role_id, [1,2]))
    <th style="width:120px" class="text-center">Actions</th> 
    @endif
  </tr>
</thead>
<tbody>
  @forelse($itemRates as $r)
    <tr>
      <td>{{ $r->starting_date?->format('d/m/Y') }}</td>
      <td>{{ $r->item_id }}</td>
      <td class="fw-semibold">{{ strtoupper($r->item_name) }}</td>
      <td>{{ strtoupper($r->category->category_name ?? '—') }}</td>     
      <td>{{ strtoupper($r->branch->branch_name ?? '—') }}</td>
      <td class="text-end">{{ number_format($r->item_rate,2) }}</td>
      <td class="text-end">{{ number_format($r->item_lavy,2) }}</td>
      @if(in_array(auth()->user()->role_id, [1,2]))
      <td class="text-center">
          {{-- Edit Button --}}
          <a href="{{ route('item-rates.edit', $r) }}" 
             class="btn-small" 
             style="background:#0b61d6;color:#fff;border-radius:4px;padding:5px 10px;text-decoration:none;margin-right:4px;">
             Edit
          </a>

          {{-- Delete Button --}}
          <form action="{{ route('item-rates.destroy', $r) }}" method="POST" style="display:inline;">
            @csrf
            @method('DELETE')
            <button type="submit" 
                    onclick="return confirm('Are you sure you want to delete this item rate?')" 
                    class="btn-small" 
                    style="background:#b3262e;color:#fff;border-radius:4px;padding:5px 10px;border:none;">
              Delete
            </button>
          </form>
        @endif
      </td>
    </tr>
  @empty
    <tr><td colspan="8" style="text-align:center;padding:20px;">No records found.</td></tr>
  @endforelse
</tbody>


    </table>
  </div>

  {{-- Footer bar with Add button and a small icon button on right --}}
  <div class="irs-footer">
    @if(in_array(auth()->user()->role_id, [1,2]))
    <a href="{{ route('item-rates.create') }}" class="btn-add">Add New Rate Slab</a>
    <button type="button" class="btn-ghost" title="Options">
      {{-- simple icon (i) --}}
      <span style="font-weight:700;">i</span>
    </button>
    @endif
  </div>
</div>

{{-- pagination (outside the window) --}}
<div style="max-width:1120px;margin:10px auto 0;">
  {{ $itemRates->links() }}
</div>
@endsection
