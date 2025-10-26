@extends('layouts.app')

@section('content')
<style>
:root {
    --win-border: #a9a9a9;
    --title-red: #b22222;
    --panel-bg: #f5f7fa;
    --grid-bg: #ecfff1;
    --grid-alt: #e2ffe9;
    --grid-border: #cdd9c5;
    --grid-head: #f0f0f0;
    --blue-select: #0b61d6;
    --footer-red: #b3262e;
    --add-green: #49aa3d;
}

.irs-window {
    max-width: 1120px;
    margin: 18px auto 32px;
    border: 1px solid var(--win-border);
    border-radius: 6px;
    background: #fff;
    box-shadow: 0 2px 10px rgba(0, 0, 0, .04);
    overflow: hidden;
}

.irs-title {
    text-align: center;
    font-weight: 700;
    font-size: 20px;
    color: var(--title-red);
    padding: 12px 14px 10px;
    border-bottom: 1px solid var(--win-border);
}

.irs-strip {
    background: var(--panel-bg);
    border-bottom: 1px solid var(--win-border);
    padding: 14px 16px;
}

.irs-row {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    align-items: center;
    margin-bottom: 10px;
}

.irs-label {
    font-size: 13px;
    color: #444;
    min-width: 80px;
}

.irs-input {
    flex: 1 1 auto;
    min-width: 180px;
    border: 1px solid #c9c9c9;
    border-radius: 4px;
    background: #fff;
    padding: 8px 10px;
    font-size: 14px;
}

.irs-grid-wrap {
    max-height: 520px;
    overflow: auto;
    border-top: 1px solid var(--win-border);
    border-bottom: 1px solid var(--win-border);
}

table.irs-grid {
    width: 100%;
    border-collapse: collapse;
    background: var(--grid-bg);
}

.irs-grid thead th {
    position: sticky;
    top: 0;
    background: var(--grid-head);
    font-size: 13px;
    font-weight: 700;
    color: #3a3a3a;
    border-bottom: 1px solid var(--grid-border);
    padding: 8px 10px;
}

.irs-grid tbody td {
    border-bottom: 1px solid var(--grid-border);
    padding: 8px 10px;
    font-size: 13px;
    color: #222;
    white-space: nowrap;
}

.irs-grid tbody tr:nth-child(even) {
    background: var(--grid-alt);
}

.irs-grid tbody tr:hover {
    outline: 2px solid var(--blue-select);
    outline-offset: -2px;
}

.irs-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: var(--footer-red);
    padding: 10px 12px;
}

.btn-add {
    background: var(--add-green);
    color: #fff;
    font-weight: 600;
    font-size: 13px;
    border: none;
    border-radius: 4px;
    padding: 8px 12px;
}

.btn-light {
    background: #fff;
    border: 1px solid rgba(0, 0, 0, .15);
    border-radius: 4px;
    padding: 8px 12px;
    cursor: pointer;
}

.pagination-wrap {
    max-width: 1120px;
    margin: 10px auto 0;
}
</style>

<div class="irs-window">
    <div class="irs-title">Items (From Item Rates)</div>

    {{-- Search strip --}}
    <div class="irs-strip">
        <form method="get">
            {{-- Row 1 --}}
            <div class="irs-row">
                <div class="irs-label">Item ID :</div>
                <input type="number" name="id" value="{{ request('id') }}" class="irs-input" placeholder="e.g. 1">
            </div>

            {{-- Row 2 --}}
            <div class="irs-row">
                <div class="irs-label">Item Name :</div>
                <input type="text" name="name" value="{{ request('name') }}" class="irs-input" placeholder="Search item name…">
            </div>

            {{-- Row 3 --}}
            <div class="irs-row">
                <div class="irs-label">Branch :</div>
                <select name="branch_id" class="irs-input">
                     @if(in_array(auth()->user()->role_id, [1,2]))
                        <option value="">All Branches</option>
                    @endif
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                            {{ $branch->branch_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Row 4: Buttons --}}
            <div class="irs-row text-end">
                <button class="btn-light" type="submit">Search</button>
                <a class="btn-light" href="{{ route('items.from_rates.index') }}">Reset</a>
            </div>
        </form>
    </div>

    {{-- Grid --}}
    <div class="irs-grid-wrap">
        <table class="irs-grid">
            <thead>
                <tr>
                    <th>Id</th>
                    <th>Item Name</th>
                    <th>Item Category Name</th>
                    <th>Branch</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $row)
                    <tr>
                        <td>{{ $row->item_id }}</td>
                        <td>{{ $row->item_name }}</td>
                        <td>{{ $row->category_name ?? '—' }}</td>
                        <td>{{ $row->branch_name ?? '—' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="text-align:center;padding:20px;">No items found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="irs-footer">
        <span style="color:#fff;font-weight:600;">Derived from Item Rates</span>
           @if(in_array(auth()->user()->role_id, [1,2,3]))
        <a href="{{ route('item-rates.create') }}" class="btn-add">Add Item Rate Slab</a>
        @endif
    </div>
</div>

<div class="pagination-wrap">
    {{ $items->onEachSide(1)->links('pagination::bootstrap-5') }}
</div>
@endsection
