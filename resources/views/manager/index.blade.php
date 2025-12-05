@extends('layouts.app')

@section('content')
<style>
    /* Custom table styling (copied from reference) */
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
        background: #800000;
        color: white;
        font-weight: bold;
        padding: 10px;
    }

    /* Optional card container similar to reference window */
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
            <h2 class="text-danger">Managers</h2>
            <a href="{{ route('manager.create') }}" class="btn btn-primary">Add Manager</a>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <table class="table table-bordered custom-table">
            <thead>
                <tr>
                    <th>Name</th><th>Email</th><th>Mobile</th>
                    <th>Branch</th>
                    {{-- <th>Ferryboat</th> --}}
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($managers as $manager)
                    <tr>
                        <td>{{ $manager->name }}</td>
                        <td>{{ $manager->email }}</td>
                        <td>{{ $manager->mobile ?? 'N/A' }}</td>
                        <td>{{ $manager->branch?->branch_name ?? 'N/A' }}</td>
                        {{-- <td>{{ $manager->ferryboat?->name ?? 'N/A' }}</td> --}}
                        <td>
                            {{-- <a href="{{ route('manager.show', $manager) }}" class="btn btn-info btn-sm">View</a> --}}
                            <a href="{{ route('manager.edit', $manager) }}" class="btn btn-warning btn-sm">Edit</a>
                            <form action="{{ route('manager.destroy', $manager) }}" method="POST" style="display:inline-block;">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm"
                                        onclick="return confirm('Delete this manager?')">
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center">No managers found</td></tr>
                @endforelse
            </tbody>
        </table>

        <!-- Footer with pagination (same maroon bar style) -->
        <div class="table-footer d-flex justify-content-between">
            <span>Total Managers: {{ $managers->total() }}</span>
            <div>{{ $managers->links() }}</div>
        </div>
    </div>
</div>

<script>
    // Keep clicked row highlighted (same as reference)
    document.querySelectorAll('.custom-table tbody tr').forEach(row => {
        row.addEventListener('click', function() {
            document.querySelectorAll('.custom-table tbody tr').forEach(r => r.classList.remove('active'));
            this.classList.add('active');
        });
    });
</script>
@endsection
