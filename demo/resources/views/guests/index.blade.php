@extends('layouts.app')

@section('content')
<style>
    :root {
        --win-border: #a9a9a9;
        --title-red: #b22222;
        --panel-bg: #f8fafc;
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
        box-shadow: 0 1px 0 #fff inset, 0 2px 10px rgba(0,0,0,.04);
        overflow: hidden;
    }
    .irs-title {
        text-align: center;
        font-weight: 700;
        font-size: 20px;
        color: var(--title-red);
        padding: 12px 14px 10px;
        border-bottom: 1px solid var(--win-border);
        background: #fff;
    }
    .irs-strip {
        background: var(--panel-bg);
        border-bottom: 1px solid var(--win-border);
        padding: 14px 16px;
    }
    .irs-row {
        display: flex;
        gap: 10px;
        align-items: center;
    }
    .irs-label { font-size: 13px; color: #444; }
    .irs-select {
        width: 200px;
        border: 1px solid #c9c9c9;
        border-radius: 4px;
        background: #fff;
        padding: 6px 10px;
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
        text-transform: none;
    }
    .irs-grid tbody td {
        border-bottom: 1px solid var(--grid-border);
        padding: 8px 10px;
        font-size: 13px;
        color: #222;
        white-space: nowrap;
    }
    .irs-grid tbody tr:nth-child(even) { background: var(--grid-alt); }
    .irs-grid tbody tr:hover { outline:2px solid var(--blue-select); outline-offset:-2px; }
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
        text-decoration: none;
    }
    .btn-add:hover { filter: brightness(0.95); }
    .btn-small {
        padding: 4px 8px;
        font-size: 12px;
    }
    .text-end { text-align: right; }
</style>

<div class="irs-window">
    <!-- Title -->
    <div class="irs-title">Guest List</div>

    <!-- Filter -->
    <div class="irs-strip">
        <form method="GET" action="{{ route('guests.index') }}">
            <div class="irs-row">
                <div class="irs-label">Branch:</div>
                <select name="branch_id" class="irs-select" onchange="this.form.submit()">
                    @if(in_array($user->role_id, [1,2]))
                        <option value="">— All Branches —</option>
                    @endif
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}" {{ $branchId == $branch->id ? 'selected' : '' }}>
                            {{ $branch->branch_name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="irs-grid-wrap">
        <table class="irs-grid">
            <thead>
                <tr>
                    <th style="width:5%">#</th>
                    <th style="width:30%">Name</th>
                    <th style="width:30%">Category</th>
                    <th style="width:25%">Branch</th>
                     @if(in_array($user->role_id, [1,2]))
                    <th style="width:10%">Actions</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @forelse($guests as $guest)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $guest->name }}</td>
                        <td>{{ $guest->category->name ?? 'N/A' }}</td>
                        <td>{{ $guest->branch->branch_name ?? '-' }}</td>
                        @if(in_array($user->role_id, [1,2]))
                        <td class="text-center">
                                <a href="{{ route('guests.edit', $guest->id) }}" class="btn btn-sm btn-primary">Edit</a>

                                <form action="{{ route('guests.destroy', $guest->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                                </form>
                            @endif
                        </td>

                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center">No guests found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Footer -->
    <div class="irs-footer">
        <span>Total: {{ $total }}</span>
        <a href="{{ route('guests.create') }}" class="btn-add">Add New Guest</a>
    </div>
</div>
@endsection
