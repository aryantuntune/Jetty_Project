import { useQuery } from '@tanstack/react-query';
import { branchService } from '@/services/branchService';

export function useBranches() {
    return useQuery({
        queryKey: ['branches'],
        queryFn: branchService.getBranches,
        staleTime: 10 * 60 * 1000, // 10 minutes - branches don't change often
    });
}

export function useDestinationBranches(branchId?: number) {
    return useQuery({
        queryKey: ['destination-branches', branchId],
        queryFn: () => branchService.getDestinationBranches(branchId!),
        enabled: !!branchId,
        staleTime: 10 * 60 * 1000,
    });
}

export function useFerries(branchId?: number) {
    return useQuery({
        queryKey: ['ferries', branchId],
        queryFn: () => branchService.getFerries(branchId!),
        enabled: !!branchId,
        staleTime: 5 * 60 * 1000,
    });
}

export function useSchedules(branchId?: number) {
    return useQuery({
        queryKey: ['schedules', branchId],
        queryFn: () => branchService.getSchedules(branchId!),
        enabled: !!branchId,
        staleTime: 60 * 1000, // 1 minute
    });
}

export function useRates(branchId?: number) {
    return useQuery({
        queryKey: ['rates', branchId],
        queryFn: () => branchService.getRates(branchId!),
        enabled: !!branchId,
        staleTime: 5 * 60 * 1000,
    });
}
