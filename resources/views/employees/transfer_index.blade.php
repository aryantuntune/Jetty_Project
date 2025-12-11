@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="text-center text-danger mb-3">Employee Transfer</h2>

    <div class="table-responsive" style="border:1px solid #007bff;">
        <table class="table table-bordered mb-0">
            <thead class="table-light" style="background-color:#f8f9fa; color:#b71c1c;">
                <tr>
                    <th>Name</th>
                    <th>Current Branch</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                <tr style="background-color:#e6f2e6;">
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->branch?->branch_name }}</td>
                    <td>
                        <a href="{{ route('employees.transfer.form', $user->id) }}" class="btn btn-sm btn-primary">
                            Transfer
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="background-color:#8b0000; color:white;">
                    <td colspan="3" class="text-end px-3">Total: {{ $users->count() }}</td>
                </tr>
            </tfoot>

        </table>
    </div>
</div>
@endsection
 