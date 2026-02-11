import { useEffect } from 'react';
import { Head, usePage } from '@inertiajs/react';

export default function TicketPrint() {
    const { ticket, width = '58' } = usePage().props;

    // Auto print on load
    useEffect(() => {
        const timer = setTimeout(() => {
            window.print();
        }, 500);

        window.onafterprint = () => {
            window.close();
        };

        // Fallback close after 5 seconds
        const closeTimer = setTimeout(() => {
            window.close();
        }, 5000);

        return () => {
            clearTimeout(timer);
            clearTimeout(closeTimer);
        };
    }, []);

    // Format number - remove trailing zeros
    const formatNum = (num) => {
        if (num === null || num === undefined) return '0';
        const n = parseFloat(num);
        return n % 1 === 0 ? Math.floor(n).toString() : n.toFixed(2).replace(/\.?0+$/, '');
    };

    // Format time
    const formatTime = (time) => {
        if (!time) return '--:--';
        if (typeof time === 'string' && time.includes(':')) {
            return time.substring(0, 5);
        }
        return time;
    };

    // Format date
    const formatDate = (date) => {
        if (!date) return '--/--/----';
        const d = new Date(date);
        return d.toLocaleDateString('en-GB', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric',
        }).replace(/\//g, '-');
    };

    const is80mm = width === '80';
    const paperWidth = is80mm ? '80mm' : '58mm';

    return (
        <>
            <Head>
                <title>Ticket #{ticket?.ticket_no || ticket?.id}</title>
            </Head>

            <div className="rcpt" style={{ '--paper-width': paperWidth }}>
                {/* Header */}
                <div className="h-title">SUVARNADURGA SHIPPING & MARINE SERVICES PVT. LTD.</div>
                <div className="h-sub">DABHOL</div>
                <div className="h-sub">MAHARASHTRA MARITIME BOARD APPROVAL</div>
                <div className="center">{ticket?.branch?.branch_name || ''}</div>

                <div className="line" />

                {/* Info Row */}
                <div className="row">
                    <div className="col">PHONE: 9767248900</div>
                    <div className="col right">TIME: {formatTime(ticket?.ferry_time || ticket?.created_at)}</div>
                </div>
                <div className="row">
                    <div className="col">CASH MEMO NO: {ticket?.ticket_no || ticket?.id}</div>
                    <div className="col right">DATE: {formatDate(ticket?.ticket_date || ticket?.created_at)}</div>
                </div>

                <div className="line" />

                {/* Items Header */}
                <div className="head-grid bold">
                    <div>Desp</div>
                    <div className="right">Qty</div>
                    <div className="right">Rate</div>
                    <div className="right">Levy</div>
                    <div className="right">Amt</div>
                </div>

                {/* Items */}
                {ticket?.lines?.map((ln, idx) => (
                    <div key={idx} className="item no-break">
                        <div className="item-grid">
                            <div>
                                {ln.item_name}
                                {(ln.vehicle_no || ln.vehicle_name) && (
                                    <div className="muted">
                                        {[ln.vehicle_name, ln.vehicle_no].filter(Boolean).join(' ')}
                                    </div>
                                )}
                            </div>
                            <div>{formatNum(ln.qty)}</div>
                            <div>{formatNum(ln.rate)}</div>
                            <div>{formatNum(ln.levy)}</div>
                            <div>{formatNum(ln.amount)}</div>
                        </div>
                    </div>
                ))}

                <div className="line" />

                {/* Total */}
                <div className="row totals">
                    <div className="col label">NET TOTAL WITH GOVT. TAX. :</div>
                    <div className="col value">{parseFloat(ticket?.total_amount || 0).toFixed(2)}</div>
                </div>

                <div className="line" />

                {/* Notes */}
                <div className="muted" style={{ marginTop: '2px' }}>
                    NOTE: Tantrik durustimule velevār nā sutlyās va uśhirā pahochlyās company jabābdār rahanār nāhī.
                </div>
                <div className="center" style={{ marginTop: '2px' }}>
                    Ferry Boat TICKET DAKHVAVE. <br /> HAPPY JOURNEY - www.carferry.in
                </div>

                <div className="line" />

                {/* Footer */}
                <div className="row">
                    <div className="col">
                        DATE: {formatDate(ticket?.ticket_date || ticket?.created_at)} {formatTime(ticket?.ferry_time || ticket?.created_at)}<br />
                        CASH MEMO NO: {ticket?.ticket_no || ticket?.id}
                    </div>
                    <div className="col right">
                        CREATED BY: {ticket?.user?.name?.toUpperCase() || '-'}
                    </div>
                </div>

                {/* QR Code */}
                <div className="center" style={{ marginTop: '6px' }}>
                    <img
                        src={`https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=${encodeURIComponent(window.location.origin + '/verify?code=' + ticket?.id)}`}
                        alt="QR Code"
                        width="100"
                        height="100"
                    />
                    <div style={{ fontSize: '10px' }}>SCAN TO VERIFY TICKET #{ticket?.ticket_no || ticket?.id}</div>
                </div>
            </div>

            <style>{`
                :root {
                    --paper-width: ${paperWidth};
                    --font-size: ${is80mm ? '12px' : '11px'};
                    --line-height: 1.25;
                    --grid-gap: 2px;
                    --col-desc: ${is80mm ? '50%' : '42%'};
                    --col-qty: ${is80mm ? '12%' : '15%'};
                    --col-rate: ${is80mm ? '12%' : '15%'};
                    --col-levy: ${is80mm ? '12%' : '13%'};
                    --col-amount: ${is80mm ? '14%' : '15%'};
                }

                @page {
                    size: var(--paper-width) auto;
                    margin: 0;
                }

                * {
                    box-sizing: border-box;
                }

                body {
                    margin: 0;
                    font-family: "Courier New", ui-monospace, SFMono-Regular, Menlo, Consolas, monospace;
                    font-size: var(--font-size);
                    line-height: var(--line-height);
                    color: #000;
                    background: #f5f5f5;
                }

                .rcpt {
                    width: var(--paper-width);
                    max-width: 100%;
                    margin: 0 auto;
                    padding: 2mm 2.2mm;
                    background: #fff;
                    box-shadow: 0 0 4px rgba(0,0,0,.2);
                }

                .center { text-align: center; }
                .right { text-align: right; }
                .bold { font-weight: bold; }
                .muted { color: #222; }
                .line { border-top: 1px dashed #000; margin: 3px 0; }

                .h-title { font-weight: 700; text-align: center; }
                .h-sub { text-align: center; }

                .row {
                    display: flex;
                    justify-content: space-between;
                }

                .col { flex: 1; }

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

                .item-title {
                    white-space: normal;
                    overflow-wrap: anywhere;
                    word-break: break-word;
                    margin-bottom: 1px;
                }

                .totals {
                    margin-top: 4px;
                    display: grid;
                    grid-template-columns: 1fr auto;
                    column-gap: var(--grid-gap);
                }

                .totals .label { font-weight: 700; }
                .totals .value { text-align: right; font-weight: 700; }

                .no-break { page-break-inside: avoid; }

                @media print {
                    body {
                        background: none;
                    }
                    .rcpt {
                        box-shadow: none;
                    }
                }
            `}</style>
        </>
    );
}

// Full page layout (no wrapper)
TicketPrint.layout = (page) => page;
