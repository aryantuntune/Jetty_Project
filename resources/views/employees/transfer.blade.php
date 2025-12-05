@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="text-center text-danger mb-3">Transfer Employee: {{ $user->name }}</h2>

    <p>Current Branch: <strong>{{ $user->branch?->branch_name }}</strong></p>

    <div class="table-responsive" style="border:1px solid #007bff;">
        <table class="table table-bordered mb-0">
            <thead class="table-light" style="background-color:#f8f9fa; color:#b71c1c;">
                <tr>
                    <th>Field</th>
                    <th>Value</th>
                </tr>
            </thead>
            <tbody style="background-color:#e6f2e6;">
                <tr>
                    <td>Employee Name</td>
                    <td>{{ $user->name }}</td>
                </tr>
                <tr>
                    <td>Current Branch</td>
                    <td>{{ $user->branch?->branch_name }}</td>
                </tr>
                <tr>
                    <td>New Branch</td>
                    <td>
                        <form action="{{ route('employees.transfer.update', $user->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <select name="to_branch_id" class="form-select mb-2" required>
                                <option value="">-- Choose Branch --</option>
                                @foreach($branches as $branch)
                                <option value="{{ $branch->id }}">{{ $branch->branch_name }}</option>
                                @endforeach
                            </select>
                            @error('to_branch_id')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                            <button class="btn btn-primary">Transfer</button>
                            <a href="{{ route('employees.transfer.index') }}" class="btn btn-secondary">Cancel</a>
                        </form>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection
