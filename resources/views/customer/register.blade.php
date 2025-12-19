{{--
================================================================================
OLD DESIGN - COMMENTED OUT (Original Tailwind/Bootstrap Mix Version)
================================================================================
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <style>
        /* ===== VIDEO BACKGROUND ===== */
        .bg-video {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            z-index: -1;
            filter: brightness(65%);
        }

        /* ===== GLASS CARD ===== */
        .glass-card {
            width: 420px;
            border-radius: 20px;
            padding: 40px 35px;
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255,255,255,0.2);
            box-shadow: 0 15px 35px rgba(0,0,0,0.3);
            animation: fadeIn 0.8s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* Input Focus */
        .form-input {
            border-radius: 12px;
        }

        .form-input:focus {
            outline: none;
            border-color: #00b3ff;
            box-shadow: 0 0 0 4px rgba(0,179,255,0.25);
        }

        /* ===== BUTTON L-R ANIMATION ===== */
        .btn-anim {
            padding: 12px;
            border-radius: 30px;
            font-size: 17px;
            color: white;
            font-weight: 700;
            background: linear-gradient(90deg, #0077ff, #00d4ff, #0077ff);
            background-size: 300% 100%;
            transition: 0.5s;
        }
        .btn-anim:hover {
            background-position: 100% 0;
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0,212,255,0.5);
        }

        /* ===== MODAL GLASS STYLE ===== */
        .modal-box {
            background: rgba(255, 255, 255, 0.20);
            border-radius: 20px;
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255,255,255,0.35);
            box-shadow: 0 18px 40px rgba(0,0,0,0.3);
            animation: fadeIn 0.4s ease;
        }
    </style>

</head>

<body class="flex items-center justify-center min-h-screen">

<!-- Sea Video Background -->
<video autoplay loop muted playsinline class="bg-video">
    <source src="{{ asset('videos/1.mp4') }}" type="video/mp4">
</video>

<!-- Main Glass Card -->
<div class="glass-card">

    <h2 class="text-3xl font-bold text-white text-center mb-2 drop-shadow-lg">
        Create Account
    </h2>

    <form id="registerForm">
        @csrf

        <input name="first_name" class="form-input w-full mb-3 p-3 border" placeholder="First Name">
        <input name="last_name" class="form-input w-full mb-3 p-3 border" placeholder="Last Name">
        <input name="mobile" class="form-input w-full mb-3 p-3 border" placeholder="Mobile">
        <input name="email" class="form-input w-full mb-3 p-3 border" placeholder="Email">
        <input type="password" name="password" class="form-input w-full mb-3 p-3 border" placeholder="Password">

        <button type="button" onclick="sendOtp()" class="btn-anim w-full mt-3">
            Generate OTP
        </button>
    </form>

</div>


<!-- ================= OTP MODAL ================= -->
<div id="otpModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
    <div class="modal-box w-full max-w-md p-8 relative">

        <button onclick="closeOtpModal()" class="absolute top-3 right-4 text-white text-2xl">✕</button>

        <h3 class="text-2xl font-bold text-white text-center mb-3">Verify OTP</h3>

        <p class="text-gray-200 text-center mb-4">
            Enter the 6-digit OTP sent to your email.
        </p>

        <input id="otpInput"
               class="form-input w-full p-3 border text-center text-2xl tracking-widest bg-white/70 rounded-xl"
               maxlength="6"
               placeholder="------">

        <button onclick="verifyOtp()" class="btn-anim w-full mt-6">
            Verify OTP
        </button>
    </div>
</div>


<!-- ================= ERROR MODAL ================= -->
<div id="errorModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
    <div class="modal-box w-full max-w-md p-8 relative">

        <button onclick="closeErrorModal()" class="absolute top-3 right-4 text-white text-2xl">✕</button>

        <h3 class="text-2xl font-bold text-red-400 text-center mb-2">Error</h3>

        <p id="errorMessage" class="text-white text-lg text-center mb-4"></p>

        <div id="errorLinks" class="text-center hidden">
            <a href="{{ route('customer.login') }}" class="font-bold text-blue-300 block mb-2">
                ➤ Login
            </a>
            <a href="{{ route('customer.password.request') }}" class="font-bold text-blue-300">
                ➤ Forgot Password?
            </a>
        </div>

        <button onclick="closeErrorModal()" class="btn-anim w-full mt-4 bg-red-500">
            Close
        </button>
    </div>
</div>


<script>

/* ================= SEND OTP ================= */
function sendOtp() {
    let formData = new FormData(document.getElementById('registerForm'));

    axios.post("{{ route('customer.register.sendOtp') }}", formData)
    .then(res => {
        document.getElementById('otpModal').classList.remove('hidden');
    })
    .catch(err => {
        if (err.response?.data?.message) {
            showErrorModal(err.response.data.message);
        } else {
            showErrorModal("Validation error. Please check your input.");
        }
    });
}

/* ================= VERIFY OTP ================= */
function verifyOtp() {
    let otp = document.getElementById('otpInput').value;

    axios.post("{{ route('customer.register.verifyOtp') }}", { otp })
    .then(res => {
        if (!res.data.success) {
            showErrorModal(res.data.message);
            return;
        }
        window.location.href = "{{ route('customer.login') }}";
    })
    .catch(() => {
        showErrorModal("Something went wrong. Try again.");
    });
}

/* ================= MODAL FUNCTIONS ================= */
function showErrorModal(message) {
    document.getElementById("errorMessage").innerText = message;
    document.getElementById("errorModal").classList.remove("hidden");

    if (message.includes("Email already exists")) {
        document.getElementById("errorLinks").classList.remove("hidden");
    } else {
        document.getElementById("errorLinks").classList.add("hidden");
    }
}

function closeOtpModal() {
    document.getElementById("otpModal").classList.add("hidden");
}
function closeErrorModal() {
    document.getElementById("errorModal").classList.add("hidden");
}

</script>

</body>
</html>
================================================================================
END OF OLD DESIGN
================================================================================
--}}

{{-- NEW DESIGN - Modern TailwindCSS Version --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Register - Jetty Ferry Booking</title>

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Axios -->
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'system-ui', 'sans-serif'],
                    },
                    colors: {
                        primary: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            200: '#bfdbfe',
                            300: '#93c5fd',
                            400: '#60a5fa',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                            800: '#1e40af',
                            900: '#1e3a8a',
                        },
                        accent: {
                            gold: '#fbbf24',
                            orange: '#f97316',
                        }
                    },
                    animation: {
                        'fade-in-up': 'fadeInUp 0.6s ease-out forwards',
                        'fade-in': 'fadeIn 0.8s ease-out forwards',
                        'float': 'float 6s ease-in-out infinite',
                        'shake': 'shake 0.5s ease-in-out',
                        'scale-up': 'scaleUp 0.3s ease-out forwards',
                    },
                    keyframes: {
                        fadeInUp: {
                            '0%': { opacity: '0', transform: 'translateY(20px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' },
                        },
                        fadeIn: {
                            '0%': { opacity: '0' },
                            '100%': { opacity: '1' },
                        },
                        float: {
                            '0%, 100%': { transform: 'translateY(0px)' },
                            '50%': { transform: 'translateY(-10px)' },
                        },
                        shake: {
                            '0%, 100%': { transform: 'translateX(0)' },
                            '25%': { transform: 'translateX(-5px)' },
                            '75%': { transform: 'translateX(5px)' },
                        },
                        scaleUp: {
                            '0%': { opacity: '0', transform: 'scale(0.9)' },
                            '100%': { opacity: '1', transform: 'scale(1)' },
                        }
                    }
                }
            }
        }
    </script>

    <style>
        .video-bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: -1;
        }

        .video-bg video {
            width: 100%;
            height: 100%;
            object-fit: cover;
            filter: brightness(0.5);
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border: 1px solid rgba(255, 255, 255, 0.15);
        }

        .input-glass {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
        }

        .input-glass:focus {
            background: rgba(255, 255, 255, 0.15);
            border-color: rgba(59, 130, 246, 0.5);
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.15);
        }

        .input-glass::placeholder {
            color: rgba(255, 255, 255, 0.5);
        }

        .input-glass.error {
            border-color: rgba(239, 68, 68, 0.5);
            animation: shake 0.5s ease-in-out;
        }

        .btn-gradient {
            background: linear-gradient(135deg, #fbbf24 0%, #f97316 100%);
            transition: all 0.3s ease;
        }

        .btn-gradient:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 15px 30px rgba(251, 191, 36, 0.35);
        }

        .btn-gradient:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        .modal-overlay {
            background: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(4px);
        }

        .otp-input {
            letter-spacing: 1em;
            text-indent: 0.5em;
        }

        .delay-100 { animation-delay: 0.1s; }
        .delay-200 { animation-delay: 0.2s; }
        .delay-300 { animation-delay: 0.3s; }
        .delay-400 { animation-delay: 0.4s; }

        .spinner {
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-top-color: #1f2937;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>
</head>

<body class="font-sans antialiased text-white min-h-screen">

<!-- Video Background -->
<div class="video-bg">
    <video autoplay muted loop playsinline>
        <source src="{{ asset('videos/1.mp4') }}" type="video/mp4">
    </video>
</div>

<!-- Main Container -->
<div class="min-h-screen flex items-center justify-center px-4 py-12">
    <div class="w-full max-w-md">

        <!-- Logo & Brand -->
        <div class="text-center mb-8 opacity-0 animate-fade-in-up">
            <a href="{{ url('/') }}" class="inline-flex items-center space-x-3 group">
                <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-primary-400 to-primary-600 flex items-center justify-center shadow-xl group-hover:shadow-primary-500/50 transition-all duration-300 animate-float">
                    <i data-lucide="ship" class="w-7 h-7 text-white"></i>
                </div>
                <span class="text-3xl font-bold tracking-tight">Jetty</span>
            </a>
        </div>

        <!-- Register Card -->
        <div class="glass-card rounded-3xl p-8 md:p-10 shadow-2xl opacity-0 animate-fade-in-up delay-100">

            <!-- Header -->
            <div class="text-center mb-8">
                <h1 class="text-2xl md:text-3xl font-bold mb-2">Create Account</h1>
                <p class="text-white/60">Join us for seamless ferry booking</p>
            </div>

            <!-- Registration Form -->
            <form id="registerForm" class="space-y-4">
                @csrf

                <!-- Name Row -->
                <div class="grid grid-cols-2 gap-4 opacity-0 animate-fade-in-up delay-200">
                    <!-- First Name -->
                    <div>
                        <label class="block text-sm font-medium text-white/80 mb-2">First Name</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i data-lucide="user" class="w-4 h-4 text-white/40"></i>
                            </div>
                            <input
                                type="text"
                                name="first_name"
                                class="input-glass w-full pl-10 pr-3 py-3 rounded-xl text-white placeholder-white/50 focus:outline-none text-sm"
                                placeholder="First"
                                required
                            >
                        </div>
                    </div>

                    <!-- Last Name -->
                    <div>
                        <label class="block text-sm font-medium text-white/80 mb-2">Last Name</label>
                        <input
                            type="text"
                            name="last_name"
                            class="input-glass w-full px-3 py-3 rounded-xl text-white placeholder-white/50 focus:outline-none text-sm"
                            placeholder="Last"
                            required
                        >
                    </div>
                </div>

                <!-- Mobile -->
                <div class="opacity-0 animate-fade-in-up delay-200">
                    <label class="block text-sm font-medium text-white/80 mb-2">Mobile Number</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i data-lucide="phone" class="w-5 h-5 text-white/40"></i>
                        </div>
                        <input
                            type="tel"
                            name="mobile"
                            class="input-glass w-full pl-12 pr-4 py-3.5 rounded-xl text-white placeholder-white/50 focus:outline-none"
                            placeholder="Enter your mobile number"
                            required
                        >
                    </div>
                </div>

                <!-- Email -->
                <div class="opacity-0 animate-fade-in-up delay-300">
                    <label class="block text-sm font-medium text-white/80 mb-2">Email Address</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i data-lucide="mail" class="w-5 h-5 text-white/40"></i>
                        </div>
                        <input
                            type="email"
                            name="email"
                            class="input-glass w-full pl-12 pr-4 py-3.5 rounded-xl text-white placeholder-white/50 focus:outline-none"
                            placeholder="you@example.com"
                            required
                        >
                    </div>
                </div>

                <!-- Password -->
                <div class="opacity-0 animate-fade-in-up delay-300">
                    <label class="block text-sm font-medium text-white/80 mb-2">Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i data-lucide="lock" class="w-5 h-5 text-white/40"></i>
                        </div>
                        <input
                            type="password"
                            name="password"
                            id="password"
                            class="input-glass w-full pl-12 pr-12 py-3.5 rounded-xl text-white placeholder-white/50 focus:outline-none"
                            placeholder="Create a strong password"
                            required
                            minlength="6"
                        >
                        <button
                            type="button"
                            onclick="togglePassword()"
                            class="absolute inset-y-0 right-0 pr-4 flex items-center text-white/40 hover:text-white/70 transition-colors"
                        >
                            <i data-lucide="eye" id="eye-icon" class="w-5 h-5"></i>
                        </button>
                    </div>
                    <p class="text-xs text-white/40 mt-2">Must be at least 6 characters</p>
                </div>

                <!-- Submit Button -->
                <div class="pt-2 opacity-0 animate-fade-in-up delay-400">
                    <button
                        type="button"
                        onclick="sendOtp()"
                        id="submitBtn"
                        class="btn-gradient w-full py-4 rounded-xl font-bold text-gray-900 text-lg flex items-center justify-center space-x-2 group"
                    >
                        <span id="btnText">Create Account</span>
                        <i data-lucide="arrow-right" id="btnIcon" class="w-5 h-5 group-hover:translate-x-1 transition-transform"></i>
                        <div id="btnSpinner" class="spinner hidden"></div>
                    </button>
                </div>
            </form>

            <!-- Divider -->
            <div class="relative my-8 opacity-0 animate-fade-in-up delay-400">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-white/10"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="px-4 bg-transparent text-white/40">Already have an account?</span>
                </div>
            </div>

            <!-- Login Link -->
            <div class="text-center opacity-0 animate-fade-in-up delay-400">
                <a
                    href="{{ route('customer.login') }}"
                    class="inline-flex items-center justify-center space-x-2 w-full py-3.5 rounded-xl border border-white/20 text-white font-medium hover:bg-white/10 transition-all duration-300"
                >
                    <i data-lucide="log-in" class="w-5 h-5"></i>
                    <span>Sign In Instead</span>
                </a>
            </div>
        </div>

        <!-- Back to Home -->
        <div class="text-center mt-6 opacity-0 animate-fade-in-up delay-400">
            <a href="{{ url('/') }}" class="inline-flex items-center space-x-2 text-white/60 hover:text-white transition-colors">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                <span>Back to Home</span>
            </a>
        </div>

        <!-- Footer -->
        <p class="text-center text-white/30 text-sm mt-8 opacity-0 animate-fade-in delay-400">
            &copy; {{ date('Y') }} Jetty. All rights reserved.
        </p>
    </div>
</div>

<!-- OTP Verification Modal -->
<div id="otpModal" class="fixed inset-0 modal-overlay hidden flex items-center justify-center z-50 px-4">
    <div class="glass-card rounded-3xl p-8 w-full max-w-md animate-scale-up relative">
        <!-- Close Button -->
        <button onclick="closeOtpModal()" class="absolute top-4 right-4 p-2 rounded-lg hover:bg-white/10 transition-colors">
            <i data-lucide="x" class="w-5 h-5 text-white/60"></i>
        </button>

        <!-- Icon -->
        <div class="text-center mb-6">
            <div class="w-16 h-16 mx-auto rounded-2xl bg-gradient-to-br from-green-400 to-green-600 flex items-center justify-center shadow-lg mb-4">
                <i data-lucide="shield-check" class="w-8 h-8 text-white"></i>
            </div>
            <h2 class="text-2xl font-bold mb-2">Verify Your Email</h2>
            <p class="text-white/60">We've sent a 6-digit code to your email address</p>
        </div>

        <!-- OTP Input -->
        <div class="mb-6">
            <input
                type="text"
                id="otpInput"
                class="input-glass otp-input w-full py-4 rounded-xl text-white text-center text-2xl font-bold tracking-widest focus:outline-none"
                maxlength="6"
                placeholder="------"
                autocomplete="one-time-code"
            >
        </div>

        <!-- Verify Button -->
        <button
            onclick="verifyOtp()"
            id="verifyBtn"
            class="btn-gradient w-full py-4 rounded-xl font-bold text-gray-900 text-lg flex items-center justify-center space-x-2"
        >
            <span id="verifyBtnText">Verify & Continue</span>
            <div id="verifySpinner" class="spinner hidden"></div>
        </button>

        <!-- Resend -->
        <p class="text-center text-white/50 text-sm mt-4">
            Didn't receive code?
            <button onclick="sendOtp()" class="text-accent-gold hover:text-accent-orange font-medium transition-colors">
                Resend
            </button>
        </p>
    </div>
</div>

<!-- Error/Success Modal -->
<div id="alertModal" class="fixed inset-0 modal-overlay hidden flex items-center justify-center z-50 px-4">
    <div class="glass-card rounded-3xl p-8 w-full max-w-md animate-scale-up relative">
        <!-- Close Button -->
        <button onclick="closeAlertModal()" class="absolute top-4 right-4 p-2 rounded-lg hover:bg-white/10 transition-colors">
            <i data-lucide="x" class="w-5 h-5 text-white/60"></i>
        </button>

        <!-- Icon -->
        <div class="text-center mb-6">
            <div id="alertIcon" class="w-16 h-16 mx-auto rounded-2xl flex items-center justify-center shadow-lg mb-4">
                <!-- Icon inserted dynamically -->
            </div>
            <h2 id="alertTitle" class="text-2xl font-bold mb-2"></h2>
            <p id="alertMessage" class="text-white/60"></p>
        </div>

        <!-- Action Links (for email exists error) -->
        <div id="alertLinks" class="hidden space-y-3 mb-6">
            <a href="{{ route('customer.login') }}" class="flex items-center justify-center space-x-2 w-full py-3 rounded-xl bg-primary-500/20 text-primary-300 font-medium hover:bg-primary-500/30 transition-colors">
                <i data-lucide="log-in" class="w-5 h-5"></i>
                <span>Go to Login</span>
            </a>
            <a href="{{ route('customer.password.request') }}" class="flex items-center justify-center space-x-2 w-full py-3 rounded-xl bg-accent-gold/20 text-accent-gold font-medium hover:bg-accent-gold/30 transition-colors">
                <i data-lucide="key" class="w-5 h-5"></i>
                <span>Forgot Password?</span>
            </a>
        </div>

        <!-- Close Button -->
        <button onclick="closeAlertModal()" class="btn-secondary w-full py-3.5 rounded-xl font-medium text-white">
            Close
        </button>
    </div>
</div>

<script>
    // Initialize Lucide icons
    lucide.createIcons();

    // Toggle password visibility
    function togglePassword() {
        const passwordInput = document.getElementById('password');
        const eyeIcon = document.getElementById('eye-icon');

        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            eyeIcon.setAttribute('data-lucide', 'eye-off');
        } else {
            passwordInput.type = 'password';
            eyeIcon.setAttribute('data-lucide', 'eye');
        }
        lucide.createIcons();
    }

    // Set loading state on button
    function setLoading(buttonId, isLoading) {
        const btn = document.getElementById(buttonId);
        const text = document.getElementById(buttonId === 'submitBtn' ? 'btnText' : 'verifyBtnText');
        const icon = document.getElementById('btnIcon');
        const spinner = document.getElementById(buttonId === 'submitBtn' ? 'btnSpinner' : 'verifySpinner');

        if (isLoading) {
            btn.disabled = true;
            text.classList.add('hidden');
            if (icon) icon.classList.add('hidden');
            spinner.classList.remove('hidden');
        } else {
            btn.disabled = false;
            text.classList.remove('hidden');
            if (icon) icon.classList.remove('hidden');
            spinner.classList.add('hidden');
        }
    }

    // Send OTP
    function sendOtp() {
        const form = document.getElementById('registerForm');
        const formData = new FormData(form);

        // Validate form
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        setLoading('submitBtn', true);

        axios.post("{{ route('customer.register.sendOtp') }}", formData)
            .then(res => {
                setLoading('submitBtn', false);
                document.getElementById('otpModal').classList.remove('hidden');
                document.getElementById('otpInput').focus();
            })
            .catch(err => {
                setLoading('submitBtn', false);
                const message = err.response?.data?.message || "Validation error. Please check your input.";
                showAlert('error', 'Registration Failed', message, message.includes('already exists'));
            });
    }

    // Verify OTP
    function verifyOtp() {
        const otp = document.getElementById('otpInput').value;

        if (otp.length !== 6) {
            document.getElementById('otpInput').classList.add('error');
            setTimeout(() => {
                document.getElementById('otpInput').classList.remove('error');
            }, 500);
            return;
        }

        setLoading('verifyBtn', true);

        axios.post("{{ route('customer.register.verifyOtp') }}", { otp })
            .then(res => {
                setLoading('verifyBtn', false);
                if (!res.data.success) {
                    showAlert('error', 'Verification Failed', res.data.message);
                    return;
                }
                closeOtpModal();
                showAlert('success', 'Account Created!', 'Your account has been created successfully. Redirecting to login...');
                setTimeout(() => {
                    window.location.href = "{{ route('customer.login') }}";
                }, 2000);
            })
            .catch(() => {
                setLoading('verifyBtn', false);
                showAlert('error', 'Error', 'Something went wrong. Please try again.');
            });
    }

    // Show alert modal
    function showAlert(type, title, message, showLinks = false) {
        const iconContainer = document.getElementById('alertIcon');
        const alertTitle = document.getElementById('alertTitle');
        const alertMessage = document.getElementById('alertMessage');
        const alertLinks = document.getElementById('alertLinks');

        if (type === 'error') {
            iconContainer.className = 'w-16 h-16 mx-auto rounded-2xl bg-gradient-to-br from-red-400 to-red-600 flex items-center justify-center shadow-lg mb-4';
            iconContainer.innerHTML = '<i data-lucide="x-circle" class="w-8 h-8 text-white"></i>';
            alertTitle.className = 'text-2xl font-bold mb-2 text-red-400';
        } else {
            iconContainer.className = 'w-16 h-16 mx-auto rounded-2xl bg-gradient-to-br from-green-400 to-green-600 flex items-center justify-center shadow-lg mb-4';
            iconContainer.innerHTML = '<i data-lucide="check-circle" class="w-8 h-8 text-white"></i>';
            alertTitle.className = 'text-2xl font-bold mb-2 text-green-400';
        }

        alertTitle.textContent = title;
        alertMessage.textContent = message;

        if (showLinks) {
            alertLinks.classList.remove('hidden');
        } else {
            alertLinks.classList.add('hidden');
        }

        lucide.createIcons();
        document.getElementById('alertModal').classList.remove('hidden');
    }

    // Close modals
    function closeOtpModal() {
        document.getElementById('otpModal').classList.add('hidden');
        document.getElementById('otpInput').value = '';
    }

    function closeAlertModal() {
        document.getElementById('alertModal').classList.add('hidden');
    }

    // Auto-focus OTP input and format
    document.getElementById('otpInput').addEventListener('input', function(e) {
        this.value = this.value.replace(/[^0-9]/g, '').slice(0, 6);
    });
</script>

</body>
</html>
