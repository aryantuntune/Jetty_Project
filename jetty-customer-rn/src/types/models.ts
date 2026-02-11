// Model types - Updated to match actual API responses

export interface Customer {
    id: number;
    firstName: string;
    lastName: string;
    email: string;
    mobile: string;
    profileImage?: string;
    googleId?: string;
}

export interface Branch {
    id: number;
    branchName: string;
    destBranchId?: number;
    destBranchName?: string;
}

export interface Ferry {
    id: number;
    name: string;
    number: string;
    capacity?: number;
}

// ItemRate matches the actual API: {id, item_name, price, description}
export interface ItemRate {
    id: number;
    itemName: string;
    price: number;
    description?: string;
}

export interface BookingItem {
    itemRateId: number;  // Required by backend API
    itemName: string;
    qty: number;
    rate: number;
    amount: number;
    vehicleNo?: string;
}

export interface Ticket {
    id: number;
    ticketNo: string;
    ticketDate: string;
    branch: string;
    destBranch?: string;
    ferryBoat: string;
    ferryTime: string;
    totalAmount: number;
    verifiedAt?: string;
    qrCode?: string;
}

export interface Booking {
    id: number;
    customerId: number;
    fromBranch: string;
    toBranch?: string;
    ferryBoat: string;
    ferryTime: string;
    items: BookingItem[];
    totalAmount: number;
    paymentId?: string;
    status: 'pending' | 'confirmed' | 'cancelled' | 'completed';
    ticketId?: number;
    ticket?: Ticket;
    qrCode?: string;
    verifiedAt?: string;
    createdAt: string;
}
