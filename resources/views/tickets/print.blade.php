<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Ticket #{{ $ticket->id }}</title>
 <style>
  :root {
    --paper-width: 58mm;
    --font-size: 11px;
    --line-height: 1.25;
    --grid-gap: 2px;
    /* column widths for 58mm */
    --col-desc: 42%;
    --col-qty: 15%;
    --col-rate: 15%;
    --col-levy: 13%;
    --col-amount: 15%;
  }

  @page { size: var(--paper-width) auto; margin: 0; }
  * { box-sizing: border-box; }

  body {
    margin: 0;
    font-family: "Courier New", ui-monospace, SFMono-Regular, Menlo, Consolas, monospace;
    font-size: var(--font-size);
    line-height: var(--line-height);
    color: #000;
  }

  .rcpt {
    width: var(--paper-width);
    max-width: 100%;
    margin: 0 auto;
    padding: 2mm 2.2mm;
  }

  .center { text-align:center; }
  .right  { text-align:right;  }
  .bold   { font-weight:bold;  }
  .muted  { color:#222; }
  .line   { border-top:1px dashed #000; margin: 3px 0; }

  .h-title { font-weight:700; text-align:center; }
  .h-sub   { text-align:center; }

  /* Grid header and items */
  .head-grid,
  .item-grid {
    display: grid;
    grid-template-columns: var(--col-desc) var(--col-qty) var(--col-rate) var(--col-levy) var(--col-amount);
    column-gap: var(--grid-gap);
    align-items: start;
  }

  .head-grid {
    font-weight: bold;
    border-bottom: 1px dashed #000;
    padding-bottom: 2px;
    margin-top: 3px;
    margin-bottom: 3px;
  }

  .head-grid > div,
  .item-grid > div {
    text-align: right;
  }

  .head-grid > div:first-child,
  .item-grid > div:first-child {
    text-align: left;
  }

  /* Description styling */
  .item-title {
    white-space: normal;
    overflow-wrap: anywhere;
    word-break: break-word;
    margin-bottom: 1px;
  }

  /* Totals */
  .totals {
    margin-top: 4px;
    display: grid;
    grid-template-columns: 1fr auto;
    column-gap: var(--grid-gap);
  }

  .totals .label { font-weight:700; }
  .totals .value { text-align:right; font-weight:700; }

  .no-break { page-break-inside: avoid; }

  @media screen {
    body { background:#f5f5f5; }
    .rcpt { background:#fff; box-shadow: 0 0 4px rgba(0,0,0,.2); }
  }

  /* ===== 80mm override ===== */
  {{ request('w') == '80' ? '
    :root {
      --paper-width: 80mm;
      --font-size: 12px;
      --col-desc: 50%;
      --col-qty: 12%;
      --col-rate: 12%;
      --col-levy: 12%;
      --col-amount: 14%;
    }
  ' : '' }}
</style>



</head>
<body onload="window.print()" style="{{ request('w') == '80' ? '--paper-width:80mm' : '--paper-width:58mm' }}">

  <div class="rcpt no-break">
    <div class="h-title">SUVARNADURGA SHIPPING & MARINE SERVICES PVT. LTD.</div>
    <div class="h-sub">DABHOL</div>
    <div class="h-sub">MAHARASHTRA MARITIME BOARD APPROVAL</div>
    <div class="center">{{ $ticket->branch->branch_name ?? '' }} 
     
        </div>

    <div class="line"></div>

    <div class="row">
      <div class="col">PHONE: 9767248900</div>
      <div class="col right">TIME: {{ optional($ticket->created_at)->timezone('Asia/Kolkata')->format('H:i') }}</div>
    </div>
    <div class="row">
      <div class="col">CASH MEMO NO: {{ $ticket->id }}</div>
      <div class="col right">DATE: {{ optional($ticket->created_at)->timezone('Asia/Kolkata')->format('d-m-Y') }}</div>
    </div>

    <div class="line"></div>
@php
if (!function_exists('wrap_2_words')) {
    function wrap_2_words(string $text): string {
        // Split into words
        $tokens = preg_split('/\s+/', trim($text));
        if (!$tokens) return e($text);

        // Group in pairs and join with <br>
        $pairs  = array_chunk($tokens, 2);
        $lines  = array_map(fn($p) => e(implode(' ', $p)), $pairs);

        // Return HTML with safe-escaped words
        return implode('<br>', $lines);
    }
}
@endphp

    <!-- Header row for the numeric columns -->
 <div class="head-grid bold">
  <div>Desp</div>
  <div class="right">Qty</div>
  <div class="right">Rate</div>
  <div class="right">Levy</div>
  <div class="right">Amount</div>
</div>

   @foreach($ticket->lines as $ln)
  <div class="item no-break">
    <div class="item-grid">
      <div>
        {{ $ln->item_name }}
        @if(!empty($ln->vehicle_no) || !empty($ln->vehicle_name))
          <div class="muted">
            {!! wrap_2_words(trim(($ln->vehicle_name ?: '').' '.($ln->vehicle_no ?: ''))) !!}
          </div>
        @endif
      </div>
      <div>{{ number_format($ln->qty, 2) }}</div>
      <div>{{ number_format($ln->rate, 2) }}</div>
      <div>{{ number_format($ln->levy, 2) }}</div>
      <div>{{ number_format($ln->amount, 2) }}</div>
    </div>
  </div>
@endforeach


    <div class="line"></div>

    <div class="row totals">
      <div class="col label">NET TOTAL WITH GOVT. TAX. :</div>
      <div class="col value">{{ number_format($ticket->total_amount, 2) }}</div>
    </div>

    <div class="line"></div>

    <div class="muted" style="margin-top:2px;">
      NOTE: Tantrik durustimule velevār nā sutlyās va uśhirā pahochlyās company jabābdār rahanār nāhī.
    </div>
    <div class="center" style="margin-top:2px;">
      Ferry Boat TICKET DAKHVAVE. <br> HAPPY JOURNEY - www.carferry.in
    </div>

    <div class="line"></div>

    <div class="row">
      <div class="col">
        DATE: {{ optional($ticket->created_at)->timezone('Asia/Kolkata')->format('d-m-Y H:i') }}<br>
        CASH MEMO NO: {{ $ticket->id }}
      </div>
      <div class="col right">
        CREATED BY: {{ strtoupper(optional($ticket->user)->name ?? '-') }}
      </div>

      @php


<div class="center" style="margin-top:6px;">
    {!! QrCode::size(100)
        ->margin(1)
        ->generate(url('/verify?code=' . $ticket->id)) !!}
    <div style="font-size:10px;">SCAN TO VERIFY TICKET #{{ $ticket->id }}</div>
</div>


    </div>
  </div>
</body>
</html>
