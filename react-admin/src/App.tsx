import { BrowserRouter, Routes, Route, Navigate } from 'react-router-dom';
import { QueryClient, QueryClientProvider } from '@tanstack/react-query';
import { Toaster } from 'sonner';

// Layouts
import { Layout } from './components/layout';
import { PublicLayout, CustomerLayout } from './components/layouts';

// Auth pages
import { Login } from './pages/auth';

// Public pages
import { Home, About, Contact, FerryRoutes, FerryRouteDetail, HouseboatBooking, PrivacyPolicy, TermsOfService, RefundPolicy } from './pages/public';

// Customer pages
import {
  CustomerLogin,
  CustomerRegister,
  CustomerDashboard,
  BookTicket,
  BookingHistory,
  CustomerProfile
} from './pages/customer';

// Admin pages
import { Dashboard } from './pages/dashboard';
import { TicketEntry, TicketList, TicketVerify, TicketPrint } from './pages/tickets';
import { Reports } from './pages/reports';
import { Branches, Ferries, Rates, Schedules, SpecialCharges, ItemCategories } from './pages/masters';
import { Users, Operators, Managers, Checkers, EmployeeTransfer } from './pages/users';
import { Settings } from './pages/settings';
import { Guests, GuestCategories } from './pages/guests';

// Stores
import { useAuthStore } from './store';
import { useCustomerAuthStore } from './store/customerAuthStore';

import './index.css';

// Create a client
const queryClient = new QueryClient({
  defaultOptions: {
    queries: {
      retry: 1,
      refetchOnWindowFocus: false,
      staleTime: 5 * 60 * 1000,
    },
  },
});

// Admin Protected Route
function AdminProtectedRoute({ children }: { children: React.ReactNode }) {
  const { isAuthenticated } = useAuthStore();
  if (!isAuthenticated) return <Navigate to="/admin/login" replace />;
  return <>{children}</>;
}

// Customer Protected Route
function CustomerProtectedRoute({ children }: { children: React.ReactNode }) {
  const { isAuthenticated } = useCustomerAuthStore();
  if (!isAuthenticated) return <Navigate to="/customer/login" replace />;
  return <>{children}</>;
}

// Admin Public Route (Login page)
function AdminPublicRoute({ children }: { children: React.ReactNode }) {
  const { isAuthenticated } = useAuthStore();
  if (isAuthenticated) return <Navigate to="/admin/dashboard" replace />;
  return <>{children}</>;
}

function App() {
  return (
    <QueryClientProvider client={queryClient}>
      <BrowserRouter>
        <Routes>
          {/* ===================== PUBLIC WEBSITE ===================== */}
          <Route element={<PublicLayout />}>
            <Route path="/" element={<Home />} />
            <Route path="/about" element={<About />} />
            <Route path="/contact" element={<Contact />} />
            <Route path="/routes" element={<FerryRoutes />} />
            <Route path="/routes/:routeId" element={<FerryRouteDetail />} />
            <Route path="/houseboat" element={<HouseboatBooking />} />
            <Route path="/privacy" element={<PrivacyPolicy />} />
            <Route path="/terms" element={<TermsOfService />} />
            <Route path="/refund-policy" element={<RefundPolicy />} />
          </Route>

          {/* ===================== CUSTOMER AUTH ===================== */}
          <Route path="/customer/login" element={<CustomerLogin />} />
          <Route path="/customer/register" element={<CustomerRegister />} />

          {/* ===================== CUSTOMER PROTECTED ===================== */}
          <Route
            path="/customer"
            element={
              <CustomerProtectedRoute>
                <CustomerLayout />
              </CustomerProtectedRoute>
            }
          >
            <Route index element={<Navigate to="/customer/dashboard" replace />} />
            <Route path="dashboard" element={<CustomerDashboard />} />
            <Route path="book" element={<BookTicket />} />
            <Route path="history" element={<BookingHistory />} />
            <Route path="profile" element={<CustomerProfile />} />
          </Route>

          {/* ===================== ADMIN AUTH ===================== */}
          <Route path="/admin/login" element={<AdminPublicRoute><Login /></AdminPublicRoute>} />
          <Route path="/login" element={<Navigate to="/admin/login" replace />} />

          {/* ===================== ADMIN PROTECTED ===================== */}
          <Route path="/tickets/print" element={<TicketPrint />} />

          <Route
            path="/admin"
            element={
              <AdminProtectedRoute>
                <Layout />
              </AdminProtectedRoute>
            }
          >
            <Route index element={<Navigate to="/admin/dashboard" replace />} />
            <Route path="dashboard" element={<Dashboard />} />

            {/* Tickets */}
            <Route path="tickets/entry" element={<TicketEntry />} />
            <Route path="tickets/verify" element={<TicketVerify />} />
            <Route path="tickets" element={<TicketList />} />

            {/* Reports */}
            <Route path="reports" element={<Reports />} />

            {/* Masters */}
            <Route path="masters/branches" element={<Branches />} />
            <Route path="masters/ferries" element={<Ferries />} />
            <Route path="masters/schedules" element={<Schedules />} />
            <Route path="masters/rates" element={<Rates />} />
            <Route path="masters/special-charges" element={<SpecialCharges />} />
            <Route path="masters/item-categories" element={<ItemCategories />} />

            {/* Guests */}
            <Route path="guests" element={<Guests />} />
            <Route path="guests/categories" element={<GuestCategories />} />

            {/* Users */}
            <Route path="users" element={<Users />} />
            <Route path="users/operators" element={<Operators />} />
            <Route path="users/managers" element={<Managers />} />
            <Route path="users/checkers" element={<Checkers />} />
            <Route path="users/transfer" element={<EmployeeTransfer />} />

            {/* Settings */}
            <Route path="settings" element={<Settings />} />
          </Route>

          {/* Legacy routes - redirect to new paths */}
          <Route path="/dashboard" element={<Navigate to="/admin/dashboard" replace />} />
          <Route path="/tickets/*" element={<Navigate to="/admin/tickets" replace />} />
          <Route path="/reports" element={<Navigate to="/admin/reports" replace />} />
          <Route path="/masters/*" element={<Navigate to="/admin/masters/branches" replace />} />
          <Route path="/users/*" element={<Navigate to="/admin/users" replace />} />
          <Route path="/guests/*" element={<Navigate to="/admin/guests" replace />} />
          <Route path="/settings" element={<Navigate to="/admin/settings" replace />} />

          {/* 404 */}
          <Route path="*" element={<Navigate to="/" replace />} />
        </Routes>
      </BrowserRouter>
      <Toaster position="top-right" richColors />
    </QueryClientProvider>
  );
}

export default App;
