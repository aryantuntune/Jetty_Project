@extends('layouts.admin')

@section('title', 'Ticket Verification')
@section('page-title', 'Ticket Verification')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <!-- Header -->
    <div>
        <h2 class="text-2xl font-bold text-slate-800">Ticket Verification</h2>
        <p class="text-slate-500 mt-1">Scan or search for tickets to verify</p>
    </div>

    <!-- Alerts -->
    @if(session('error'))
    <div class="p-4 bg-red-50 border border-red-200 rounded-xl">
        <div class="flex items-center space-x-3">
            <i data-lucide="alert-circle" class="w-5 h-5 text-red-500"></i>
            <p class="text-red-800">{{ session('error') }}</p>
        </div>
    </div>
    @endif

    @if(session('success'))
    <div class="p-4 bg-green-50 border border-green-200 rounded-xl">
        <div class="flex items-center space-x-3">
            <i data-lucide="check-circle" class="w-5 h-5 text-green-500"></i>
            <p class="text-green-800">{{ session('success') }}</p>
        </div>
    </div>
    @endif

    <!-- Search Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-4 border-b border-slate-200 bg-slate-50">
            <div class="flex items-center space-x-2">
                <i data-lucide="scan" class="w-5 h-5 text-slate-400"></i>
                <span class="font-semibold text-slate-700">Search Ticket</span>
            </div>
        </div>
        <form method="GET" action="{{ route('verify.index') }}" class="p-6">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        Scan or Enter Ticket Code
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i data-lucide="qr-code" class="w-5 h-5 text-slate-400"></i>
                        </div>
                        <input type="text" name="code" class="w-full pl-12 pr-4 py-3 rounded-xl border border-slate-200 focus:border-primary-500 focus:ring-4 focus:ring-primary-500/10 outline-none transition-all" placeholder="Scan QR code or type ticket number..." autofocus required>
                    </div>
                </div>
                <button type="submit" class="w-full px-5 py-3 bg-gradient-to-r from-primary-500 to-primary-600 text-white font-semibold rounded-xl hover:from-primary-600 hover:to-primary-700 transition-all duration-200 shadow-lg shadow-primary-500/30 flex items-center justify-center">
                    <i data-lucide="search" class="w-5 h-5 mr-2"></i>
                    Search Ticket
                </button>
            </div>
        </form>
    </div>

    <!-- Ticket Details Card -->
    @if($ticket)
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-4 border-b border-slate-200 bg-slate-50">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <i data-lucide="ticket" class="w-5 h-5 text-slate-400"></i>
                    <span class="font-semibold text-slate-700">Ticket #{{ $ticket->ticket_no ?? $ticket->id }}</span>
                    @if($ticket->ticket_no)
                    <span class="text-xs text-slate-400">(ID: {{ $ticket->id }})</span>
                    @endif
                </div>
                @php
                    $verifiedAt = $ticket->verified_at ? \Carbon\Carbon::parse($ticket->verified_at)->timezone('Asia/Kolkata') : null;
                @endphp
                @if($verifiedAt)
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                    <i data-lucide="check-circle" class="w-3 h-3 mr-1"></i>
                    Verified
                </span>
                @else
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                    <i data-lucide="clock" class="w-3 h-3 mr-1"></i>
                    Pending
                </span>
                @endif
            </div>
        </div>

        <div class="p-6 space-y-6">
            <!-- Ticket Info -->
            @php
                $ticketDate = $ticket->ticket_date ?? ($ticket->created_at ? \Carbon\Carbon::parse($ticket->created_at)->timezone('Asia/Kolkata') : null);
                $ferryTime = $ticket->ferry_time ?? ($ticket->created_at ? \Carbon\Carbon::parse($ticket->created_at)->timezone('Asia/Kolkata') : null);
            @endphp
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-slate-50 rounded-xl p-4">
                    <p class="text-xs text-slate-500 mb-1">Branch</p>
                    <p class="font-semibold text-slate-800">{{ $ticket->branch->branch_name ?? '-' }}</p>
                </div>
                <div class="bg-slate-50 rounded-xl p-4">
                    <p class="text-xs text-slate-500 mb-1">Date</p>
                    <p class="font-semibold text-slate-800">{{ $ticketDate ? $ticketDate->format('d-m-Y') : '-' }}</p>
                </div>
                <div class="bg-slate-50 rounded-xl p-4">
                    <p class="text-xs text-slate-500 mb-1">Ferry Time</p>
                    <p class="font-semibold text-slate-800">{{ $ferryTime ? $ferryTime->format('H:i') : '-' }}</p>
                </div>
                <div class="bg-slate-50 rounded-xl p-4">
                    <p class="text-xs text-slate-500 mb-1">Total</p>
                    <p class="font-semibold text-lg text-primary-600">₹{{ number_format($ticket->total_amount, 2) }}</p>
                </div>
            </div>

            @if($verifiedAt)
            <div class="bg-green-50 border border-green-200 rounded-xl p-4">
                <div class="flex items-center space-x-3">
                    <i data-lucide="check-circle" class="w-5 h-5 text-green-500"></i>
                    <div>
                        <p class="font-medium text-green-800">Verified</p>
                        <p class="text-sm text-green-600">{{ $verifiedAt->format('d-m-Y H:i') }}</p>
                    </div>
                </div>
            </div>
            @endif

            <!-- Items Table -->
            <div>
                <h3 class="text-sm font-semibold text-slate-700 mb-3">Ticket Items</h3>
                <div class="overflow-x-auto border border-slate-200 rounded-xl">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200">
                                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Description</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider">Qty</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">Rate</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">Levy</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">Amount</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($ticket->lines as $ln)
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-3">
                                    <span class="font-medium text-slate-800">{{ $ln->item_name }}</span>
                                    @if($ln->vehicle_no || $ln->vehicle_name)
                                    <br>
                                    <span class="text-xs text-slate-500">{{ $ln->vehicle_name }} {{ $ln->vehicle_no }}</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center text-slate-600">{{ $ln->qty }}</td>
                                <td class="px-4 py-3 text-right text-slate-600">{{ number_format($ln->rate, 2) }}</td>
                                <td class="px-4 py-3 text-right text-slate-600">{{ number_format($ln->levy, 2) }}</td>
                                <td class="px-4 py-3 text-right font-medium text-slate-800">₹{{ number_format($ln->amount, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="bg-slate-50 border-t border-slate-200">
                                <td colspan="4" class="px-4 py-3 text-right font-semibold text-slate-700">Total Amount:</td>
                                <td class="px-4 py-3 text-right font-bold text-lg text-primary-600">₹{{ number_format($ticket->total_amount, 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- Verify Button -->
            @if(!$verifiedAt)
            <form method="POST" action="{{ route('verify.ticket') }}">
                @csrf
                <input type="hidden" name="ticket_id" value="{{ $ticket->id }}">
                <button type="submit" class="w-full px-5 py-3 bg-gradient-to-r from-green-500 to-green-600 text-white font-semibold rounded-xl hover:from-green-600 hover:to-green-700 transition-all duration-200 shadow-lg shadow-green-500/30 flex items-center justify-center">
                    <i data-lucide="check-circle" class="w-5 h-5 mr-2"></i>
                    Mark as Verified
                </button>
            </form>
            @endif
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script>
    lucide.createIcons();
</script>
@endpush
@endsection
