# REACT ADMIN PANEL BUILD GUIDE - Jetty Ferry Management System
## Modern Frontend with React, TypeScript, Vite, TailwindCSS

---

## 🎯 OBJECTIVE

Build a modern, fast, and beautiful React-based admin panel to replace Laravel Blade templates.

**What we're building:**
- ✅ Admin dashboard (ticket entry, reports, management)
- ✅ Operator interface (counter ticket sales)
- ✅ Manager interface (reports and analytics)
- ✅ Checker interface (basic verification panel)

**What we're NOT building:**
- ❌ Public website (keep Blade for now, or do later)
- ❌ Customer portal (keep Blade/Flutter apps)

---

## 🏗️ TECH STACK

### Core
- **React 18.3** - UI library
- **TypeScript 5.3** - Type safety
- **Vite 5.0** - Build tool (blazing fast)
- **React Router 6.20** - Routing

### UI & Styling
- **TailwindCSS 3.4** - Utility-first CSS
- **Shadcn/ui** - Beautiful component library
- **Lucide React** - Icons
- **Recharts** - Charts & graphs

### State Management
- **TanStack Query (React Query) 5.0** - Server state
- **Zustand 4.4** - Client state (simple & fast)

### Forms & Validation
- **React Hook Form 7.49** - Form handling
- **Zod 3.22** - Schema validation

### API & Auth
- **Axios 1.6** - HTTP client
- **Laravel Sanctum** - Authentication

### Dev Tools
- **ESLint** - Code linting
- **Prettier** - Code formatting
- **TypeScript** - Type checking

---

## 📂 PROJECT STRUCTURE

```
jetty-admin-react/                    # New React project
├── public/
│   ├── favicon.ico
│   └── logo.png
├── src/
│   ├── main.tsx                      # App entry point
│   ├── App.tsx                       # Root component
│   ├── vite-env.d.ts                 # Vite types
│   │
│   ├── config/
│   │   ├── api.ts                    # API base URL, headers
│   │   └── routes.ts                 # Route paths
│   │
│   ├── types/
│   │   ├── auth.ts                   # User, Login types
│   │   ├── ticket.ts                 # Ticket types
│   │   ├── booking.ts                # Booking types
│   │   ├── branch.ts                 # Branch types
│   │   ├── ferry.ts                  # Ferry types
│   │   └── api.ts                    # API response types
│   │
│   ├── lib/
│   │   ├── axios.ts                  # Axios instance
│   │   ├── utils.ts                  # Helper functions
│   │   └── cn.ts                     # Class name utility
│   │
│   ├── hooks/
│   │   ├── useAuth.ts                # Authentication hook
│   │   ├── useTickets.ts             # Tickets queries
│   │   ├── useBookings.ts            # Bookings queries
│   │   ├── useBranches.ts            # Branches queries
│   │   └── useReports.ts             # Reports queries
│   │
│   ├── store/
│   │   ├── authStore.ts              # Auth state (Zustand)
│   │   └── ticketStore.ts            # Ticket form state
│   │
│   ├── components/
│   │   ├── ui/                       # Shadcn components
│   │   │   ├── button.tsx
│   │   │   ├── input.tsx
│   │   │   ├── card.tsx
│   │   │   ├── table.tsx
│   │   │   ├── dialog.tsx
│   │   │   ├── select.tsx
│   │   │   ├── badge.tsx
│   │   │   └── ...
│   │   │
│   │   ├── layout/
│   │   │   ├── Sidebar.tsx           # Navigation sidebar
│   │   │   ├── Header.tsx            # Top header
│   │   │   ├── Layout.tsx            # Main layout wrapper
│   │   │   └── ProtectedRoute.tsx    # Auth guard
│   │   │
│   │   ├── dashboard/
│   │   │   ├── StatsCard.tsx         # Statistics cards
│   │   │   ├── RevenueChart.tsx      # Revenue chart
│   │   │   ├── RecentTickets.tsx     # Recent tickets list
│   │   │   └── QuickActions.tsx      # Quick action buttons
│   │   │
│   │   ├── tickets/
│   │   │   ├── TicketEntryForm.tsx   # Main ticket form
│   │   │   ├── PassengerRow.tsx      # Passenger input row
│   │   │   ├── VehicleRow.tsx        # Vehicle input row
│   │   │   ├── TicketSummary.tsx     # Total calculation
│   │   │   └── TicketPrint.tsx       # Print view
│   │   │
│   │   ├── reports/
│   │   │   ├── ReportFilters.tsx     # Date/branch filters
│   │   │   ├── TicketReport.tsx      # Ticket reports
│   │   │   ├── VehicleReport.tsx     # Vehicle reports
│   │   │   └── DailySummary.tsx      # Daily summary
│   │   │
│   │   └── common/
│   │       ├── LoadingSpinner.tsx
│   │       ├── ErrorMessage.tsx
│   │       ├── SearchInput.tsx
│   │       ├── DatePicker.tsx
│   │       └── Pagination.tsx
│   │
│   ├── pages/
│   │   ├── auth/
│   │   │   ├── Login.tsx
│   │   │   └── ForgotPassword.tsx
│   │   │
│   │   ├── dashboard/
│   │   │   └── Dashboard.tsx
│   │   │
│   │   ├── tickets/
│   │   │   ├── TicketEntry.tsx       # Counter ticket sales
│   │   │   ├── TicketList.tsx        # All tickets
│   │   │   ├── TicketDetail.tsx      # Ticket details
│   │   │   └── TicketVerify.tsx      # Verify tickets
│   │   │
│   │   ├── bookings/
│   │   │   ├── BookingList.tsx       # Online bookings
│   │   │   └── BookingDetail.tsx     # Booking details
│   │   │
│   │   ├── reports/
│   │   │   ├── TicketReports.tsx
│   │   │   ├── VehicleReports.tsx
│   │   │   └── DailySummary.tsx
│   │   │
│   │   ├── masters/
│   │   │   ├── Branches.tsx          # Branch CRUD
│   │   │   ├── Ferries.tsx           # Ferry CRUD
│   │   │   ├── Schedules.tsx         # Schedule CRUD
│   │   │   └── Rates.tsx             # Rate CRUD
│   │   │
│   │   ├── users/
│   │   │   ├── Admins.tsx
│   │   │   ├── Managers.tsx
│   │   │   ├── Operators.tsx
│   │   │   └── Checkers.tsx
│   │   │
│   │   └── NotFound.tsx
│   │
│   ├── services/
│   │   ├── authService.ts            # Login, logout
│   │   ├── ticketService.ts          # Ticket CRUD
│   │   ├── bookingService.ts         # Booking operations
│   │   ├── branchService.ts          # Branch operations
│   │   ├── ferryService.ts           # Ferry operations
│   │   └── reportService.ts          # Report generation
│   │
│   ├── utils/
│   │   ├── formatters.ts             # Date, currency formatters
│   │   ├── validators.ts             # Form validators
│   │   ├── constants.ts              # App constants
│   │   └── helpers.ts                # Helper functions
│   │
│   └── styles/
│       ├── globals.css               # Global styles
│       └── tailwind.css              # Tailwind imports
│
├── .env                               # Environment variables
├── .env.example                       # Env template
├── .gitignore
├── eslint.config.js
├── index.html
├── package.json
├── postcss.config.js
├── tailwind.config.js
├── tsconfig.json
├── tsconfig.node.json
└── vite.config.ts
```

---

## 🚀 STEP 1: PROJECT SETUP

### 1.1: Create React Project

```bash
# Navigate to your projects directory (NOT inside Laravel)
cd /var/www  # or wherever you keep projects

# Create Vite + React + TypeScript project
npm create vite@latest jetty-admin-react -- --template react-ts

# Navigate to project
cd jetty-admin-react

# Install dependencies
npm install
```

### 1.2: Install Core Dependencies

```bash
# Routing
npm install react-router-dom

# State Management
npm install @tanstack/react-query zustand

# Forms & Validation
npm install react-hook-form zod @hookform/resolvers

# API Client
npm install axios

# UI Components & Styling
npm install tailwindcss postcss autoprefixer
npx tailwindcss init -p

# Shadcn UI (component library)
npm install class-variance-authority clsx tailwind-merge

# Icons
npm install lucide-react

# Charts
npm install recharts

# Date handling
npm install date-fns

# Utilities
npm install sonner  # Toast notifications
```

### 1.3: Install Dev Dependencies

```bash
npm install -D @types/node
npm install -D prettier eslint-config-prettier
```

---

## ⚙️ STEP 2: CONFIGURE PROJECT

### 2.1: Setup TailwindCSS

**File: `tailwind.config.js`**

```javascript
/** @type {import('tailwindcss').Config} */
export default {
  darkMode: ["class"],
  content: [
    './pages/**/*.{ts,tsx}',
    './components/**/*.{ts,tsx}',
    './app/**/*.{ts,tsx}',
    './src/**/*.{ts,tsx}',
  ],
  theme: {
    container: {
      center: true,
      padding: "2rem",
      screens: {
        "2xl": "1400px",
      },
    },
    extend: {
      colors: {
        border: "hsl(var(--border))",
        input: "hsl(var(--input))",
        ring: "hsl(var(--ring))",
        background: "hsl(var(--background))",
        foreground: "hsl(var(--foreground))",
        primary: {
          DEFAULT: "hsl(var(--primary))",
          foreground: "hsl(var(--primary-foreground))",
        },
        secondary: {
          DEFAULT: "hsl(var(--secondary))",
          foreground: "hsl(var(--secondary-foreground))",
        },
        destructive: {
          DEFAULT: "hsl(var(--destructive))",
          foreground: "hsl(var(--destructive-foreground))",
        },
        muted: {
          DEFAULT: "hsl(var(--muted))",
          foreground: "hsl(var(--muted-foreground))",
        },
        accent: {
          DEFAULT: "hsl(var(--accent))",
          foreground: "hsl(var(--accent-foreground))",
        },
        popover: {
          DEFAULT: "hsl(var(--popover))",
          foreground: "hsl(var(--popover-foreground))",
        },
        card: {
          DEFAULT: "hsl(var(--card))",
          foreground: "hsl(var(--card-foreground))",
        },
      },
      borderRadius: {
        lg: "var(--radius)",
        md: "calc(var(--radius) - 2px)",
        sm: "calc(var(--radius) - 4px)",
      },
      keyframes: {
        "accordion-down": {
          from: { height: 0 },
          to: { height: "var(--radix-accordion-content-height)" },
        },
        "accordion-up": {
          from: { height: "var(--radix-accordion-content-height)" },
          to: { height: 0 },
        },
      },
      animation: {
        "accordion-down": "accordion-down 0.2s ease-out",
        "accordion-up": "accordion-up 0.2s ease-out",
      },
    },
  },
  plugins: [require("tailwindcss-animate")],
}
```

### 2.2: Global Styles

**File: `src/styles/globals.css`**

```css
@tailwind base;
@tailwind components;
@tailwind utilities;

@layer base {
  :root {
    --background: 0 0% 100%;
    --foreground: 222.2 84% 4.9%;
    --card: 0 0% 100%;
    --card-foreground: 222.2 84% 4.9%;
    --popover: 0 0% 100%;
    --popover-foreground: 222.2 84% 4.9%;
    --primary: 221.2 83.2% 53.3%;
    --primary-foreground: 210 40% 98%;
    --secondary: 210 40% 96.1%;
    --secondary-foreground: 222.2 47.4% 11.2%;
    --muted: 210 40% 96.1%;
    --muted-foreground: 215.4 16.3% 46.9%;
    --accent: 210 40% 96.1%;
    --accent-foreground: 222.2 47.4% 11.2%;
    --destructive: 0 84.2% 60.2%;
    --destructive-foreground: 210 40% 98%;
    --border: 214.3 31.8% 91.4%;
    --input: 214.3 31.8% 91.4%;
    --ring: 221.2 83.2% 53.3%;
    --radius: 0.5rem;
  }

  .dark {
    --background: 222.2 84% 4.9%;
    --foreground: 210 40% 98%;
    --card: 222.2 84% 4.9%;
    --card-foreground: 210 40% 98%;
    --popover: 222.2 84% 4.9%;
    --popover-foreground: 210 40% 98%;
    --primary: 217.2 91.2% 59.8%;
    --primary-foreground: 222.2 47.4% 11.2%;
    --secondary: 217.2 32.6% 17.5%;
    --secondary-foreground: 210 40% 98%;
    --muted: 217.2 32.6% 17.5%;
    --muted-foreground: 215 20.2% 65.1%;
    --accent: 217.2 32.6% 17.5%;
    --accent-foreground: 210 40% 98%;
    --destructive: 0 62.8% 30.6%;
    --destructive-foreground: 210 40% 98%;
    --border: 217.2 32.6% 17.5%;
    --input: 217.2 32.6% 17.5%;
    --ring: 224.3 76.3% 48%;
  }
}

@layer base {
  * {
    @apply border-border;
  }
  body {
    @apply bg-background text-foreground;
  }
}
```

### 2.3: Environment Variables

**File: `.env`**

```bash
# API Configuration
VITE_API_URL=http://localhost  # Local Laravel
# VITE_API_URL=https://carferry.online  # Production

# App Configuration
VITE_APP_NAME=Jetty Ferry Admin
VITE_APP_VERSION=1.0.0

# Features
VITE_ENABLE_DEBUG=true
```

**File: `.env.example`**

```bash
# Copy this to .env and update values
VITE_API_URL=http://localhost
VITE_APP_NAME=Jetty Ferry Admin
VITE_APP_VERSION=1.0.0
VITE_ENABLE_DEBUG=false
```

### 2.4: Vite Configuration

**File: `vite.config.ts`**

```typescript
import { defineConfig } from 'vite'
import react from '@vitejs/plugin-react'
import path from 'path'

// https://vitejs.dev/config/
export default defineConfig({
  plugins: [react()],
  resolve: {
    alias: {
      "@": path.resolve(__dirname, "./src"),
    },
  },
  server: {
    port: 3000,
    proxy: {
      '/api': {
        target: 'http://localhost',  // Your Laravel backend
        changeOrigin: true,
        secure: false,
      }
    }
  },
  build: {
    outDir: 'dist',
    sourcemap: false,
    rollupOptions: {
      output: {
        manualChunks: {
          vendor: ['react', 'react-dom', 'react-router-dom'],
          ui: ['lucide-react', 'recharts'],
        },
      },
    },
  },
})
```

### 2.5: TypeScript Configuration

**File: `tsconfig.json`**

```json
{
  "compilerOptions": {
    "target": "ES2020",
    "useDefineForClassFields": true,
    "lib": ["ES2020", "DOM", "DOM.Iterable"],
    "module": "ESNext",
    "skipLibCheck": true,

    /* Bundler mode */
    "moduleResolution": "bundler",
    "allowImportingTsExtensions": true,
    "resolveJsonModule": true,
    "isolatedModules": true,
    "noEmit": true,
    "jsx": "react-jsx",

    /* Linting */
    "strict": true,
    "noUnusedLocals": true,
    "noUnusedParameters": true,
    "noFallthroughCasesInSwitch": true,

    /* Path Mapping */
    "baseUrl": ".",
    "paths": {
      "@/*": ["./src/*"]
    }
  },
  "include": ["src"],
  "references": [{ "path": "./tsconfig.node.json" }]
}
```

---

## 📝 STEP 3: CREATE TYPE DEFINITIONS

### 3.1: API Types

**File: `src/types/api.ts`**

```typescript
export interface ApiResponse<T> {
  message?: string;
  data?: T;
  error?: string;
}

export interface PaginatedResponse<T> {
  data: T[];
  current_page: number;
  last_page: number;
  per_page: number;
  total: number;
}

export interface ApiError {
  message: string;
  errors?: Record<string, string[]>;
}
```

### 3.2: Auth Types

**File: `src/types/auth.ts`**

```typescript
export interface User {
  id: number;
  name: string;
  email: string;
  role_id: number;
  role_name: string;
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
}
```

### 3.3: Ticket Types

**File: `src/types/ticket.ts`**

```typescript
export interface Ticket {
  id: number;
  ticket_no: string;
  ticket_date: string;
  branch_id: number;
  branch_name: string;
  dest_branch_id: number;
  dest_branch_name: string;
  ferry_boat_id: number;
  ferry_name: string;
  ferry_time: string;
  payment_mode: 'Cash' | 'Card' | 'UPI' | 'Online';
  total_amount: number;
  customer_id?: number;
  verified_at?: string;
  checker_id?: number;
  checker_name?: string;
  ticket_lines: TicketLine[];
  created_at: string;
  updated_at: string;
}

export interface TicketLine {
  id: number;
  ticket_id: number;
  item_name: string;
  qty: number;
  rate: number;
  levy: number;
  amount: number;
  vehicle_name?: string;
  vehicle_no?: string;
}

export interface CreateTicketRequest {
  branch_id: number;
  dest_branch_id: number;
  ferry_boat_id: number;
  ferry_time: string;
  payment_mode: string;
  items: CreateTicketItem[];
  total_amount: number;
}

export interface CreateTicketItem {
  item_name: string;
  qty: number;
  rate: number;
  levy: number;
  amount: number;
  vehicle_no?: string;
}
```

### 3.4: Branch Types

**File: `src/types/branch.ts`**

```typescript
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
  capacity_passengers: number;
  capacity_vehicles: number;
  is_active: boolean;
}

export interface FerrySchedule {
  id: number;
  hour: number;
  minute: number;
  schedule_time: string;
  branch_id: number;
  ferry_boat_id: number;
  is_active: boolean;
}

export interface ItemRate {
  id: number;
  item_name: string;
  item_rate: number;
  item_lavy: number;
  branch_id: number;
  is_vehicle: boolean;
  starting_date: string;
  ending_date?: string;
}
```

---

## 🔌 STEP 4: API CONFIGURATION

### 4.1: Axios Instance

**File: `src/lib/axios.ts`**

```typescript
import axios, { AxiosError } from 'axios';

const apiClient = axios.create({
  baseURL: import.meta.env.VITE_API_URL,
  timeout: 30000,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
  withCredentials: true,  // Important for Sanctum
});

// Request interceptor - add auth token
apiClient.interceptors.request.use(
  (config) => {
    const token = localStorage.getItem('auth_token');
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
  },
  (error) => {
    return Promise.reject(error);
  }
);

// Response interceptor - handle errors
apiClient.interceptors.response.use(
  (response) => response,
  (error: AxiosError) => {
    if (error.response?.status === 401) {
      // Unauthorized - clear token and redirect to login
      localStorage.removeItem('auth_token');
      window.location.href = '/login';
    }
    
    if (error.response?.status === 419) {
      // CSRF token mismatch
      console.error('CSRF token mismatch - session expired');
      localStorage.removeItem('auth_token');
      window.location.href = '/login';
    }

    return Promise.reject(error);
  }
);

export default apiClient;
```

### 4.2: API Configuration

**File: `src/config/api.ts`**

```typescript
export const API_BASE_URL = import.meta.env.VITE_API_URL || 'http://localhost';

export const API_ENDPOINTS = {
  // Auth
  LOGIN: '/login',
  LOGOUT: '/logout',
  
  // Tickets
  TICKETS: '/api/tickets',
  TICKET_ENTRY: '/ticket-entry',
  
  // Bookings
  BOOKINGS: '/api/bookings',
  
  // Branches
  BRANCHES: '/api/customer/branch',
  
  // Ferries
  FERRIES: '/api/customer/ferries/branch',
  
  // Rates
  RATES: '/api/customer/rates/branch',
  
  // Reports
  REPORTS_TICKETS: '/reports/tickets',
  REPORTS_VEHICLES: '/reports/vehicle-tickets',
  REPORTS_DAILY: '/reports/daily-summary',
  
  // Health
  HEALTH: '/health',
};
```

---

## 🏪 STEP 5: STATE MANAGEMENT

### 5.1: Auth Store (Zustand)

**File: `src/store/authStore.ts`**

```typescript
import { create } from 'zustand';
import { persist } from 'zustand/middleware';
import { User } from '@/types/auth';

interface AuthState {
  user: User | null;
  token: string | null;
  isAuthenticated: boolean;
  
  setAuth: (user: User, token?: string) => void;
  clearAuth: () => void;
}

export const useAuthStore = create<AuthState>()(
  persist(
    (set) => ({
      user: null,
      token: null,
      isAuthenticated: false,

      setAuth: (user, token) => {
        if (token) {
          localStorage.setItem('auth_token', token);
        }
        set({ user, token, isAuthenticated: true });
      },

      clearAuth: () => {
        localStorage.removeItem('auth_token');
        set({ user: null, token: null, isAuthenticated: false });
      },
    }),
    {
      name: 'auth-storage',
    }
  )
);
```

### 5.2: Ticket Form Store

**File: `src/store/ticketStore.ts`**

```typescript
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

  setBranch: (id) => set({ branchId: id }),
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
```

---

## 🎣 STEP 6: CUSTOM HOOKS (React Query)

### 6.1: Auth Hook

**File: `src/hooks/useAuth.ts`**

```typescript
import { useMutation } from '@tanstack/react-query';
import { useNavigate } from 'react-router-dom';
import { useAuthStore } from '@/store/authStore';
import { authService } from '@/services/authService';
import { LoginRequest } from '@/types/auth';
import { toast } from 'sonner';

export function useAuth() {
  const navigate = useNavigate();
  const { setAuth, clearAuth, user, isAuthenticated } = useAuthStore();

  const loginMutation = useMutation({
    mutationFn: (credentials: LoginRequest) => authService.login(credentials),
    onSuccess: (data) => {
      setAuth(data.user, data.token);
      toast.success('Login successful!');
      navigate('/dashboard');
    },
    onError: (error: any) => {
      toast.error(error.response?.data?.message || 'Login failed');
    },
  });

  const logoutMutation = useMutation({
    mutationFn: () => authService.logout(),
    onSuccess: () => {
      clearAuth();
      navigate('/login');
      toast.success('Logged out successfully');
    },
  });

  return {
    user,
    isAuthenticated,
    login: loginMutation.mutate,
    logout: logoutMutation.mutate,
    isLoggingIn: loginMutation.isPending,
    isLoggingOut: logoutMutation.isPending,
  };
}
```

### 6.2: Tickets Hook

**File: `src/hooks/useTickets.ts`**

```typescript
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import { ticketService } from '@/services/ticketService';
import { CreateTicketRequest } from '@/types/ticket';
import { toast } from 'sonner';

export function useTickets(filters?: any) {
  const queryClient = useQueryClient();

  // Fetch tickets
  const ticketsQuery = useQuery({
    queryKey: ['tickets', filters],
    queryFn: () => ticketService.getTickets(filters),
  });

  // Create ticket
  const createTicketMutation = useMutation({
    mutationFn: (data: CreateTicketRequest) => ticketService.createTicket(data),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['tickets'] });
      toast.success('Ticket created successfully!');
    },
    onError: (error: any) => {
      toast.error(error.response?.data?.message || 'Failed to create ticket');
    },
  });

  return {
    tickets: ticketsQuery.data,
    isLoading: ticketsQuery.isLoading,
    error: ticketsQuery.error,
    createTicket: createTicketMutation.mutate,
    isCreating: createTicketMutation.isPending,
  };
}

export function useTicketDetail(id: number) {
  return useQuery({
    queryKey: ['ticket', id],
    queryFn: () => ticketService.getTicket(id),
    enabled: !!id,
  });
}
```

### 6.3: Branches Hook

**File: `src/hooks/useBranches.ts`**

```typescript
import { useQuery } from '@tanstack/react-query';
import { branchService } from '@/services/branchService';

export function useBranches() {
  return useQuery({
    queryKey: ['branches'],
    queryFn: branchService.getBranches,
    staleTime: 5 * 60 * 1000, // 5 minutes
  });
}

export function useFerries(branchId?: number) {
  return useQuery({
    queryKey: ['ferries', branchId],
    queryFn: () => branchService.getFerries(branchId!),
    enabled: !!branchId,
  });
}

export function useRates(branchId?: number) {
  return useQuery({
    queryKey: ['rates', branchId],
    queryFn: () => branchService.getRates(branchId!),
    enabled: !!branchId,
  });
}
```

---

## 🎨 STEP 7: UI COMPONENTS

### 7.1: Base Button Component

**File: `src/components/ui/button.tsx`**

```typescript
import * as React from "react"
import { Slot } from "@radix-ui/react-slot"
import { cva, type VariantProps } from "class-variance-authority"
import { cn } from "@/lib/utils"

const buttonVariants = cva(
  "inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 [&_svg]:pointer-events-none [&_svg]:size-4 [&_svg]:shrink-0",
  {
    variants: {
      variant: {
        default:
          "bg-primary text-primary-foreground shadow hover:bg-primary/90",
        destructive:
          "bg-destructive text-destructive-foreground shadow-sm hover:bg-destructive/90",
        outline:
          "border border-input bg-background shadow-sm hover:bg-accent hover:text-accent-foreground",
        secondary:
          "bg-secondary text-secondary-foreground shadow-sm hover:bg-secondary/80",
        ghost: "hover:bg-accent hover:text-accent-foreground",
        link: "text-primary underline-offset-4 hover:underline",
      },
      size: {
        default: "h-9 px-4 py-2",
        sm: "h-8 rounded-md px-3 text-xs",
        lg: "h-10 rounded-md px-8",
        icon: "h-9 w-9",
      },
    },
    defaultVariants: {
      variant: "default",
      size: "default",
    },
  }
)

export interface ButtonProps
  extends React.ButtonHTMLAttributes<HTMLButtonElement>,
    VariantProps<typeof buttonVariants> {
  asChild?: boolean
}

const Button = React.forwardRef<HTMLButtonElement, ButtonProps>(
  ({ className, variant, size, asChild = false, ...props }, ref) => {
    const Comp = asChild ? Slot : "button"
    return (
      <Comp
        className={cn(buttonVariants({ variant, size, className }))}
        ref={ref}
        {...props}
      />
    )
  }
)
Button.displayName = "Button"

export { Button, buttonVariants }
```

### 7.2: Layout Component

**File: `src/components/layout/Layout.tsx`**

```typescript
import { Outlet } from 'react-router-dom';
import { Sidebar } from './Sidebar';
import { Header } from './Header';

export function Layout() {
  return (
    <div className="flex h-screen bg-gray-50">
      {/* Sidebar */}
      <Sidebar />

      {/* Main Content */}
      <div className="flex-1 flex flex-col overflow-hidden">
        {/* Header */}
        <Header />

        {/* Page Content */}
        <main className="flex-1 overflow-y-auto p-6">
          <Outlet />
        </main>
      </div>
    </div>
  );
}
```

### 7.3: Sidebar Component

**File: `src/components/layout/Sidebar.tsx`**

```typescript
import { Link, useLocation } from 'react-router-dom';
import { cn } from '@/lib/utils';
import { 
  LayoutDashboard, 
  Ticket, 
  Ship, 
  FileText, 
  Users, 
  Settings 
} from 'lucide-react';

const menuItems = [
  { icon: LayoutDashboard, label: 'Dashboard', path: '/dashboard' },
  { icon: Ticket, label: 'Ticket Entry', path: '/tickets/entry' },
  { icon: Ship, label: 'Bookings', path: '/bookings' },
  { icon: FileText, label: 'Reports', path: '/reports' },
  { icon: Users, label: 'Users', path: '/users' },
  { icon: Settings, label: 'Masters', path: '/masters' },
];

export function Sidebar() {
  const location = useLocation();

  return (
    <div className="w-64 bg-white border-r border-gray-200">
      {/* Logo */}
      <div className="h-16 flex items-center px-6 border-b border-gray-200">
        <h1 className="text-xl font-bold text-primary">Jetty Ferry</h1>
      </div>

      {/* Navigation */}
      <nav className="p-4 space-y-1">
        {menuItems.map((item) => {
          const Icon = item.icon;
          const isActive = location.pathname.startsWith(item.path);
          
          return (
            <Link
              key={item.path}
              to={item.path}
              className={cn(
                "flex items-center gap-3 px-4 py-3 rounded-lg transition-colors",
                isActive
                  ? "bg-primary text-white"
                  : "text-gray-700 hover:bg-gray-100"
              )}
            >
              <Icon className="w-5 h-5" />
              <span className="font-medium">{item.label}</span>
            </Link>
          );
        })}
      </nav>
    </div>
  );
}
```

---

## 📄 STEP 8: KEY PAGES

### 8.1: Login Page

**File: `src/pages/auth/Login.tsx`**

```typescript
import { useState } from 'react';
import { useAuth } from '@/hooks/useAuth';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Card } from '@/components/ui/card';

export function Login() {
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const { login, isLoggingIn } = useAuth();

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    login({ email, password });
  };

  return (
    <div className="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-500 to-purple-600">
      <Card className="w-full max-w-md p-8 space-y-6">
        <div className="text-center">
          <h1 className="text-3xl font-bold text-gray-900">Jetty Ferry</h1>
          <p className="text-gray-600 mt-2">Admin Panel Login</p>
        </div>

        <form onSubmit={handleSubmit} className="space-y-4">
          <div>
            <label className="block text-sm font-medium mb-2">Email</label>
            <Input
              type="email"
              value={email}
              onChange={(e) => setEmail(e.target.value)}
              placeholder="admin@jetty.com"
              required
            />
          </div>

          <div>
            <label className="block text-sm font-medium mb-2">Password</label>
            <Input
              type="password"
              value={password}
              onChange={(e) => setPassword(e.target.value)}
              placeholder="••••••••"
              required
            />
          </div>

          <Button 
            type="submit" 
            className="w-full" 
            disabled={isLoggingIn}
          >
            {isLoggingIn ? 'Logging in...' : 'Login'}
          </Button>
        </form>
      </Card>
    </div>
  );
}
```

### 8.2: Dashboard Page

**File: `src/pages/dashboard/Dashboard.tsx`**

```typescript
import { useQuery } from '@tanstack/react-query';
import { Card } from '@/components/ui/card';
import { Ticket, DollarSign, Users, Ship } from 'lucide-react';

export function Dashboard() {
  // Fetch dashboard stats
  const { data: stats } = useQuery({
    queryKey: ['dashboard-stats'],
    queryFn: async () => {
      // Fetch from API
      return {
        todayTickets: 247,
        todayRevenue: 52340,
        activeUsers: 45,
        activeFerries: 6,
      };
    },
  });

  return (
    <div className="space-y-6">
      <h1 className="text-3xl font-bold">Dashboard</h1>

      {/* Stats Grid */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <StatsCard
          icon={Ticket}
          title="Today's Tickets"
          value={stats?.todayTickets || 0}
          color="blue"
        />
        <StatsCard
          icon={DollarSign}
          title="Today's Revenue"
          value={`₹${stats?.todayRevenue.toLocaleString() || 0}`}
          color="green"
        />
        <StatsCard
          icon={Users}
          title="Active Users"
          value={stats?.activeUsers || 0}
          color="purple"
        />
        <StatsCard
          icon={Ship}
          title="Active Ferries"
          value={stats?.activeFerries || 0}
          color="orange"
        />
      </div>

      {/* Recent Tickets Table */}
      <Card className="p-6">
        <h2 className="text-xl font-bold mb-4">Recent Tickets</h2>
        {/* Add ticket table here */}
      </Card>
    </div>
  );
}

function StatsCard({ icon: Icon, title, value, color }: any) {
  return (
    <Card className="p-6">
      <div className="flex items-center justify-between">
        <div>
          <p className="text-sm text-gray-600">{title}</p>
          <p className="text-2xl font-bold mt-2">{value}</p>
        </div>
        <div className={`p-3 rounded-lg bg-${color}-100`}>
          <Icon className={`w-6 h-6 text-${color}-600`} />
        </div>
      </div>
    </Card>
  );
}
```

### 8.3: Ticket Entry Page (Most Important!)

**File: `src/pages/tickets/TicketEntry.tsx`**

```typescript
import { useState } from 'react';
import { useTicketStore } from '@/store/ticketStore';
import { useBranches, useFerries, useRates } from '@/hooks/useBranches';
import { useTickets } from '@/hooks/useTickets';
import { Button } from '@/components/ui/button';
import { Card } from '@/components/ui/card';
import { Select } from '@/components/ui/select';

export function TicketEntry() {
  const {
    branchId,
    destBranchId,
    ferryId,
    items,
    setBranch,
    setDestBranch,
    setFerry,
    addItem,
    removeItem,
    getTotalAmount,
    clearForm,
  } = useTicketStore();

  const { data: branches } = useBranches();
  const { data: ferries } = useFerries(branchId || undefined);
  const { data: rates } = useRates(branchId || undefined);
  const { createTicket, isCreating } = useTickets();

  const handleSubmit = () => {
    if (!branchId || !destBranchId || !ferryId || items.length === 0) {
      alert('Please fill all required fields');
      return;
    }

    createTicket({
      branch_id: branchId,
      dest_branch_id: destBranchId,
      ferry_boat_id: ferryId,
      ferry_time: '10:00',
      payment_mode: 'Cash',
      items: items,
      total_amount: getTotalAmount(),
    });

    clearForm();
  };

  return (
    <div className="space-y-6">
      <h1 className="text-3xl font-bold">Ticket Entry</h1>

      {/* Route Selection */}
      <Card className="p-6">
        <h2 className="text-xl font-bold mb-4">Journey Details</h2>
        
        <div className="grid grid-cols-1 md:grid-cols-4 gap-4">
          {/* From Branch */}
          <div>
            <label className="block text-sm font-medium mb-2">From</label>
            <Select
              value={branchId?.toString()}
              onValueChange={(value) => setBranch(Number(value))}
            >
              {branches?.map((branch) => (
                <option key={branch.id} value={branch.id}>
                  {branch.branch_name}
                </option>
              ))}
            </Select>
          </div>

          {/* To Branch */}
          <div>
            <label className="block text-sm font-medium mb-2">To</label>
            <Select
              value={destBranchId?.toString()}
              onValueChange={(value) => setDestBranch(Number(value))}
              disabled={!branchId}
            >
              {branches
                ?.filter((b) => b.id !== branchId)
                .map((branch) => (
                  <option key={branch.id} value={branch.id}>
                    {branch.branch_name}
                  </option>
                ))}
            </Select>
          </div>

          {/* Ferry */}
          <div>
            <label className="block text-sm font-medium mb-2">Ferry</label>
            <Select
              value={ferryId?.toString()}
              onValueChange={(value) => setFerry(Number(value))}
              disabled={!branchId}
            >
              {ferries?.map((ferry) => (
                <option key={ferry.id} value={ferry.id}>
                  {ferry.name}
                </option>
              ))}
            </Select>
          </div>

          {/* Time */}
          <div>
            <label className="block text-sm font-medium mb-2">Time</label>
            <Select>
              <option value="08:00">08:00 AM</option>
              <option value="10:00">10:00 AM</option>
              <option value="12:00">12:00 PM</option>
              <option value="14:00">02:00 PM</option>
              <option value="16:00">04:00 PM</option>
              <option value="18:00">06:00 PM</option>
            </Select>
          </div>
        </div>
      </Card>

      {/* Passengers & Vehicles */}
      <Card className="p-6">
        <h2 className="text-xl font-bold mb-4">Passengers & Vehicles</h2>
        
        {/* Items list */}
        <div className="space-y-2 mb-4">
          {items.map((item, index) => (
            <div key={index} className="flex items-center gap-4 p-4 bg-gray-50 rounded-lg">
              <span className="flex-1">{item.item_name}</span>
              <span>Qty: {item.qty}</span>
              <span className="font-bold">₹{item.amount}</span>
              <Button
                variant="destructive"
                size="sm"
                onClick={() => removeItem(index)}
              >
                Remove
              </Button>
            </div>
          ))}
        </div>

        {/* Add item button */}
        <Button
          onClick={() => {
            // Add logic to show add item dialog
          }}
        >
          + Add Passenger/Vehicle
        </Button>
      </Card>

      {/* Total & Submit */}
      <Card className="p-6">
        <div className="flex items-center justify-between">
          <div>
            <p className="text-sm text-gray-600">Total Amount</p>
            <p className="text-3xl font-bold">₹{getTotalAmount()}</p>
          </div>
          
          <Button
            size="lg"
            onClick={handleSubmit}
            disabled={isCreating || items.length === 0}
          >
            {isCreating ? 'Creating...' : 'Generate Ticket'}
          </Button>
        </div>
      </Card>
    </div>
  );
}
```

---

## 🚀 STEP 9: ROUTING

**File: `src/App.tsx`**

```typescript
import { BrowserRouter, Routes, Route, Navigate } from 'react-router-dom';
import { QueryClient, QueryClientProvider } from '@tanstack/react-query';
import { Toaster } from 'sonner';
import { Layout } from './components/layout/Layout';
import { Login } from './pages/auth/Login';
import { Dashboard } from './pages/dashboard/Dashboard';
import { TicketEntry } from './pages/tickets/TicketEntry';
import { useAuthStore } from './store/authStore';

const queryClient = new QueryClient({
  defaultOptions: {
    queries: {
      retry: 1,
      refetchOnWindowFocus: false,
    },
  },
});

function ProtectedRoute({ children }: { children: React.ReactNode }) {
  const { isAuthenticated } = useAuthStore();
  return isAuthenticated ? children : <Navigate to="/login" />;
}

function App() {
  return (
    <QueryClientProvider client={queryClient}>
      <BrowserRouter>
        <Routes>
          {/* Public Routes */}
          <Route path="/login" element={<Login />} />

          {/* Protected Routes */}
          <Route
            path="/"
            element={
              <ProtectedRoute>
                <Layout />
              </ProtectedRoute>
            }
          >
            <Route index element={<Navigate to="/dashboard" />} />
            <Route path="dashboard" element={<Dashboard />} />
            <Route path="tickets/entry" element={<TicketEntry />} />
            {/* Add more routes here */}
          </Route>
        </Routes>
      </BrowserRouter>
      
      <Toaster position="top-right" />
    </QueryClientProvider>
  );
}

export default App;
```

---

## 🏃 STEP 10: RUN THE PROJECT

### Development

```bash
# Start dev server
npm run dev

# Open browser at http://localhost:3000
```

### Production Build

```bash
# Build for production
npm run build

# Preview production build
npm run preview

# Deploy dist/ folder to your server
```

---

## 📊 SUCCESS CRITERIA

Your React admin is working when:

- ✅ Login page loads and works
- ✅ Dashboard shows stats
- ✅ Ticket entry form works
- ✅ Can create tickets successfully
- ✅ Tables display data properly
- ✅ Navigation works smoothly
- ✅ Fast page loads (< 1 second)
- ✅ Responsive on mobile

---

## 🎯 NEXT STEPS

After basic setup:

1. **Complete all pages** (reports, bookings, users)
2. **Add more components** (tables, charts, modals)
3. **Implement search/filters**
4. **Add print functionality**
5. **Connect to Laravel API** (update all services)
6. **Deploy to production**

---

**Give this to Claude Code and he'll build your modern React admin panel!** 🚀
