import { useState, useEffect } from 'react';
import { Button, Input, Select, Badge, Card, CardHeader, CardTitle, CardContent } from '@/components/ui';
import { useBranches, useFerries, useRates } from '@/hooks';
import { useAuthStore, useTicketStore } from '@/store';
import { ticketService } from '@/services';
import { Ship, MapPin, Clock, User, Plus, Trash2, CreditCard, Save, Printer } from 'lucide-react';
import { formatCurrency } from '@/lib/utils';
import { toast } from 'sonner';

// Line item type
interface LineItem {
    id: string;
    itemId: number | null;
    itemName: string;
    qty: number;
    rate: number;
    levy: number;
    amount: number;
    vehicleName: string;
    vehicleNo: string;
}

// Payment modal component
interface PaymentModalProps {
    isOpen: boolean;
    onClose: () => void;
    total: number;
    onConfirm: (paymentMode: string) => void;
}

function PaymentModal({ isOpen, onClose, total, onConfirm }: PaymentModalProps) {
    const [paymentMode, setPaymentMode] = useState('Cash');
    const [givenAmount, setGivenAmount] = useState(total);
    const change = givenAmount - total;

    useEffect(() => {
        setGivenAmount(total);
    }, [total]);

    if (!isOpen) return null;

    return (
        <div className="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50">
            <div className="bg-white rounded-xl shadow-2xl w-full max-w-md mx-4 overflow-hidden">
                <div className="bg-gradient-to-r from-blue-600 to-indigo-600 text-white p-4 flex items-center justify-between">
                    <span className="font-semibold">Confirm Payment</span>
                    <button onClick={onClose} className="p-1 hover:bg-white/20 rounded">✕</button>
                </div>

                <div className="p-6 space-y-4">
                    <div>
                        <label className="block text-sm font-medium text-gray-700 mb-1">Net Total</label>
                        <Input value={formatCurrency(total)} readOnly className="text-lg font-bold" />
                    </div>

                    <div>
                        <label className="block text-sm font-medium text-gray-700 mb-1">Payment Mode</label>
                        <Select value={paymentMode} onChange={(e) => setPaymentMode(e.target.value)}>
                            <option value="Cash">Cash</option>
                            <option value="GPay">GPay / UPI</option>
                            <option value="Card">Card</option>
                            <option value="Guest Pass">Guest Pass</option>
                        </Select>
                    </div>

                    {paymentMode === 'Cash' && (
                        <>
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">Given Amount</label>
                                <Input
                                    type="number"
                                    value={givenAmount}
                                    onChange={(e) => setGivenAmount(Number(e.target.value))}
                                    className="text-right"
                                />
                            </div>

                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">Change to Return</label>
                                <Input
                                    value={formatCurrency(Math.max(0, change))}
                                    readOnly
                                    className={`text-right font-bold ${change >= 0 ? 'text-green-600' : 'text-red-600'}`}
                                />
                            </div>
                        </>
                    )}
                </div>

                <div className="bg-gray-50 p-4 flex justify-end gap-3">
                    <Button variant="outline" onClick={onClose}>Cancel</Button>
                    <Button onClick={() => onConfirm(paymentMode)} className="bg-green-600 hover:bg-green-700">
                        <Save className="w-4 h-4" />
                        Confirm Payment
                    </Button>
                </div>
            </div>
        </div>
    );
}

export function TicketEntry() {
    const { user } = useAuthStore();
    const { clearForm } = useTicketStore();

    // Form state
    const [branchId, setBranchId] = useState<number | null>(user?.branch_id || null);
    const [ferryId, setFerryId] = useState<number | null>(null);
    const [ferryTime, setFerryTime] = useState<string>('');
    const [customerName, setCustomerName] = useState('');
    const [customerMobile, setCustomerMobile] = useState('');
    const [printAfterSave, setPrintAfterSave] = useState(true);
    const [discountPct, setDiscountPct] = useState(0);
    const [discountAmt, setDiscountAmt] = useState(0);
    const [isPaymentModalOpen, setIsPaymentModalOpen] = useState(false);
    const [isSubmitting, setIsSubmitting] = useState(false);

    // Line items state
    const [lineItems, setLineItems] = useState<LineItem[]>([
        createEmptyLine(),
    ]);

    // Fetch data
    const { data: branches = [], isLoading: branchesLoading } = useBranches();
    const { data: ferries = [], isLoading: ferriesLoading } = useFerries(branchId || undefined);
    const { data: rates = [], isLoading: ratesLoading } = useRates(branchId || undefined);

    // Create empty line item
    function createEmptyLine(): LineItem {
        return {
            id: crypto.randomUUID(),
            itemId: null,
            itemName: '',
            qty: 1,
            rate: 0,
            levy: 0,
            amount: 0,
            vehicleName: '',
            vehicleNo: '',
        };
    }

    // Calculate totals
    const subtotal = lineItems.reduce((sum, item) => sum + item.amount, 0);
    const discountTotal = discountAmt + (subtotal * discountPct / 100);
    const netTotal = Math.max(0, subtotal - discountTotal);

    // Add new line
    const addLine = () => {
        setLineItems([...lineItems, createEmptyLine()]);
    };

    // Remove line
    const removeLine = (id: string) => {
        if (lineItems.length > 1) {
            setLineItems(lineItems.filter(item => item.id !== id));
        }
    };

    // Update line item
    const updateLine = (id: string, updates: Partial<LineItem>) => {
        setLineItems(lineItems.map(item => {
            if (item.id !== id) return item;

            const updated = { ...item, ...updates };

            // Recalculate amount if qty, rate, or levy changed
            if ('qty' in updates || 'rate' in updates || 'levy' in updates) {
                updated.amount = (updated.qty * updated.rate) + (updated.qty * updated.levy);
            }

            return updated;
        }));
    };

    // Handle item selection
    const handleItemSelect = (lineId: string, rateId: number) => {
        const rate = rates.find((r: any) => r.id === rateId);
        if (rate) {
            // API now returns: { id, item_name, item_rate, item_lavy, price (combined), description }
            const baseRate = rate.item_rate || 0;
            const levy = rate.item_lavy || 0;
            updateLine(lineId, {
                itemId: rate.id,
                itemName: rate.item_name,
                rate: baseRate,
                levy: levy,
                amount: (1 * baseRate) + (1 * levy),
            });
        }
    };

    // Handle form submission
    const handleSubmit = async (paymentMode: string) => {
        // Validate
        if (!branchId) {
            toast.error('Please select a branch');
            return;
        }
        if (!ferryId) {
            toast.error('Please select a ferry');
            return;
        }

        const validLines = lineItems.filter(l => l.itemId);
        if (validLines.length === 0) {
            toast.error('Please add at least one item');
            return;
        }

        setIsSubmitting(true);
        setIsPaymentModalOpen(false);

        try {
            // Build request payload
            const payload = {
                branch_id: branchId,
                ferry_boat_id: ferryId,
                ferry_time: ferryTime || undefined,
                payment_mode: paymentMode,
                customer_name: customerName || undefined,
                customer_mobile: customerMobile || undefined,
                discount_pct: discountPct || undefined,
                discount_rs: discountAmt || undefined,
                lines: validLines.map(l => ({
                    item_name: l.itemName,
                    qty: l.qty,
                    rate: l.rate,
                    levy: l.levy,
                    amount: l.amount,
                    vehicle_name: l.vehicleName || undefined,
                    vehicle_no: l.vehicleNo || undefined,
                })),
            };

            // Submit to API
            const result = await ticketService.createTicket(payload as any);

            toast.success(`Ticket #${result.ticket_number} created successfully!`);

            // Reset form
            setLineItems([createEmptyLine()]);
            setCustomerName('');
            setCustomerMobile('');
            setDiscountPct(0);
            setDiscountAmt(0);

            if (printAfterSave) {
                // Open print in new window
                window.open(`/tickets/${result.id}/print`, '_blank');
            }
        } catch (error: any) {
            console.error('Ticket creation error:', error);
            const message = error.response?.data?.message || 'Failed to create ticket';
            toast.error(message);
        } finally {
            setIsSubmitting(false);
        }
    };

    // Mock ferry times based on selected ferry
    const ferryTimes = [
        '08:00', '09:00', '10:00', '11:00', '12:00',
        '14:00', '15:00', '16:00', '17:00', '18:00'
    ];

    return (
        <div className="space-y-6">
            {/* Header */}
            <div className="flex items-center justify-between">
                <div>
                    <h1 className="text-2xl font-bold text-gray-900">Ticket Entry</h1>
                    <p className="text-gray-500">Create new ferry ticket</p>
                </div>
                <Badge variant="success">
                    <Clock className="w-3 h-3 mr-1" />
                    Active Schedule
                </Badge>
            </div>

            {/* Main Grid - Trip Info + Passenger Details */}
            <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {/* Trip Information */}
                <Card className="lg:col-span-2">
                    <CardHeader>
                        <CardTitle className="flex items-center gap-2">
                            <Ship className="w-5 h-5 text-blue-600" />
                            Trip Information
                        </CardTitle>
                    </CardHeader>
                    <CardContent className="space-y-4">
                        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                            {/* Branch */}
                            <div>
                                <label className="flex items-center gap-2 text-sm font-medium text-gray-700 mb-1">
                                    <MapPin className="w-4 h-4 text-gray-400" />
                                    Branch
                                </label>
                                <Select
                                    value={branchId?.toString() || ''}
                                    onChange={(e) => {
                                        setBranchId(Number(e.target.value) || null);
                                        setFerryId(null);
                                    }}
                                    disabled={branchesLoading || user?.role_id === 3}
                                >
                                    <option value="">-- Select Branch --</option>
                                    {branches.map((branch: any) => (
                                        <option key={branch.id} value={branch.id}>
                                            {branch.name || branch.branch_name}
                                        </option>
                                    ))}
                                </Select>
                            </div>

                            {/* Ferry */}
                            <div>
                                <label className="flex items-center gap-2 text-sm font-medium text-gray-700 mb-1">
                                    <Ship className="w-4 h-4 text-gray-400" />
                                    Ferry Boat
                                </label>
                                <Select
                                    value={ferryId?.toString() || ''}
                                    onChange={(e) => setFerryId(Number(e.target.value) || null)}
                                    disabled={!branchId || ferriesLoading}
                                >
                                    <option value="">-- Select Ferry --</option>
                                    {ferries.map((ferry: any) => (
                                        <option key={ferry.id} value={ferry.id}>
                                            {ferry.name}
                                        </option>
                                    ))}
                                </Select>
                            </div>

                            {/* Ferry Time */}
                            <div>
                                <label className="flex items-center gap-2 text-sm font-medium text-gray-700 mb-1">
                                    <Clock className="w-4 h-4 text-gray-400" />
                                    Schedule Time
                                </label>
                                <Select
                                    value={ferryTime}
                                    onChange={(e) => setFerryTime(e.target.value)}
                                >
                                    <option value="">-- Select Time --</option>
                                    {ferryTimes.map((time) => (
                                        <option key={time} value={time}>
                                            {time}
                                        </option>
                                    ))}
                                </Select>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                {/* Passenger Details */}
                <Card>
                    <CardHeader>
                        <CardTitle className="flex items-center gap-2">
                            <User className="w-5 h-5 text-blue-600" />
                            Passenger Details
                        </CardTitle>
                    </CardHeader>
                    <CardContent className="space-y-4">
                        <div>
                            <label className="text-sm font-medium text-gray-700 mb-1 block">Customer Name</label>
                            <Input
                                value={customerName}
                                onChange={(e) => setCustomerName(e.target.value)}
                                placeholder="Enter name (optional)"
                            />
                        </div>
                        <div>
                            <label className="text-sm font-medium text-gray-700 mb-1 block">Mobile Number</label>
                            <Input
                                value={customerMobile}
                                onChange={(e) => setCustomerMobile(e.target.value)}
                                placeholder="+91 XXXXX XXXXX"
                            />
                        </div>

                        <div className="bg-yellow-50 border border-yellow-200 rounded-lg p-3 text-sm">
                            <p className="font-medium text-yellow-800">Note:</p>
                            <p className="text-yellow-700">Verify ID proof for non-local passengers.</p>
                        </div>
                    </CardContent>
                </Card>
            </div>

            {/* Line Items */}
            <Card>
                <CardHeader className="flex-row items-center justify-between">
                    <CardTitle>Line Items</CardTitle>
                    <Button variant="outline" size="sm" onClick={addLine}>
                        <Plus className="w-4 h-4" />
                        Add Row
                    </Button>
                </CardHeader>
                <CardContent className="p-0">
                    <div className="overflow-x-auto">
                        <table className="w-full">
                            <thead className="bg-gray-50 border-b">
                                <tr>
                                    <th className="px-3 py-3 text-left text-xs font-semibold text-gray-600 uppercase w-12">#</th>
                                    <th className="px-3 py-3 text-left text-xs font-semibold text-gray-600 uppercase min-w-[200px]">Item</th>
                                    <th className="px-3 py-3 text-left text-xs font-semibold text-gray-600 uppercase w-20">Qty</th>
                                    <th className="px-3 py-3 text-left text-xs font-semibold text-gray-600 uppercase w-24">Rate</th>
                                    <th className="px-3 py-3 text-left text-xs font-semibold text-gray-600 uppercase w-20">Levy</th>
                                    <th className="px-3 py-3 text-left text-xs font-semibold text-gray-600 uppercase w-28">Amount</th>
                                    <th className="px-3 py-3 text-left text-xs font-semibold text-gray-600 uppercase w-32">Vehicle Name</th>
                                    <th className="px-3 py-3 text-left text-xs font-semibold text-gray-600 uppercase w-28">Vehicle No</th>
                                    <th className="px-3 py-3 text-center text-xs font-semibold text-gray-600 uppercase w-16">-</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y">
                                {lineItems.map((line, index) => (
                                    <tr key={line.id} className="hover:bg-gray-50">
                                        <td className="px-3 py-2 text-gray-500 text-center">{index + 1}</td>
                                        <td className="px-3 py-2">
                                            <Select
                                                value={line.itemId?.toString() || ''}
                                                onChange={(e) => handleItemSelect(line.id, Number(e.target.value))}
                                                disabled={ratesLoading || !branchId}
                                                className="text-sm"
                                            >
                                                <option value="">-- Select Item --</option>
                                                {rates.map((rate: any) => (
                                                    <option key={rate.id} value={rate.id}>
                                                        {rate.item_name} - {formatCurrency(rate.price || rate.item_rate || 0)}
                                                    </option>
                                                ))}
                                            </Select>
                                        </td>
                                        <td className="px-3 py-2">
                                            <Input
                                                type="number"
                                                min="1"
                                                value={line.qty}
                                                onChange={(e) => updateLine(line.id, { qty: Number(e.target.value) || 1 })}
                                                className="text-right text-sm w-full"
                                            />
                                        </td>
                                        <td className="px-3 py-2">
                                            <Input value={line.rate.toFixed(2)} readOnly className="text-right text-sm bg-gray-50" />
                                        </td>
                                        <td className="px-3 py-2">
                                            <Input value={line.levy.toFixed(2)} readOnly className="text-right text-sm bg-gray-50" />
                                        </td>
                                        <td className="px-3 py-2">
                                            <Input value={line.amount.toFixed(2)} readOnly className="text-right text-sm font-medium bg-gray-50" />
                                        </td>
                                        <td className="px-3 py-2">
                                            <Input
                                                value={line.vehicleName}
                                                onChange={(e) => updateLine(line.id, { vehicleName: e.target.value })}
                                                placeholder="Optional"
                                                className="text-sm"
                                            />
                                        </td>
                                        <td className="px-3 py-2">
                                            <Input
                                                value={line.vehicleNo}
                                                onChange={(e) => updateLine(line.id, { vehicleNo: e.target.value })}
                                                placeholder="Optional"
                                                className="text-sm"
                                            />
                                        </td>
                                        <td className="px-3 py-2 text-center">
                                            <Button
                                                variant="ghost"
                                                size="icon"
                                                onClick={() => removeLine(line.id)}
                                                disabled={lineItems.length === 1}
                                                className="text-red-500 hover:text-red-700 hover:bg-red-50"
                                            >
                                                <Trash2 className="w-4 h-4" />
                                            </Button>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>

                    {/* Footer with totals and actions */}
                    <div className="bg-gray-50 border-t p-4">
                        <div className="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                            {/* Print checkbox */}
                            <label className="flex items-center gap-2 text-sm cursor-pointer">
                                <input
                                    type="checkbox"
                                    checked={printAfterSave}
                                    onChange={(e) => setPrintAfterSave(e.target.checked)}
                                    className="w-4 h-4 accent-blue-600"
                                />
                                <Printer className="w-4 h-4 text-gray-500" />
                                Print receipt automatically
                            </label>

                            {/* Totals and actions */}
                            <div className="flex flex-col sm:flex-row items-end gap-6">
                                {/* Summary */}
                                <div className="space-y-2 text-right">
                                    <div className="flex items-center justify-end gap-4">
                                        <span className="text-sm text-gray-600">Subtotal:</span>
                                        <span className="font-semibold w-24">{formatCurrency(subtotal)}</span>
                                    </div>
                                    <div className="flex items-center justify-end gap-2">
                                        <span className="text-sm text-gray-600">Discount %:</span>
                                        <Input
                                            type="number"
                                            min="0"
                                            max="100"
                                            value={discountPct}
                                            onChange={(e) => setDiscountPct(Number(e.target.value) || 0)}
                                            className="w-16 text-right text-sm"
                                        />
                                    </div>
                                    <div className="flex items-center justify-end gap-2">
                                        <span className="text-sm text-gray-600">Discount ₹:</span>
                                        <Input
                                            type="number"
                                            min="0"
                                            value={discountAmt}
                                            onChange={(e) => setDiscountAmt(Number(e.target.value) || 0)}
                                            className="w-16 text-right text-sm"
                                        />
                                    </div>
                                    <div className="flex items-center justify-end gap-4 pt-2 border-t">
                                        <span className="text-sm font-medium text-gray-700">Net Total:</span>
                                        <span className="text-xl font-bold text-green-600 w-24">{formatCurrency(netTotal)}</span>
                                    </div>
                                </div>

                                {/* Action buttons */}
                                <div className="flex flex-col gap-2">
                                    <Button
                                        onClick={() => setIsPaymentModalOpen(true)}
                                        disabled={isSubmitting || netTotal <= 0}
                                        loading={isSubmitting}
                                        className="bg-gradient-to-r from-blue-600 to-indigo-600 shadow-lg"
                                    >
                                        <Save className="w-4 h-4" />
                                        Save Ticket
                                    </Button>
                                    <Button
                                        variant="outline"
                                        onClick={() => setIsPaymentModalOpen(true)}
                                        disabled={isSubmitting || netTotal <= 0}
                                    >
                                        <CreditCard className="w-4 h-4" />
                                        Pay & Save
                                    </Button>
                                </div>
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>

            {/* Payment Modal */}
            <PaymentModal
                isOpen={isPaymentModalOpen}
                onClose={() => setIsPaymentModalOpen(false)}
                total={netTotal}
                onConfirm={handleSubmit}
            />
        </div>
    );
}
