export interface Branch {
    id: number;
    branch_id: string;
    branch_name: string;
    branch_address?: string;
    branch_phone?: string;
    dest_branch_id?: number;
    dest_branch_name?: string;
    is_active: boolean;
}

export interface Ferry {
    id: number;
    number: string;
    name: string;
    branch_id?: number;
    capacity_passengers?: number;
    capacity_vehicles?: number;
    is_active: boolean;
}

export interface FerrySchedule {
    id: number;
    hour: number;
    minute: number;
    schedule_time?: string;
    branch_id: number;
    ferry_boat_id: number;
    is_active: boolean;
}

export interface ItemRate {
    id: number;
    item_name: string;
    item_rate: number;
    item_lavy: number;
    branch_id?: number;
    route_id?: string;
    is_vehicle: boolean;
    is_fixed_rate: boolean;
    starting_date: string;
    ending_date?: string;
    is_active: boolean;
}

export interface ItemCategory {
    id: number;
    category_name: string;
    is_active: boolean;
}
