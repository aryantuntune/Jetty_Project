@extends('layouts.app')

@section('content')
<style>
    /* Custom table styling (same as reference/other pages) */
    .custom-table thead th {
        background: #f8f9fa;
        font-weight: bold;
    }
    .custom-table tbody tr {
        background-color: #eafbea; /* light green */
    }
    .custom-table tbody tr:nth-child(even) {
        background-color: #f4fff4; /* alternate green */
    }
    .custom-table tbody tr:hover {
        background-color: #cce5ff; /* hover blue highlight */
    }
    .custom-table tbody tr.active {
        background-color: #003366; /* dark blue selected row */
        color: #fff;
    }
    .table-footer {
        background: #800000; /* maroon footer bar */
        color: white;
        font-weight: bold;
        padding: 10px;
    }

    /* Window/card container for consistent layout */
    .list-window{
        max-width:1120px; margin:18px auto 32px;
        border:1px solid #a9a9a9; border-radius:6px; background:#fff;
        box-shadow:0 2px 10px rgba(0,0,0,.04); overflow:hidden;
    }
    .list-body{ padding:16px; }
</style>

<div class="container list-window">
    <div class="list-body">
        <div class="d-flex justify-content-between mb-3">
            <h2 class="text-danger">Administrators</h2>
            <a href="{{ route('admin.create') }}" class="btn btn-primary">Add Administrator</a>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <table class="table table-bordered custom-table">
            <thead>
                <tr>
                    <th>Name</th><th>Email</th><th>Mobile</th>
                    {{-- <th>Branch</th> --}}
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($administrators as $admin)
                    <tr>
                        <td>{{ $admin->name }}</td>
                        <td>{{ $admin->email }}</td>
                        <td>{{ $admin->mobile }}</td>
                        {{-- <td>{{ $admin->branch?->branch_name }}</td> --}}
                        {{-- <td>{{ $admin->ferryboat?->name }}</td> --}}
                        <td>
                            {{-- <a href="{{ route('admin.show', $admin) }}" class="btn btn-info btn-sm">View</a> --}}
                            <a href="{{ route('admin.edit', $admin) }}" class="btn btn-warning btn-sm">Edit</a>
                            <form action="{{ route('admin.destroy', $admin) }}" method="POST" style="display:inline-block;">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm"
                                        onclick="return confirm('Delete this administrator?')">
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Footer with pagination -->
        <div class="table-footer d-flex justify-content-between">
            <span>Total Administrators: {{ $administrators->total() }}</span>
            <div>{{ $administrators->links() }}</div>
        </div>
    </div>
</div>

<script>
    // Keep clicked row highlighted (consistent behavior)
    document.querySelectorAll('.custom-table tbody tr').forEach(row => {
        row.addEventListener('click', function() {
            document.querySelectorAll('.custom-table tbody tr').forEach(r => r.classList.remove('active'));
            this.classList.add('active');
        });
    });
</script>
@endsection
