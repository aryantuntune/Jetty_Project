// Root Navigator following the migration guide
import React, { useEffect, useState } from 'react';
import { useAppSelector, useAppDispatch } from '@/store';
import { checkAuthStatus } from '@/store/slices/authSlice';
import { AuthNavigator } from './AuthNavigator';
import { MainNavigator } from './MainNavigator';
import { Loading } from '@/components/common';

export const RootNavigator: React.FC = () => {
    const dispatch = useAppDispatch();
    const { isAuthenticated, isLoading } = useAppSelector((state) => state.auth);

    // Track if initial auth check is complete (separate from isLoading)
    const [initialCheckDone, setInitialCheckDone] = useState(false);

    useEffect(() => {
        const checkAuth = async () => {
            await dispatch(checkAuthStatus());
            setInitialCheckDone(true);
        };
        checkAuth();
    }, [dispatch]);

    // Only show loading during INITIAL auth check, not during API calls
    // This prevents unmounting navigators during register/login
    if (!initialCheckDone) {
        return <Loading message="Loading..." />;
    }

    // Conditionally render based on auth state
    return isAuthenticated ? <MainNavigator /> : <AuthNavigator />;
};

export default RootNavigator;
