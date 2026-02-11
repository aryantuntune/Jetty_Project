import apiClient from '@/lib/axios';
import { Branch, Ferry, FerrySchedule, ItemRate } from '@/types/branch';

// Helper to extract data from Laravel API response wrapper
function extractData<T>(response: any): T {
    // Laravel APIs return { success: true, data: [...] }
    if (response.data && typeof response.data === 'object' && 'data' in response.data) {
        return response.data.data;
    }
    // Direct array response
    return response.data;
}

export const branchService = {
    /**
     * Get all branches
     */
    async getBranches(): Promise<Branch[]> {
        const response = await apiClient.get('/api/branches');
        return extractData<Branch[]>(response);
    },

    /**
     * Get ferries for a branch
     */
    async getFerries(branchId: number): Promise<Ferry[]> {
        const response = await apiClient.get(`/api/ferries/branch/${branchId}`);
        return extractData<Ferry[]>(response);
    },

    /**
     * Get ferry schedules for a branch
     */
    async getSchedules(branchId: number): Promise<FerrySchedule[]> {
        const response = await apiClient.get(`/ajax/next-ferry-time`, {
            params: { branch_id: branchId }
        });
        return extractData<FerrySchedule[]>(response);
    },

    /**
     * Get item rates for a branch
     */
    async getRates(branchId: number): Promise<ItemRate[]> {
        const response = await apiClient.get(`/api/rates/branch/${branchId}`);
        return extractData<ItemRate[]>(response);
    },

    /**
     * Get destination branches for a route
     */
    async getDestinationBranches(branchId: number): Promise<Branch[]> {
        const response = await apiClient.get(`/booking/to-branches/${branchId}`);
        return extractData<Branch[]>(response);
    },
};
