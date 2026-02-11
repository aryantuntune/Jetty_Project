import { create } from 'zustand';
import { CreateTicketItem } from '@/types/ticket';

interface TicketFormState {
    branchId: number | null;
    destBranchId: number | null;
    ferryId: number | null;
    ferryTime: string | null;
    paymentMode: string;
    items: CreateTicketItem[];

    setBranch: (id: number) => void;
    setDestBranch: (id: number) => void;
    setFerry: (id: number) => void;
    setFerryTime: (time: string) => void;
    setPaymentMode: (mode: string) => void;
    addItem: (item: CreateTicketItem) => void;
    removeItem: (index: number) => void;
    updateItem: (index: number, item: CreateTicketItem) => void;
    clearForm: () => void;
    getTotalAmount: () => number;
}

export const useTicketStore = create<TicketFormState>((set, get) => ({
    branchId: null,
    destBranchId: null,
    ferryId: null,
    ferryTime: null,
    paymentMode: 'Cash',
    items: [],

    setBranch: (id) => set({ branchId: id, destBranchId: null, ferryId: null }),
    setDestBranch: (id) => set({ destBranchId: id }),
    setFerry: (id) => set({ ferryId: id }),
    setFerryTime: (time) => set({ ferryTime: time }),
    setPaymentMode: (mode) => set({ paymentMode: mode }),

    addItem: (item) => set((state) => ({
        items: [...state.items, item]
    })),

    removeItem: (index) => set((state) => ({
        items: state.items.filter((_, i) => i !== index)
    })),

    updateItem: (index, item) => set((state) => ({
        items: state.items.map((existingItem, i) =>
            i === index ? item : existingItem
        ),
    })),

    clearForm: () => set({
        branchId: null,
        destBranchId: null,
        ferryId: null,
        ferryTime: null,
        paymentMode: 'Cash',
        items: [],
    }),

    getTotalAmount: () => {
        const state = get();
        return state.items.reduce((sum, item) => sum + item.amount, 0);
    },
}));
