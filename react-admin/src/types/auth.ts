export interface User {
    id: number;
    name: string;
    email: string;
    role_id: number;
    role_name?: string;
    branch_id?: number;
    branch_name?: string;
    ferry_boat_id?: number;
}

export interface LoginRequest {
    email: string;
    password: string;
    remember?: boolean;
}

export interface LoginResponse {
    user: User;
    token?: string;
    message?: string;
}

export type UserRole = 'admin' | 'manager' | 'operator' | 'checker';

export const ROLE_IDS = {
    ADMIN: 1,
    MANAGER: 2,
    OPERATOR: 3,
    CHECKER: 5,
} as const;
