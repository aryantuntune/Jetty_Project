<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Branch;
use App\Models\Customer;
use App\Models\FerryBoat;
use App\Models\ItemRate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Razorpay\Api\Api;
use Illuminate\Support\Facades\Storage;
use App\Mail\BookingConfirmationMail;
use Carbon\Carbon;


class ApiController extends Controller
{
    /**
     * Send OTP to the given email
     */


    public function generateOtp(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => 'required|email',
            'mobile'     => 'required|string|max:20',
            'password'   => 'required|string|min:6',
            'profile_image'  => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $email = $request->email;

        // Check if email already exists
        if (Customer::where('email', $email)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Email is already registered.'
            ], 422);
        }

        $profileImagePath = null;
        if ($request->hasFile('profile_image')) {
            // stores in storage/app/public/profile_images
            $profileImagePath = $request->file('profile_image')->store('profile_images', 'public');
        }

        // Generate 6 digit OTP
        $otp = random_int(100000, 999999);

        // Store OTP and user data temporarily in cache for 10 mins
        $cacheData = [
            'otp' => $otp,
            'user_data' => [
                'first_name' => $request->first_name,
                'last_name'  => $request->last_name,
                'email'      => $email,
                'mobile'     => $request->mobile,
                'password'   => $request->password,
                'profile_image' => $profileImagePath,
            ]
        ];
        Cache::put('signup_otp_for_' . $email, $cacheData, now()->addMinutes(10));

        // Send OTP to email (or mobile SMS, if you want)
        Mail::raw("Your signup OTP is: $otp", function ($message) use ($email) {
            $message->to($email)->subject('Signup OTP Verification');
        });

        return response()->json([
            'success' => true,
            'message' => 'OTP sent successfully.'
        ], 200);
    }


    /**
     * Register new customer after OTP verification
     */
    public function verifyOtpAndRegister(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:customers,email',
            'otp'   => 'required|digits:6',
        ]);

        $email = $request->email;
        $otp = $request->otp;

        $cacheData = Cache::get('signup_otp_for_' . $email);

        if (!$cacheData) {
            return response()->json(['message' => 'OTP expired or not found.'], 422);
        }

        if ($cacheData['otp'] != $otp) {
            return response()->json(['message' => 'Invalid OTP.'], 422);
        }

        // Create user
        $userData = $cacheData['user_data'];
        $customer = Customer::create([
            'first_name' => $userData['first_name'],
            'last_name'  => $userData['last_name'],
            'email'      => $userData['email'],
            'mobile'     => $userData['mobile'],
            'password'   => Hash::make($userData['password']),
            'profile_image' => $userData['profile_image'] ?? null,
        ]);

        // Delete cached data
        Cache::forget('signup_otp_for_' . $email);

        return response()->json([
            'success' => true,
            'message' => 'Signup successful.',
            'data'    => $customer
        ], 201);
    }
    // ---------------------------------------------------------------------------------

    // Send Password Reset OTP (forgot password request)
    public function sendPasswordResetOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:customers,email',
        ]);

        $email = $request->email;

        // Generate OTP
        $otp = random_int(100000, 999999);

        Cache::put('password_reset_otp_for_' . $email, $otp, now()->addMinutes(10));

        // Send OTP to email
        Mail::raw("Your password reset OTP is: $otp", function ($message) use ($email) {
            $message->to($email)->subject('Password Reset OTP Verification');
        });

        return response()->json(['success' => true, 'message' => 'Password reset OTP sent successfully.']);
    }

    // Verify Password Reset OTP
    public function verifyPasswordResetOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:customers,email',
            'otp'   => 'required|digits:6',
        ]);

        $email = $request->email;
        $otp = $request->otp;

        $cachedOtp = Cache::get('password_reset_otp_for_' . $email);

        if (!$cachedOtp) {
            return response()->json(['message' => 'OTP expired or not found.'], 422);
        }

        if ($cachedOtp != $otp) {
            return response()->json(['message' => 'Invalid OTP.'], 422);
        }

        return response()->json(['success' => true, 'message' => 'OTP verified successfully.']);
    }

    // Reset Password after OTP verified
    public function resetPassword_old(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:customers,email',
            'password' => 'required',
        ]);

        $email = $request->email;

        // Optional: check if OTP is verified by looking up cached OTP or some flag

        $customer = Customer::where('email', $email)->first();

        $customer->password = Hash::make($request->password);
        $customer->save();

        // Clear OTP cache
        Cache::forget('password_reset_otp_for_' . $email);

        return response()->json(['success' => true, 'message' => 'Password reset successful.']);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $customer = Customer::where('email', $request->email)->first();

        if (!$customer || !Hash::check($request->password, $customer->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid email or password'
            ], 401);
        }

        // Create Sanctum token
        $token = $customer->createToken('customer_api_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'token'   => $token,
            'data'    => $customer
        ]);
    }

    /**
     * Logout (Revoke Token)
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully'
        ]);
    }

    /**
     * Customer Profile API
     */
    public function profile(Request $request)
    {
        return response()->json([
            'success' => true,
            'message' => 'Profile fetched successfully',
            'data' => $request->user()
        ]);
    }

    // -----------------------booking api-----------------------------------------------------------


    //get branches
    public function branch_list()
    {
        $branches = Branch::select('id', 'branch_id', 'branch_name', 'user_id')->get();

        return response()->json([
            'success' => true,
            'data' => $branches
        ], 200);
    }

    //get ferryboat branch wise

    public function getByBranch($branchId)
    {
        $boats = FerryBoat::where('branch_id', $branchId)
            ->select('id', 'number', 'name', 'user_id', 'branch_id')
            ->get();

        return response()->json([
            'success' => true,
            'data'   => $boats
        ], 200);
    }

    public function getItemByBranch($branchId)
    {
        $items = ItemRate::where('branch_id', $branchId)
            ->with([

                'branch:id,branch_name',
            ])
            ->select(
                'id',
                'item_name',
                'item_category_id',
                'item_rate',
                'item_lavy',
                'branch_id',
                'starting_date',
                'ending_date',
                'user_id',
                'item_id',
                'route_id'
            )
            ->orderBy('item_name')
            ->get();

        return response()->json([
            'success' => true,
            'data'   => $items
        ], 200);
    }

    // ---------------------------------------------------------------

    public function getBooking()
    {
        return response()->json([
            'success' => true,
            'data'   => Booking::latest()->get()
        ]);
    }

    // public function store(Request $request)
    // {
    //     $validated = $request->validate([
    //         'customer_id'  => 'required|integer',
    //         'from_branch'  => 'required|integer',
    //         'to_branch'    => 'required|integer',
    //         'items'        => 'required',
    //         'total_amount'  => 'required|numeric',
    //         'payment_id'   => 'nullable|string',
    //         'status'       => 'nullable|string'
    //     ]);

    //     $booking = Booking::create([
    //         'customer_id'  => $validated['customer_id'],
    //         'from_branch'  => $validated['from_branch'],
    //         'to_branch'    => $validated['to_branch'],
    //         'items'        => json_encode($validated['items']),
    //         'total_amount'  => $validated['total_amount'],
    //         'payment_id'   => $validated['payment_id'] ?? null,
    //         'status'       => $validated['status'] ?? 'Pending',
    //     ]);

    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Booking created successfully',
    //         'data' => $booking
    //     ], 201);
    // }

    public function getSingleBooking($id)
    {
        $booking = Booking::find($id);

        if (!$booking) {
            return response()->json([
                'success' => true,
                'message' => 'Booking not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'   => $booking
        ]);
    }

    public function createMobileOrder(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric',
        ]);

        $api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));

        $order = $api->order->create([
            'receipt'  => 'ORD_' . time(),
            'amount'   => $request->amount * 100, // important: convert to paise
            'currency' => 'INR',
        ]);

        return response()->json([
            'success'   => true,
            'order_id'  => $order['id'],
            'amount'    => $order['amount'],
            'currency'  => 'INR',
            'key'       => env('RAZORPAY_KEY'), // mobile needs this for checkout
        ]);
    }

    public function verifyMobilePayment(Request $request)
    {
        $request->validate([
            'razorpay_order_id'   => 'required|string',
            'razorpay_payment_id' => 'required|string',
            'razorpay_signature'  => 'required|string',
            'customer_id'         => 'required|exists:customers,id',
            'from_branch'         => 'required|integer',
            'to_branch'           => 'required|integer',
            'items'               => 'required|array',      // MUST be array
            'total_amount'        => 'required|numeric',
        ]);

        try {
            $api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));

            // Verify payment signature
            $api->utility->verifyPaymentSignature([
                'razorpay_order_id'   => $request->razorpay_order_id,
                'razorpay_payment_id' => $request->razorpay_payment_id,
                'razorpay_signature'  => $request->razorpay_signature
            ]);

            // ⭐ Process items (ensure each item has vehicle_no if required) 
            $items = collect($request->items)->map(function ($item) use ($request) {
                return [
                    'item_name'  => $item['item_name'] ?? null,
                    'quantity'   => $item['quantity'] ?? null,
                    'rate'       => $item['rate'] ?? null,
                    'vehicle_no' => $item['vehicle_no'] ?? $request->vehical_no ?? null,
                ];
            });

            // ⭐ Save booking
            $booking = Booking::create([
                'customer_id'   => $request->customer_id,
                'from_branch'   => $request->from_branch,
                'to_branch'     => $request->to_branch,
                'items'         => json_encode($items),
                'total_amount'  => $request->total_amount,
                'payment_id'    => $request->razorpay_payment_id,

                'status' => 'paid'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Payment verified & booking created successfully',
                'booking' => $booking
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Payment verification failed',
                'error'   => $e->getMessage()  // Optional: remove in production
            ], 400);
        }
    }

    // public function getSuccessfulBookings()
    // {
    //     $bookings = Booking::where('status', 'Paid')->get();

    //     return response()->json([
    //         'success' => true,
    //         'data' => $bookings
    //     ]);
    // }

    public function getCustomerBookings($customer_id)
    {
        // 1️⃣ Check if customer exists
        $customer = Customer::find($customer_id);

        if (!$customer) {
            return response()->json([
                'success' => false,
                'message' => 'Customer not found',
            ], 404);
        }

        // 2️⃣ Fetch successful bookings for this customer
        $bookings = Booking::where('customer_id', $customer_id)
            ->where('payment_status', 'success')
            ->get();

        return response()->json([
            'success' => true,
            'customer' => $customer,
            'bookings_count' => $bookings->count(),
            'data' => $bookings
        ]);
    }

    // -----------------------------------------------

    // public function getToBranches($branchId)
    // {
    //     // Find route_id of this "from" branch
    //     $routeId = DB::table('routes')
    //         ->where('branch_id', $branchId)
    //         ->value('route_id');

    //     if (!$routeId) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'No route found for this branch',
    //             'data' => []
    //         ], 404);
    //     }

    //     // Get all connected branch IDs except from-branch
    //     $toBranchIds = DB::table('routes')
    //         ->where('route_id', $routeId)
    //         ->where('branch_id', '!=', $branchId)
    //         ->pluck('branch_id');

    //     // Get branch names
    //     $branches = Branch::whereIn('id', $toBranchIds)
    //         ->select('id', 'branch_name')
    //         ->get();

    //     return response()->json([
    //         'success' => true,
    //         'message' => 'To-branches fetched successfully',
    //         'data' => $branches
    //     ]);
    // }

    public function updateProfileImage(Request $request)
    {
        $request->validate([
            'profile_image' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $customer = $request->user(); // logged-in customer via Sanctum

        // Delete old image if exists
        if ($customer->profile_image && Storage::disk('public')->exists($customer->profile_image)) {
            Storage::disk('public')->delete($customer->profile_image);
        }

        // Store new image
        $path = $request->file('profile_image')
            ->store('profile_images', 'public');

        // Update customer record
        $customer->profile_image = $path;
        $customer->save();

        return response()->json([
            'success' => true,
            'message' => 'Profile image updated successfully',
            'data' => [
                'customer_id'  => $customer->id,
                'profile_image' => asset('storage/' . $path),
            ]
        ], 200);
    }













    // ------------------new api from booking controller------------------------------------------------------------

    public function index(Request $request)
    {
        $customerId = $request->user()->id;

        $bookings = Booking::where('customer_id', $customerId)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($booking) {
                return [
                    'id' => $booking->id,
                    'customer_id' => $booking->customer_id,
                    'ferry_id' => $booking->ferry_id,
                    'from_branch_id' => $booking->from_branch,
                    'to_branch_id' => $booking->to_branch,
                    'booking_date' => $booking->booking_date,
                    'departure_time' => $booking->departure_time,
                    'total_amount' => floatval($booking->total_amount),
                    'status' => $booking->status,
                    'qr_code' => $booking->qr_code,
                    'created_at' => $booking->created_at->toIso8601String(),
                ];
            });

        return response()->json([
            'success' => true,
            'message' => 'Bookings retrieved successfully',
            'data' => $bookings
        ]);
    }

    /**
     * Show customer dashboard with booking form (WEB)
     */
    // public function showDashboard()
    // {
    //     $branches = Branch::orderBy('branch_name')->get();
    //     return view('customer.dashboard', compact('branches'));
    // }

    /**
     * Show booking details by ID (API)
     */
    public function show(Request $request, $id)
    {
        $customerId = $request->user()->id;

        $booking = Booking::where('customer_id', $customerId)
            ->where('id', $id)
            ->first();

        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'Booking not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Booking details retrieved successfully',
            'data' => $booking
        ]);
    }

    /**
     * Create new booking (API)
     */

    private function generateTicketNo()
    {
        $year   = Carbon::now()->format('Y');
        $date   = Carbon::now()->format('md'); // MMDD
        $random = random_int(100000, 999999);

        return "{$year}{$date}{$random}";
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'ferry_id' => 'required|integer',
            'from_branch_id' => 'required|integer',
            'to_branch_id' => 'required|integer',
            'booking_date' => 'required|date',
            'departure_time' => 'required',
            'items' => 'required|array',
        ]);

        // Calculate total amount from items
        $totalAmount = 0;
        foreach ($validated['items'] as $item) {
            $itemRate = \App\Models\ItemRate::find($item['item_rate_id']);
            if ($itemRate) {
                $totalAmount += ($itemRate->item_rate + $itemRate->item_lavy) * $item['quantity'];
            }
        }

        // Generate QR code
        $qrCode = 'JETTY-' . strtoupper(uniqid());

        $ticketNo = null;

        do {
            $ticketNo = $this->generateTicketNo();
        } while (Booking::where('ticket_id', $ticketNo)->exists());


        $booking = Booking::create([
            'customer_id' => $request->user()->id,
            'ferry_id' => $validated['ferry_id'],
            'from_branch' => $validated['from_branch_id'],
            'to_branch' => $validated['to_branch_id'],
            'booking_date' => $validated['booking_date'],
            'departure_time' => $validated['departure_time'],
            'items' => json_encode($validated['items']),
            'total_amount' => $totalAmount,
            'qr_code' => $qrCode,
            'status' => 'confirmed',
            'booking_source' => 'mobile_app',
            'ticket_id'      => $ticketNo,
        ]);


        $booking->load(['customer', 'fromBranch', 'toBranch']);

        //  SEND BOOKING CONFIRMATION EMAIL
        Mail::to($booking->customer->email)
            ->send(new BookingConfirmationMail($booking));

        // Reload to get proper formatting
        $booking->refresh();

        return response()->json([
            'success' => true,
            'message' => 'Booking created successfully',
            'data' => [
                'id' => $booking->id,
                'customer_id' => $booking->customer_id,
                'ticket_id'       => $booking->ticket_id,
                'ferry_id' => $booking->ferry_id,
                'from_branch_id' => $booking->from_branch,
                'to_branch_id' => $booking->to_branch,
                'booking_date' => $booking->booking_date,
                'departure_time' => $booking->departure_time,
                'total_amount' => floatval($booking->total_amount),
                'status' => $booking->status,
                'qr_code' => $booking->qr_code,
                'created_at' => $booking->created_at->toIso8601String(),
            ]
        ], 201);
    }

    /**
     * Cancel booking (API)
     */
    public function cancel(Request $request, $id)
    {
        $customerId = $request->user()->id;

        $booking = Booking::where('customer_id', $customerId)
            ->where('id', $id)
            ->first();

        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'Booking not found'
            ], 404);
        }

        if ($booking->status === 'cancelled') {
            return response()->json([
                'success' => false,
                'message' => 'Booking already cancelled'
            ], 400);
        }

        $booking->update(['status' => 'cancelled']);

        return response()->json([
            'success' => true,
            'message' => 'Booking cancelled successfully',
            'data' => $booking
        ]);
    }

    /**
     * Get to-branches for a from-branch (API)
     */
    public function getToBranches($branchId)
    {
        $routeId = DB::table('routes')->where('branch_id', $branchId)->value('route_id');

        if (!$routeId) {
            return response()->json([
                'success' => false,
                'message' => 'No routes found for this branch',
                'data' => []
            ]);
        }

        $toBranchIds = DB::table('routes')
            ->where('route_id', $routeId)
            ->where('branch_id', '!=', $branchId)
            ->pluck('branch_id');

        $branches = Branch::whereIn('id', $toBranchIds)
            ->select('id', 'branch_name as name')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'To branches retrieved successfully',
            'data' => $branches
        ]);
    }

    /**
     * Get successful bookings (API)
     */
    public function getSuccessfulBookings(Request $request)
    {
        $customerId = $request->user()->id;

        $bookings = Booking::where('customer_id', $customerId)
            ->where('status', 'confirmed')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($booking) {
                return [
                    'id' => $booking->id,
                    'from_branch_id' => $booking->from_branch,
                    'to_branch_id' => $booking->to_branch,
                    'total_amount' => $booking->total_amount,
                    'status' => $booking->status,
                    'created_at' => $booking->created_at
                ];
            });

        return response()->json([
            'success' => true,
            'message' => 'Successful bookings retrieved successfully',
            'data' => $bookings
        ]);
    }

    /**
     * Get items (rates) for a branch (WEB)
     */
    // public function getItems($branchId)
    // {
    //     $items = \App\Models\ItemRate::where('branch_id', $branchId)
    //         ->where(function ($q) {
    //             $q->whereNull('ending_date')
    //                 ->orWhere('ending_date', '>=', now());
    //         })
    //         ->select('id', 'item_name', 'item_rate', 'item_lavy')
    //         ->get();

    //     return response()->json($items);
    // }

    /**
     * Get single item rate details (WEB)
     */
    // public function getItemRate($itemRateId)
    // {
    //     $item = \App\Models\ItemRate::find($itemRateId);

    //     if (!$item) {
    //         return response()->json(['error' => 'Item not found'], 404);
    //     }

    //     return response()->json([
    //         'item_rate' => $item->item_rate,
    //         'item_lavy' => $item->item_lavy
    //     ]);
    // }

    /**
     * Submit booking form (WEB) - currently unused, handled by Razorpay
     */
    public function submit(Request $request)
    {
        return redirect()->route('booking.form')
            ->with('success', 'Booking submitted successfully');
    }

    /**
     * Create Razorpay order (WEB)
     */
    public function createOrder(Request $request)
    {
        $validated = $request->validate([
            'grand_total' => 'required|numeric',
            'from_branch' => 'required|integer',
            'to_branch' => 'required|integer',
            'items' => 'required|array'
        ]);

        // Store in session for later use after payment
        session([
            'booking_data' => [
                'from_branch' => $validated['from_branch'],
                'to_branch' => $validated['to_branch'],
                'items' => $validated['items'],
                'grand_total' => $validated['grand_total']
            ]
        ]);

        // Create Razorpay order
        $api = new \Razorpay\Api\Api(config('services.razorpay.key'), config('services.razorpay.secret'));

        $orderData = [
            'receipt'         => 'FERRY-' . time(),
            'amount'          => $validated['grand_total'] * 100, // Convert to paise
            'currency'        => 'INR',
            'payment_capture' => 1
        ];

        $razorpayOrder = $api->order->create($orderData);

        return response()->json([
            'order_id' => $razorpayOrder['id'],
            'amount' => $razorpayOrder['amount'],
            'key' => config('services.razorpay.key')
        ]);
    }

    /**
     * Verify Razorpay payment (WEB)
     */
    // public function verifyPayment(Request $request)
    // {
    //     // Razorpay signature verification
    //     $api = new \Razorpay\Api\Api(config('services.razorpay.key'), config('services.razorpay.secret'));

    //     try {
    //         $attributes = [
    //             'razorpay_order_id' => $request->razorpay_order_id,
    //             'razorpay_payment_id' => $request->razorpay_payment_id,
    //             'razorpay_signature' => $request->razorpay_signature
    //         ];

    //         $api->utility->verifyPaymentSignature($attributes);

    //         // Payment verified - create booking
    //         $bookingData = session('booking_data');

    //         // TODO: Create booking record here

    //         session()->forget('booking_data');

    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Payment verified successfully'
    //         ]);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Payment verification failed: ' . $e->getMessage()
    //         ], 400);
    //     }
    // }



    // ---------------------------customer login controller----------------------------------------------


    public function customerlogin(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Find customer by email
        $customer = \App\Models\Customer::where('email', $request->email)->first();

        // Check if customer exists and password matches
        if (!$customer || !Hash::check($request->password, $customer->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid email or password'
            ], 401);
        }

        // Create authentication token
        $token = $customer->createToken('mobile-app')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'data' => [
                'token' => $token,
                'customer' => $customer
            ]
        ]);
    }

    public function customerlogout(Request $request)
    {
        // Revoke current token for API
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully'
        ]);
    }

    /**
     * Google Sign-In for API
     * Accepts Google ID token and creates/logs in the user
     */
    public function customergoogleSignIn(Request $request)
    {
        $request->validate([
            'id_token' => 'required|string',
            'email' => 'required|email',
            'first_name' => 'required|string',
            'last_name' => 'nullable|string',
        ]);

        // Check if customer exists
        $customer = Customer::where('email', $request->email)->first();

        if (!$customer) {
            // Create new customer from Google account
            $customer = Customer::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name ?? '',
                'email' => $request->email,
                'mobile' => '', // User can add later in profile
                'password' => Hash::make(uniqid()), // Random password for Google users
            ]);
        }

        // Create authentication token
        $token = $customer->createToken('mobile-app')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Google Sign-In successful',
            'data' => [
                'token' => $token,
                'customer' => $customer
            ]
        ]);
    }

    // --------------------customers register api  --------------------------------------------------

    // STEP 1: SEND OTP
    public function sendOtp(Request $request)
    {
        $request->validate([
            'first_name' => 'required',
            'last_name'  => 'required',
            'mobile'     => 'required',
            'email'      => 'required|email',
            'password'   => 'required|min:6',
        ]);

        // --------------------------------------------
        // CHECK IF EMAIL EXISTS IN CUSTOMERS TABLE
        // --------------------------------------------
        if (\App\Models\Customer::where('email', $request->email)->exists()) {
            return response()->json([
                'success' => false,
                'exists' => true,
                'message' => "Email already exists. Please login or reset your password."
            ], 409);
        }

        // --------------------------------------------
        // GENERATE OTP
        // --------------------------------------------
        $otp = rand(100000, 999999);

        // Store OTP data in cache (15 minutes expiry) instead of session for API support
        $cacheKey = 'pending_registration_' . $request->email;
        Cache::put($cacheKey, [
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
            'mobile'     => $request->mobile,
            'email'      => $request->email,
            'password'   => $request->password,
            'otp'        => $otp
        ], now()->addMinutes(15));

        // --------------------------------------------
        // SEND OTP EMAIL
        // --------------------------------------------
        Mail::raw("Your OTP is: $otp", function ($message) use ($request) {
            $message->to($request->email)
                ->subject('Your Email OTP Verification');
        });

        return response()->json([
            'success' => true,
            'message' => "OTP sent successfully!"
        ]);
    }


    // STEP 2: VERIFY OTP
    public function verifyOtpLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required',
        ]);

        // Retrieve registration data from cache
        $cacheKey = 'pending_registration_' . $request->email;
        $data = Cache::get($cacheKey);

        if (!$data) {
            return response()->json([
                'success' => false,
                'message' => 'OTP expired or invalid. Please request a new one.'
            ], 400);
        }

        // Verify OTP
        if ($request->otp != $data['otp']) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid OTP'
            ], 400);
        }

        // OTP Correct → Create Customer
        $customer = Customer::create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'mobile' => $data['mobile'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        // Create authentication token
        $token = $customer->createToken('mobile-app')->plainTextToken;

        // Clear cache data
        Cache::forget($cacheKey);

        return response()->json([
            'success' => true,
            'message' => 'Registration successful',
            'data' => [
                'token' => $token,
                'customer' => $customer
            ]
        ]);
    }
    // ----------------------------forgotpassword controller code-----------------------------------------------

    // API METHOD: Request Password Reset OTP
    public function requestOTP(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        // Check if customer exists
        $customer = Customer::where('email', $request->email)->first();

        if (!$customer) {
            return response()->json([
                'success' => false,
                'message' => 'No account found with this email address.'
            ], 404);
        }

        // Generate OTP
        $otp = rand(100000, 999999);

        // Store OTP in cache (15 minutes expiry)
        $cacheKey = 'password_reset_' . $request->email;
        Cache::put($cacheKey, [
            'email' => $request->email,
            'otp' => $otp
        ], now()->addMinutes(15));

        // Send OTP email
        Mail::raw("Your password reset OTP is: $otp\n\nThis OTP will expire in 15 minutes.", function ($message) use ($request) {
            $message->to($request->email)
                ->subject('Password Reset OTP');
        });

        return response()->json([
            'success' => true,
            'message' => 'OTP sent to your email successfully!'
        ]);
    }

    // API METHOD: Verify Password Reset OTP
    public function verifyOTP(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required',
        ]);

        // Retrieve OTP from cache
        $cacheKey = 'password_reset_' . $request->email;
        $data = Cache::get($cacheKey);

        if (!$data) {
            return response()->json([
                'success' => false,
                'message' => 'OTP expired or invalid. Please request a new one.'
            ], 400);
        }

        // Verify OTP
        if ($request->otp != $data['otp']) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid OTP'
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => 'OTP verified successfully'
        ]);
    }

    // API METHOD: Reset Password
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required',
            'password' => 'required|min:6',
        ]);

        // Verify OTP one more time
        $cacheKey = 'password_reset_' . $request->email;
        $data = Cache::get($cacheKey);

        if (!$data || $request->otp != $data['otp']) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired OTP'
            ], 400);
        }

        // Find customer
        $customer = Customer::where('email', $request->email)->first();

        if (!$customer) {
            return response()->json([
                'success' => false,
                'message' => 'Customer not found'
            ], 404);
        }

        // Update password
        $customer->password = Hash::make($request->password);
        $customer->save();

        // Clear OTP from cache
        Cache::forget($cacheKey);

        return response()->json([
            'success' => true,
            'message' => 'Password reset successfully!'
        ]);
    }
}
