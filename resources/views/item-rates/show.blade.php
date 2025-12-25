@extends('layouts.admin')

@section('title', 'View Item Rate')
@section('page-title', 'View Item Rate')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('item-rates.index') }}" class="inline-flex items-center text-slate-500 hover:text-slate-700 transition-colors mb-4">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
            Back to Item Rates
        </a>
        <h2 class="text-2xl font-bold text-slate-800">Item Rate Details</h2>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
        <p class="text-slate-500 text-center py-8">No details to display</p>
    </div>
</div>

@push('scripts')
<script>
    lucide.createIcons();
</script>
@endpush
@endsection
