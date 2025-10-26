@extends('layouts.app')

@section('content')
<style>
    /* Consistent window/card container like your reference pages */
    .list-window{
        max-width:1120px; margin:18px auto 32px;
        border:1px solid #a9a9a9; border-radius:6px; background:#fff;
        box-shadow:0 2px 10px rgba(0,0,0,.04); overflow:hidden;
    }
    .list-body{ padding:16px; }

    /* Title uses the same red as .text-danger from Bootstrap */
    .page-title { color:#dc3545; margin:0; }

    /* Form consistency */
    .form-label { font-weight:600; }
</style>

<div class="container list-window">
    <div class="list-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="page-title">Edit Manager</h2>
            <a href="{{ route('manager.index') }}" class="btn btn-secondary">↩ Back to List</a>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>Please fix the errors below.</strong>
            </div>
        @endif

        <form action="{{ route('manager.update', $manager) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label class="form-label">Name</label>
                <input type="text" name="name" value="{{ old('name', $manager->name) }}" class="form-control" required>
                @error('name') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" value="{{ old('email', $manager->email) }}" class="form-control" required>
                @error('email') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Password <small class="text-muted">(leave blank if unchanged)</small></label>
                <input type="password" name="password" class="form-control">
                @error('password') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Mobile</label>
                <input type="text" name="mobile" value="{{ old('mobile', $manager->mobile) }}" class="form-control">
                @error('mobile') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Branch</label>
                <select name="branch_id" class="form-select">
                    <option value="">-- Select Branch --</option>
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}"
                            {{ (string) old('branch_id', $manager->branch_id) === (string) $branch->id ? 'selected' : '' }}>
                            {{ $branch->branch_name }}
                        </option>
                    @endforeach
                </select>
                @error('branch_id') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            {{-- <div class="mb-3">
                <label class="form-label">Ferryboat</label>
                <select name="ferryboat_id" class="form-select">
                    <option value="">-- Select Ferryboat --</option>
                    @foreach($ferryboats as $ferryboat)
                        <option value="{{ $ferryboat->id }}"
                            {{ (string) old('ferryboat_id', $manager->ferryboat_id) === (string) $ferryboat->id ? 'selected' : '' }}>
                            {{ $ferryboat->name }}
                        </option>
                    @endforeach
                </select>
                @error('ferryboat_id') <small class="text-danger">{{ $message }}</small> @enderror
            </div> --}}

            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('manager.index') }}" class="btn btn-secondary">↩ Cancel</a>
                <button type="submit" class="btn btn-primary">✔ Update</button>
            </div>
        </form>
    </div>
</div>
@endsection
