@extends('layouts.app')

@section('content')
<style>
  /* --- Page look & feel to match screenshot --- */
  .rate-page h3.page-title{
    color:#b31217;               /* red title */
    font-weight:700;
    text-align:center;
    margin-bottom:18px;
  }
  .filter-row{
    background:#f8f9fa;
    border:1px solid #e5e7eb;
    border-radius:.4rem;
    padding:14px 16px;
    display:flex;align-items:center;gap:12px;
  }
  .filter-row label{margin:0;font-weight:600;color:#444}
  .filter-row .form-select{
    min-width:320px;
  }
  table.rate-table{
    width:100%;
    border:1px solid #dee2e6;
    border-radius:.4rem;
    overflow:hidden;
    background:#fff;
  }
  table.rate-table thead th{
    background:#f3f4f6;          /* light header like screenshot */
    color:#333;
    font-weight:700;
    border-bottom:1px solid #e5e7eb;
    padding:10px 12px;
    white-space:nowrap;
  }
  table.rate-table tbody td{
    padding:10px 12px;
    vertical-align:middle;
    border-top:1px solid #eef2f7;
  }
  /* mint green zebra rows */
  table.rate-table tbody tr:nth-child(odd){ background:#eaffef;}
  table.rate-table tbody tr:nth-child(even){ background:#e1ffe8;}

  /* Buttons to match screenshot */
  .btn-edit{ background:#0d6efd; color:#fff; }
  .btn-edit:hover{ filter:brightness(.95); color:#a31622;}
  .btn-delete{ background:#dc3545; color:#fff; }
  .btn-delete:hover{ filter:brightness(.95); color:#a31622;}

  /* Footer action bar */
  .footer-bar{
    margin-top:0;
    background:#a31622;          /* dark red bar */
    padding:14px 16px;
    border-radius:.4rem;
    display:flex;align-items:center;justify-content:space-between;
  }
  .btn-add{
    background:#28a745;          /* green add button */
    color:#fff;
    font-weight:600;
    border:none;
    padding:8px 14px;
    border-radius:.4rem;
  }
  .btn-add:hover{ filter:brightness(.95); color:#fff;}
  .info-pill{
    width:36px;height:36px;border-radius:.6rem;
    background:#f3f4f6;border:none;color:#000;font-weight:700;
  }
</style>

<div class="container mt-4 rate-page">

  <h3 class="page-title">Special Charges</h3>

  {{-- Filter row (Branch Name + Filter/All) --}}
 <div class="filter-row mb-3">
  <form method="GET" action="{{ route('special-charges.index') }}"
        class="d-flex align-items-center gap-2 flex-nowrap">
    <label for="branch_id" class="mb-0 fw-semibold">Branch Name :</label>

    <select id="branch_id" name="branch_id" class="form-select"
            style="width:320px;max-width:100%;">
      <option value="">— All Branches —</option>
      @foreach($branches as $branch)
        <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
          {{ $branch->branch_name }}
        </option>
      @endforeach
    </select>

    <button type="submit" class="btn btn-secondary">Filter</button>
    <a href="{{ route('special-charges.index') }}" class="btn btn-link">All</a>
  </form>
</div>


  {{-- Table --}}
  <div class="table-responsive">
    <table class="rate-table">
      <thead>
        <tr>
          <th style="width:80px;">ID</th>
          <th>Branch Name</th>
          <th>Special Charge</th>
          <th>Created At</th>
          <th style="width:140px;">Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($charges as $charge)
          <tr>
            <td>{{ $charge->id }}</td>
            <td>{{ $charge->branch->branch_name ?? 'N/A' }}</td>
            <td>₹ {{ number_format($charge->special_charge, 2) }}</td>
            <td>{{ optional($charge->created_at)->format('d/m/Y') }}</td>
            <td>
              <a href="{{ route('special-charges.edit', $charge->id) }}" class="btn btn-sm btn-edit">Edit</a>
              <form action="{{ route('special-charges.destroy', $charge->id) }}" method="POST" class="d-inline">
                @csrf
                @method('DELETE')
                <button class="btn btn-sm btn-delete" onclick="return confirm('Delete this charge?')">Delete</button>
              </form>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="5" class="text-center py-4">No special charges found.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  {{-- Footer action bar (dark red) --}}
  <div class="footer-bar">
    <a href="{{ route('special-charges.create') }}" class="btn btn-add">Add New Special Charge</a>
    {{-- <button type="button" class="info-pill">i</button> --}}
  </div>

</div>
@endsection
