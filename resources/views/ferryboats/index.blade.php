@extends('layouts.app')

@section('content')
<style>
    .win-card {
        border: 2px solid #9ec5fe;   
        background: #f8fafc;
        box-shadow: 2px 2px 6px rgba(0,0,0,0.15);
    }
    .win-header {
        background: #fff;
        text-align: center;
        font-weight: bold;
        color: darkred;
        padding: 6px;
        border-bottom: 1px solid #ccc;
    }
    .win-table { width: 100%; border-collapse: collapse; font-size: 14px; }
    .win-table th { background: #f0f0f0; border: 1px solid #ccc; padding: 4px; text-align: left; }
    .win-table td { border: 1px solid #ccc; padding: 4px; }
    .win-row { background: #eaffea; }
    .win-row:hover { background: #1e3a8a; color: white; }
    .win-footer {
        background: darkred;
        color: white;
        padding: 6px 10px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-weight: bold;
    }
    .btn-add {
        background: #1d4ed8;
        color: white;
        padding: 4px 10px;
        border-radius: 3px;
        text-decoration: none;
        font-size: 13px;
    }
    .scroll-box { max-height: 400px; overflow-y: auto; }
</style>

<div class="flex justify-center items-center min-h-screen bg-gray-200 container p-4">
    <div class="win-card w-2/3">
        <!-- Header -->
        <div class="win-header">
            Ferry Boats
        </div>

        <!-- Filters -->
        <form method="GET" action="{{ route('ferryboats.index') }}" class="flex gap-2 px-4 py-2">
            <select name="branch_id" class="border px-2 py-1 text-sm">
                <option value="">-- Select Branch --</option>
                @foreach($branches as $branch)
                    <option value="{{ $branch->id }}" {{ $branchId == $branch->id ? 'selected' : '' }}>
                        {{ $branch->branch_name }}
                    </option>
                @endforeach
            </select>
            <button type="submit" class="border px-3 py-1 bg-gray-200">Filter</button>
            <a href="{{ route('ferryboats.index') }}" class="border px-3 py-1 bg-gray-200">All</a>
        </form>

        <!-- Table -->
        <div class="scroll-box">
            <table class="win-table">
              <thead>
  <tr>
    <th style="width:15%">Ferry Boat ID</th>
    <th style="width:25%">Ferry Boat No.</th>
    <th>Ferry Boat Name</th>
    <th>Branch</th>
    @if(in_array(auth()->user()->role_id, [1,2]))
      <th style="width:15%">Actions</th>
    @endif
  </tr>
</thead>
<tbody>
  @forelse($boats as $boat)
    <tr class="win-row">
      <td>{{ $boat->id }}</td>
      <td>{{ $boat->number }}</td>
      <td>{{ $boat->name }}</td>
      <td>{{ $boat->branch->branch_name ?? '-' }}</td>
      
      @if(in_array(auth()->user()->role_id, [1,2]))
      <td>
        <a href="{{ route('ferryboats.edit',$boat) }}" 
           class="btn-add" style="background:#1d4ed8;padding:3px 8px;">Edit</a>

        <form action="{{ route('ferryboats.destroy',$boat) }}" 
              method="POST" style="display:inline;">
          @csrf
          @method('DELETE')
          <button type="submit" onclick="return confirm('Delete this boat?')" 
                  style="background:#b3262e;color:#fff;padding:3px 8px;border:none;border-radius:3px;">
            Delete
          </button>
        </form>
      </td>
      @endif
    </tr>
  @empty
    <tr><td colspan="5" class="text-center">No ferry boats found.</td></tr>
  @endforelse
</tbody>

            </table>
        </div>

        <!-- Footer -->
        <div class="win-footer">
            <span>Total: {{ $total }}</span>
              @if(in_array(auth()->user()->role_id, [1,2]))
            <a href="{{ route('ferryboats.create') }}" class="btn-add">Add New Ferry Boat</a>
            @endif
        </div>
    </div>
</div>
@endsection
