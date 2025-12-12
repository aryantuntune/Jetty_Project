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
            return response()->json(['message' => 'Email is already registered.'], 422);
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

        return response()->json(['message' => 'OTP sent successfully.']);
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

        return response()->json(['message' => 'Password reset OTP sent successfully.']);
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

        return response()->json(['message' => 'OTP verified successfully.']);
    }

    // Reset Password after OTP verified
    public function resetPassword(Request $request)
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

        return response()->json(['message' => 'Password reset successful.']);
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
                'message' => 'Invalid email or password'
            ], 401);
        }

        // Create Sanctum token
        $token = $customer->createToken('customer_api_token')->plainTextToken;

        return response()->json([
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
            'message' => 'Logged out successfully'
        ]);
    }

    /**
     * Customer Profile API
     */
    public function profile(Request $request)
    {
        return response()->json([
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
            'status' => true,
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
            'status' => true,
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
            'status' => true,
            'data'   => $items
        ], 200);
    }

    // ---------------------------------------------------------------

    public function getBooking()
    {
        return response()->json([
            'status' => true,
            'data'   => Booking::latest()->get()
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id'  => 'required|integer',
            'from_branch'  => 'required|integer',
            'to_branch'    => 'required|integer',
            'items'        => 'required',
            'total_amount'  => 'required|numeric',
            'payment_id'   => 'nullable|string',
            'status'       => 'nullable|string'
        ]);

        $booking = Booking::create([
            'customer_id'  => $validated['customer_id'],
            'from_branch'  => $validated['from_branch'],
            'to_branch'    => $validated['to_branch'],
            'items'        => json_encode($validated['items']),
            'total_amount'  => $validated['total_amount'],
            'payment_id'   => $validated['payment_id'] ?? null,
            'status'       => $validated['status'] ?? 'Pending',
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Booking created successfully',
            'data' => $booking
        ], 201);
    }

    public function getSingleBooking($id)
    {
        $booking = Booking::find($id);

        if (!$booking) {
            return response()->json([
                'status' => false,
                'message' => 'Booking not found'
            ], 404);
        }

        return response()->json([
            'status' => true,
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

    public function getSuccessfulBookings()
    {
        $bookings = Booking::where('status', 'Paid')->get();

        return response()->json([
            'status' => true,
            'data' => $bookings
        ]);
    }

    public function getCustomerBookings($customer_id)
    {
        // 1️⃣ Check if customer exists
        $customer = Customer::find($customer_id);

        if (!$customer) {
            return response()->json([
                'status' => false,
                'message' => 'Customer not found',
            ], 404);
        }

        // 2️⃣ Fetch successful bookings for this customer
        $bookings = Booking::where('customer_id', $customer_id)
            ->where('payment_status', 'success')
            ->get();

        return response()->json([
            'status' => true,
            'customer' => $customer,
            'bookings_count' => $bookings->count(),
            'data' => $bookings
        ]);
    }

    // -----------------------------------------------

    public function getToBranches($branchId)
    {
        // Find route_id of this "from" branch
        $routeId = DB::table('routes')
            ->where('branch_id', $branchId)
            ->value('route_id');

        if (!$routeId) {
            return response()->json([
                'status' => false,
                'message' => 'No route found for this branch',
                'data' => []
            ], 404);
        }

        // Get all connected branch IDs except from-branch
        $toBranchIds = DB::table('routes')
            ->where('route_id', $routeId)
            ->where('branch_id', '!=', $branchId)
            ->pluck('branch_id');

        // Get branch names
        $branches = Branch::whereIn('id', $toBranchIds)
            ->select('id', 'branch_name')
            ->get();

        return response()->json([
            'status' => true,
            'message' => 'To-branches fetched successfully',
            'data' => $branches
        ]);
    }
}
