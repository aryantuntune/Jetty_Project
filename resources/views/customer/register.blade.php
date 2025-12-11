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

<!-- 🔵 Sea Video Background -->
<video autoplay loop muted playsinline class="bg-video">
    <source src="{{ asset('videos/1.mp4') }}" type="video/mp4">
</video>

<!-- 🔵 Main Glass Card -->
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
