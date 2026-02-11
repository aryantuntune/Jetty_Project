import { useEffect, useState } from 'react';
import { useSearchParams } from 'react-router-dom';
import { useQuery } from '@tanstack/react-query';
import apiClient from '@/lib/axios';
import QRCode from 'react-qr-code';

interface TicketLine {
    id: number;
    item_name: string;
    qty: number;
    rate: number;
    levy: number;
    amount: number;
    vehicle_name?: string;
    vehicle_no?: string;
}

interface Ticket {
    id: number;
    ticket_no: string;
    ticket_date: string;
    ferry_time: string;
    total_amount: number;
    branch_name: string;
    user_name: string;
    lines: TicketLine[];
}

export function TicketPrint() {
    const [searchParams] = useSearchParams();
    const ticketId = searchParams.get('id');
    const paperWidth = searchParams.get('w') || '58';

    const { data: ticket, isLoading } = useQuery({
        queryKey: ['ticket-print', ticketId],
        queryFn: async () => {
            const res = await apiClient.get(`/api/tickets/${ticketId}`);
            return res.data?.data || res.data;
        },
        enabled: !!ticketId,
    });

    useEffect(() => {
        if (ticket) {
            // Auto print after load
            setTimeout(() => {
                window.print();
            }, 500);
        }
    }, [ticket]);

    if (isLoading) {
        return <div className="text-center py-8">Loading...</div>;
    }

    if (!ticket) {
        return <div className="text-center py-8">Ticket not found</div>;
    }

    const is80mm = paperWidth === '80';
    const paperClass = is80mm ? 'w-[80mm]' : 'w-[58mm]';

    return (
        <>
            <style>{`
        @page { 
          size: ${is80mm ? '80mm' : '58mm'} auto; 
          margin: 0; 
        }
        @media print {
          body { 
            margin: 0; 
            padding: 0; 
          }
          .no-print { display: none !important; }
        }
        body {
          font-family: "Courier New", monospace;
          font-size: ${is80mm ? '12px' : '11px'};
          line-height: 1.25;
          margin: 0;
          padding: 0;
        }
      `}</style>

            {/* Print Controls - Hidden when printing */}
            <div className="no-print fixed top-4 right-4 flex gap-2">
                <button
                    onClick={() => window.print()}
                    className="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
                >
                    Print
                </button>
                <button
                    onClick={() => window.close()}
                    className="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300"
                >
                    Close
                </button>
            </div>

            {/* Receipt */}
            <div className={`${paperClass} mx-auto bg-white p-2`} style={{ fontFamily: '"Courier New", monospace' }}>
                {/* Header */}
                <div className="text-center font-bold text-xs">
                    SUVARNA DURGA SHIPPING & MARINE SERVICES PVT. LTD.
                </div>
                <div className="text-center text-xs">DABHOL</div>
                <div className="text-center text-xs">MAHARASHTRA MARITIME BOARD APPROVAL</div>
                <div className="text-center text-xs mt-1">{ticket.branch_name || ''}</div>

                <div className="border-t border-dashed border-black my-2" />

                {/* Info Row */}
                <div className="flex justify-between text-xs">
                    <span>PHONE: 9767248900</span>
                    <span>TIME: {ticket.ferry_time || '-'}</span>
                </div>
                <div className="flex justify-between text-xs">
                    <span>CASH MEMO NO: {ticket.ticket_no || ticket.id}</span>
                    <span>DATE: {ticket.ticket_date || '-'}</span>
                </div>

                <div className="border-t border-dashed border-black my-2" />

                {/* Header Row */}
                <div className={`grid ${is80mm ? 'grid-cols-5' : 'grid-cols-5'} gap-0 text-xs font-bold border-b border-dashed pb-1 mb-1`}>
                    <div className="col-span-1">Desp</div>
                    <div className="text-right">Qty</div>
                    <div className="text-right">Rate</div>
                    <div className="text-right">Levy</div>
                    <div className="text-right">Amt</div>
                </div>

                {/* Line Items */}
                {ticket.lines?.map((line: TicketLine) => (
                    <div key={line.id} className="mb-1">
                        <div className={`grid ${is80mm ? 'grid-cols-5' : 'grid-cols-5'} gap-0 text-xs`}>
                            <div className="col-span-1 break-words">
                                {line.item_name}
                                {(line.vehicle_name || line.vehicle_no) && (
                                    <div className="text-[10px] text-gray-600">
                                        {line.vehicle_name} {line.vehicle_no}
                                    </div>
                                )}
                            </div>
                            <div className="text-right">{Math.floor(line.qty) === line.qty ? line.qty : line.qty.toFixed(2)}</div>
                            <div className="text-right">{Math.floor(line.rate) === line.rate ? line.rate : line.rate.toFixed(2)}</div>
                            <div className="text-right">{Math.floor(line.levy) === line.levy ? line.levy : line.levy.toFixed(2)}</div>
                            <div className="text-right">{Math.floor(line.amount) === line.amount ? line.amount : line.amount.toFixed(2)}</div>
                        </div>
                    </div>
                ))}

                <div className="border-t border-dashed border-black my-2" />

                {/* Total */}
                <div className="flex justify-between text-xs font-bold">
                    <span>NET TOTAL WITH GOVT. TAX.:</span>
                    <span>{parseFloat(String(ticket.total_amount)).toFixed(2)}</span>
                </div>

                <div className="border-t border-dashed border-black my-2" />

                {/* Note */}
                <div className="text-[9px] text-gray-700 mt-1">
                    NOTE: Tantrik durustimule velevār nā sutlyās va uśhirā pahochlyās company jabābdār rahanār nāhī.
                </div>
                <div className="text-center text-[10px] mt-1">
                    Ferry Boat TICKET DAKHVAVE.<br />
                    HAPPY JOURNEY - www.carferry.in
                </div>

                <div className="border-t border-dashed border-black my-2" />

                {/* Footer */}
                <div className="flex justify-between text-[10px]">
                    <div>
                        DATE: {ticket.ticket_date} {ticket.ferry_time}<br />
                        CASH MEMO NO: {ticket.ticket_no || ticket.id}
                    </div>
                    <div className="text-right">
                        CREATED BY: {(ticket.user_name || '-').toUpperCase()}
                    </div>
                </div>

                {/* QR Code */}
                <div className="flex flex-col items-center mt-3">
                    <QRCode
                        value={`${window.location.origin}/tickets/verify?code=${ticket.id}`}
                        size={80}
                        level="L"
                    />
                    <div className="text-[10px] mt-1">SCAN TO VERIFY TICKET #{ticket.ticket_no || ticket.id}</div>
                </div>
            </div>
        </>
    );
}
