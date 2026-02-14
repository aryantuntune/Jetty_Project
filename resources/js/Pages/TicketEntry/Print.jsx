import { useEffect, useRef } from 'react';

export default function Print({ ticket }) {
    const printRef = useRef();

    useEffect(() => {
        const timer = setTimeout(() => {
            window.print();
        }, 500);
        return () => clearTimeout(timer);
    }, []);

    const formatDate = (date) => {
        if (!date) return '-';
        const d = new Date(date);
        const dd = String(d.getDate()).padStart(2, '0');
        const mm = String(d.getMonth() + 1).padStart(2, '0');
        const yyyy = d.getFullYear();
        return `${dd}-${mm}-${yyyy}`;
    };

    const formatTime24 = (time) => {
        if (!time) return '-';
        // Handle ISO datetime
        if (time.includes('T')) {
            const d = new Date(time);
            return `${String(d.getHours()).padStart(2, '0')}:${String(d.getMinutes()).padStart(2, '0')}`;
        }
        // Handle HH:MM or HH:MM:SS
        const parts = time.split(':');
        if (parts.length >= 2) {
            return `${parts[0].padStart(2, '0')}:${parts[1].padStart(2, '0')}`;
        }
        return time;
    };

    const branchName = ticket?.branch?.branch_name || '-';
    const destName = ticket?.dest_branch?.branch_name || ticket?.dest_branch_name || '-';
    const ticketNo = ticket?.ticket_no || ticket?.id || '-';
    const createdBy = ticket?.user?.name || '-';
    const totalAmount = parseFloat(ticket?.total_amount || 0).toFixed(2);
    const ferryTime = formatTime24(ticket?.ferry_time);
    const ticketDate = formatDate(ticket?.ticket_date || ticket?.created_at);

    return (
        <div className="print-page">
            <div ref={printRef} className="receipt">
                {/* ---- Header ---- */}
                <div className="header-section">
                    <p className="company-name">SUVARNADURGA SHIPPING & MARINE SERVICES PVT.LTD.</p>
                    <p className="branch-name">{branchName.toUpperCase()}</p>
                    <p className="sub-text">MAHARASHTRA MARITIME BOARD APPROVAL</p>
                    <p className="route-text">{branchName.toUpperCase()} To {destName.toUpperCase()}</p>
                </div>

                {/* ---- Info Row ---- */}
                <div className="info-row">
                    <span>PHONE: 9767248900</span>
                    <span>TIME: {ferryTime}</span>
                </div>
                <div className="info-row">
                    <span>CASH MEMO NO: {ticketNo}</span>
                    <span>DATE: {ticketDate}</span>
                </div>

                <div className="divider" />

                {/* ---- Items Table ---- */}
                <table className="items-table">
                    <thead>
                        <tr>
                            <th className="left">Description</th>
                            <th className="right">Qty</th>
                            <th className="right">Rate</th>
                            <th className="right">Levy</th>
                            <th className="right">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        {ticket?.lines?.map((line, idx) => {
                            const qty = parseFloat(line.qty || 0).toFixed(2);
                            const rate = parseFloat(line.rate || 0).toFixed(2);
                            const levy = parseFloat(line.levy || 0).toFixed(2);
                            const amount = parseFloat(line.amount || 0).toFixed(2);
                            return (
                                <tr key={idx}>
                                    <td className="left">
                                        {line.item_name || '-'}
                                        {line.vehicle_no ? <br /> : null}
                                        {line.vehicle_no || ''}
                                    </td>
                                    <td className="right">{qty}</td>
                                    <td className="right">{rate}</td>
                                    <td className="right">{levy}</td>
                                    <td className="right">{amount}</td>
                                </tr>
                            );
                        })}
                    </tbody>
                </table>

                <div className="divider" />

                {/* ---- Total ---- */}
                <div className="total-row">
                    <span>NET TOTAL WITH GOVT.TAX. :</span>
                    <span className="total-amount">{totalAmount}</span>
                </div>

                <div className="divider" />

                {/* ---- Note ---- */}
                <div className="note-section">
                    <p>NOTE: Tantrik Durustimule Velevar na sutlyas va ushira pohochlyas company jababdar rahanar nahi.</p>
                    <p>Ferry Boatit TICKET DAKHVAVE.</p>
                    <p className="happy">HAPPY JOURNEY - www.carferry.in</p>
                </div>

                <div className="divider" />

                {/* ---- Footer ---- */}
                <div className="footer-section">
                    <div className="info-row">
                        <span>DATE: {ticketDate} {ferryTime}</span>
                        <span>CREATED BY: {createdBy}</span>
                    </div>
                    <p>CASH MEMO NO: {ticketNo}</p>
                    <div className="total-row">
                        <span>NET TOTAL WITH GOVT.TAX. :</span>
                        <span className="total-amount">{totalAmount}</span>
                    </div>
                </div>

                <div className="divider" />
            </div>

            {/* Print button (screen only) */}
            <div className="no-print" style={{ textAlign: 'center', marginTop: '16px' }}>
                <button
                    onClick={() => window.print()}
                    style={{
                        padding: '8px 24px',
                        backgroundColor: '#6366f1',
                        color: '#fff',
                        border: 'none',
                        borderRadius: '8px',
                        cursor: 'pointer',
                        fontSize: '14px',
                    }}
                >
                    Print Ticket
                </button>
            </div>

            <style>{`
                * { margin: 0; padding: 0; box-sizing: border-box; }

                .print-page {
                    min-height: 100vh;
                    background: #f0f0f0;
                    padding: 16px;
                    font-family: 'Courier New', Courier, monospace;
                    font-size: 11px;
                    color: #000;
                }

                .receipt {
                    max-width: 80mm;
                    margin: 0 auto;
                    background: #fff;
                    padding: 8px 10px;
                    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
                    line-height: 1.4;
                }

                .header-section {
                    text-align: center;
                    margin-bottom: 4px;
                }
                .company-name {
                    font-weight: bold;
                    font-size: 11px;
                    line-height: 1.3;
                }
                .branch-name {
                    font-weight: bold;
                    font-size: 11px;
                }
                .sub-text {
                    font-size: 10px;
                }
                .route-text {
                    font-weight: bold;
                    font-size: 11px;
                    margin-top: 2px;
                }

                .info-row {
                    display: flex;
                    justify-content: space-between;
                    font-size: 10px;
                    margin: 2px 0;
                }

                .divider {
                    border-top: 1px dashed #000;
                    margin: 6px 0;
                }

                .items-table {
                    width: 100%;
                    border-collapse: collapse;
                    font-size: 10px;
                    margin: 4px 0;
                }
                .items-table th {
                    border-bottom: 1px solid #000;
                    padding: 2px 1px;
                    font-weight: bold;
                    font-size: 10px;
                }
                .items-table td {
                    padding: 3px 1px;
                    vertical-align: top;
                    font-size: 10px;
                }
                .items-table .left { text-align: left; }
                .items-table .right { text-align: right; }

                .total-row {
                    display: flex;
                    justify-content: space-between;
                    font-weight: bold;
                    font-size: 12px;
                    padding: 4px 0;
                }
                .total-amount {
                    font-size: 12px;
                }

                .note-section {
                    font-size: 9px;
                    line-height: 1.4;
                    padding: 4px 0;
                }
                .note-section .happy {
                    text-align: center;
                    font-weight: bold;
                    margin-top: 4px;
                    font-size: 10px;
                }

                .footer-section {
                    font-size: 10px;
                    padding: 4px 0;
                }
                .footer-section p {
                    margin: 2px 0;
                }

                .no-print {}

                @media print {
                    @page {
                        size: 80mm auto;
                        margin: 0;
                    }
                    body {
                        margin: 0;
                        padding: 0;
                        print-color-adjust: exact;
                        -webkit-print-color-adjust: exact;
                    }
                    .print-page {
                        padding: 0;
                        background: #fff;
                        min-height: auto;
                    }
                    .receipt {
                        max-width: 100%;
                        box-shadow: none;
                        padding: 4px 6px;
                    }
                    .no-print {
                        display: none !important;
                    }
                }
            `}</style>
        </div>
    );
}

// No layout for print page - it should be standalone
Print.layout = (page) => page;
