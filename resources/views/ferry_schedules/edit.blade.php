@extends('layouts.app')

@section('content')
<style>
    .win-card {
        border: 2px solid #9ec5fe;
        background: #f8fafc;
        box-shadow: 2px 2px 6px rgba(0,0,0,0.15);
    }

    .win-header {
        background: #ffffff;
        text-align: center;
        font-weight: bold;
        color: darkred;
        padding: 10px;
        font-size: 18px;
        border-bottom: 1px solid #ccc;
    }

    .form-label {
        display: block;
        font-weight: bold;
        margin-bottom: 6px;
    }

    .form-input {
        width: 100%;
        padding: 8px 10px;
        font-size: 14px;
        border: 1px solid #ccc;
        border-radius: 4px;
        margin-bottom: 16px;
    }

    .form-footer {
        background: darkred;
        padding: 10px;
        display: flex;
        justify-content: space-between;
    }

    .btn {
        padding: 6px 14px;
        font-size: 14px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        color: white;
    }

    .btn-secondary {
        background-color: #6b7280; /* Gray */
    }

    .btn-secondary:hover {
        background-color: #4b5563;
    }

    .btn-primary {
        background-color: #1d4ed8; /* Blue */
    }

    .btn-primary:hover {
        background-color: #1e40af;
    }

    .text-danger {
        color: red;
        font-size: 13px;
        margin-top: -10px;
        margin-bottom: 10px;
    }
</style>

<div class="flex justify-center items-center min-h-screen bg-gray-100 p-4">
    <div class="win-card w-1/2">

        <!-- Header -->
        <div class="win-header">
            Edit Ferry Time
        </div>

        <!-- Form -->
        <form method="POST" action="{{ route('ferry_schedules.update', $ferry_schedule) }}" class="p-4">
            @csrf
            @method('PUT')

            <label for="hour" class="form-label">Hour (0-23)</label>
            <input type="number" name="hour" id="hour" class="form-input" min="0" max="23" required value="{{ old('hour', $ferry_schedule->hour) }}">
            @error('hour') <div class="text-danger">{{ $message }}</div> @enderror

            <label for="minute" class="form-label">Minute (0-59)</label>
            <input type="number" name="minute" id="minute" class="form-input" min="0" max="59" required value="{{ old('minute', $ferry_schedule->minute) }}">
            @error('minute') <div class="text-danger">{{ $message }}</div> @enderror

            <!-- Footer Buttons -->
            <div class="form-footer">
                <a href="{{ route('ferry_schedules.index') }}" class="btn btn-secondary">Back</a>
                <button type="submit" class="btn btn-primary">Update</button>
            </div>
        </form>

    </div>
</div>
@endsection
