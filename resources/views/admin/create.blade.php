@extends('layouts.app')

@section('content')
<style>
    /* Keep the same palette & feel as your reference listing pages */
    .list-window{
        max-width:1120px; margin:18px auto 32px;
        border:1px solid #a9a9a9; border-radius:6px; background:#fff;
        box-shadow:0 2px 10px rgba(0,0,0,.04); overflow:hidden;
    }
    .list-body{ padding:16px; }

    /* Headline in the same red tone used before */
    .page-title { color:#dc3545; /* .text-danger */ margin:0; }

    /* Use Bootstrap’s default primary to match the other pages’ buttons */
    .btn-primary { }
    .btn-secondary { }

    /* Inputs: subtle consistency */
    .form-label { font-weight:600; }
</style>

<div class="container list-window">
    <div class="list-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="page-title">Add Administrator</h2>
            <a href="{{ route('admin.index') }}" class="btn btn-secondary">↩ Back to List</a>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>Please fix the errors below.</strong>
            </div>
        @endif

        <form action="{{ route('admin.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label class="form-label">Name</label>
                <input type="text" name="name" class="form-control" required value="{{ old('name') }}">
                @error('name') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" required value="{{ old('email') }}">
                @error('email') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
                @error('password') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Mobile</label>
                <input type="text" name="mobile" class="form-control" value="{{ old('mobile') }}">
                @error('mobile') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            {{-- Keep these commented blocks for future use, same as your reference --}}
            {{-- 
            <div class="mb-3">
                <label class="form-label">Branch</label>
                <select name="branch_id" class="form-select">
                    <option value="">-- Select Branch --</option>
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}" {{ old('branch_id') == $branch->id ? 'selected' : '' }}>
                            {{ $branch->branch_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Ferry Boat</label>
                <select name="ferryboat_id" class="form-select">
                    <option value="">-- Select Ferry Boat --</option>
                    @foreach($ferryboats as $ferryboat)
                        <option value="{{ $ferryboat->id }}" {{ old('ferryboat_id') == $ferryboat->id ? 'selected' : '' }}>
                            {{ $ferryboat->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            --}}

            <input type="hidden" name="role_id" value="2">

            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('admin.index') }}" class="btn btn-secondary">↩ Cancel</a>
                <button type="submit" class="btn btn-primary">💾 Save</button>
            </div>
        </form>
    </div>
</div>
@endsection
