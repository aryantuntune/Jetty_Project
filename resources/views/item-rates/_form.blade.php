<form method="post" action="{{ $route }}">
    @csrf
    @if($method !== 'POST') @method($method) @endif

    <div class="row g-3">
        <div class="col-md-4">
            <label class="form-label">Item Name</label>
            <input type="text" name="item_name" class="form-control"
                   value="{{ old('item_name', $model->item_name ?? '') }}" required>
            @error('item_name') <div class="text-danger small">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-4">
            <label class="form-label">Item Category ID</label>
            <input type="number" name="item_category_id" class="form-control"
                   value="{{ old('item_category_id', $model->item_category_id ?? '') }}">
            @error('item_category_id') <div class="text-danger small">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-2">
            <label class="form-label">Branch ID</label>
            <input type="number" name="branch_id" class="form-control"
                   value="{{ old('branch_id', $model->branch_id ?? '') }}">
            @error('branch_id') <div class="text-danger small">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-2">
            <label class="form-label">User ID</label>
            <input type="number" name="user_id" class="form-control" value="{{ old('user_id', $model->user_id ?? auth()->id()) }}" readonly>
        </div>

        <div class="col-md-3">
            <label class="form-label">Item Rate</label>
            <input type="number" step="0.01" min="0" name="item_rate" class="form-control"
                   value="{{ old('item_rate', $model->item_rate ?? 0) }}" required>
            @error('item_rate') <div class="text-danger small">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-3">
            <label class="form-label">Item Levy</label>
            <input type="number" step="0.01" min="0" name="item_lavy" class="form-control"
                   value="{{ old('item_lavy', $model->item_lavy ?? 0) }}" required>
            @error('item_lavy') <div class="text-danger small">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-3">
            <label class="form-label">Starting Date</label>
            <input type="date" name="starting_date" class="form-control"
                   value="{{ old('starting_date', optional($model->starting_date ?? null)?->format('Y-m-d')) }}" required>
            @error('starting_date') <div class="text-danger small">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-3">
            <label class="form-label">Ending Date (optional)</label>
            <input type="date" name="ending_date" class="form-control"
                   value="{{ old('ending_date', optional($model->ending_date ?? null)?->format('Y-m-d')) }}">
            @error('ending_date') <div class="text-danger small">{{ $message }}</div> @enderror
        </div>

        <div class="col-12">
            <button class="btn btn-primary">{{ $method === 'POST' ? 'Create' : 'Update' }}</button>
            <a href="{{ route('item-rates.index') }}" class="btn btn-light">Cancel</a>
        </div>
    </div>
</form>
