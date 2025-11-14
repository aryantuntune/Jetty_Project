@extends('layouts.app')

@section('content')
<style>
    .custom-table thead th { background: #f8f9fa; font-weight:bold; }
    .custom-table tbody tr { background:#eafbea; }
    .custom-table tbody tr:nth-child(even){ background:#f4fff4; }
    .custom-table tbody tr:hover{ background:#cce5ff; }
    .custom-table tbody tr.active{ background:#003366;color:#fff; }
    .table-footer{ background:#800000;color:white;font-weight:bold;padding:10px; }
    .list-window{ max-width:1120px;margin:18px auto 32px;border:1px solid #a9a9a9;
        border-radius:6px;background:#fff;box-shadow:0 2px 10px rgba(0,0,0,.04);overflow:hidden;}
    .list-body{ padding:16px; }
</style>

<div class="container list-window">
    <div class="list-body">

        <div class="d-flex justify-content-between mb-3">
            <h2 class="text-danger">Checkers</h2>
            <a href="{{ route('checker.create') }}" class="btn btn-primary">Add Checker</a>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <table class="table table-bordered custom-table">
            <thead>
                <tr>
                    <th>Name</th><th>Email</th><th>Mobile</th><th>Branch</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($checkers as $checker)
                    <tr>
                        <td>{{ $checker->name }}</td>
                        <td>{{ $checker->email }}</td>
                        <td>{{ $checker->mobile ?? 'N/A' }}</td>
                        <td>{{ $checker->branch?->branch_name ?? 'N/A' }}</td>

                        <td>
                            <a href="{{ route('checker.edit', $checker) }}" class="btn btn-warning btn-sm">Edit</a>

                            <form action="{{ route('checker.destroy', $checker) }}"
                                  method="POST" style="display:inline-block;">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm"
                                    onclick="return confirm('Delete this checker?')">
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center">No checkers found</td></tr>
                @endforelse
            </tbody>
        </table>

        <div class="table-footer d-flex justify-content-between">
            <span>Total Checkers: {{ $checkers->total() }}</span>
            <div>{{ $checkers->links() }}</div>
        </div>

    </div>
</div>

<script>
document.querySelectorAll('.custom-table tbody tr').forEach(tr=>{
    tr.addEventListener('click',function(){
        document.querySelectorAll('.custom-table tbody tr')
            .forEach(r=>r.classList.remove('active'));
        this.classList.add('active');
    });
});
</script>
@endsection
