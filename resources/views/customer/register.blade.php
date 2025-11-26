<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
</head>

<body class="bg-gray-100 flex items-center justify-center min-h-screen">

<div class="bg-white p-8 rounded-xl shadow-md w-full max-w-sm">

    <h2 class="text-2xl font-bold text-blue-400 mb-2 text-center">Create Account</h2>

    <form id="registerForm">
        @csrf

        <input name="first_name" class="w-full mb-3 p-2 border rounded" placeholder="First Name">
        <input name="last_name" class="w-full mb-3 p-2 border rounded" placeholder="Last Name">
        <input name="mobile" class="w-full mb-3 p-2 border rounded" placeholder="Mobile">
        <input name="email" class="w-full mb-3 p-2 border rounded" placeholder="Email">
        <input type="password" name="password" class="w-full mb-3 p-2 border rounded" placeholder="Password">

        <button type="button" 
                onclick="sendOtp()" 
                class="w-full bg-blue-500 text-white py-2 rounded">
            Generate OTP
        </button>
    </form>

</div>

<!-- OTP MODAL -->
<div id="otpModal" 
     class="fixed inset-0 bg-black bg-opacity-40 hidden flex items-center justify-center z-50">

    <div class="bg-white p-8 rounded-2xl shadow-lg w-full max-w-md relative">

        <!-- Close Button -->
        <button onclick="closeOtpModal()" 
                class="absolute top-3 right-3 text-gray-500 hover:text-gray-700 text-xl">
            ✕
        </button>

        <h3 class="text-2xl font-bold text-blue-500 mb-4 text-center">
            Verify Email
        </h3>

        <p class="text-gray-600 text-center mb-4">
            Enter the 6-digit OTP sent to your email address.
        </p>

        <input id="otpInput"
               class="w-full p-3 border rounded-xl text-center tracking-widest text-2xl"
               maxlength="6"
               placeholder="------">

        <p id="otpError" class="text-red-500 text-sm mt-2 hidden"></p>

        <button onclick="verifyOtp()"
                class="w-full mt-6 bg-blue-500 text-white py-3 rounded-xl text-lg">
            Verify OTP
        </button>

    </div>
</div>


<!-- ERROR MODAL -->
<div id="errorModal" 
     class="fixed inset-0 bg-black bg-opacity-40 hidden flex items-center justify-center z-50">

    <div class="bg-white p-8 rounded-2xl w-full max-w-md shadow relative">

        <!-- Close Button -->
        <button onclick="closeErrorModal()" 
                class="absolute top-3 right-3 text-gray-500 hover:text-gray-700 text-xl">
            ✕
        </button>

        <h3 class="text-2xl font-bold text-red-500 mb-3 text-center">Error</h3>
<p id="errorMessage" class="text-gray-700 text-center text-lg mb-4"></p>

<!-- Login + Forgot Password Links -->
<div id="errorLinks" class="text-center hidden">
    <a href="{{ route('customer.login') }}" class="text-blue-600 font-semibold block mb-2">
        ➤ Login
    </a>
    <a href="{{ route('customer.password.request')  }}" class="text-blue-600 font-semibold">
        ➤ Forgot Password?
    </a>
</div>

<button onclick="closeErrorModal()"
        class="w-full mt-6 bg-red-500 text-white py-3 rounded-xl text-lg">
    Close
</button>


    </div>
</div>




<script>
function sendOtp() {
    let formData = new FormData(document.getElementById('registerForm'));

    axios.post("{{ route('customer.register.sendOtp') }}", formData)
    .then(res => {
        document.getElementById('otpModal').classList.remove('hidden');
    })
    .catch(err => {

        if (err.response && err.response.data && err.response.data.message) {
            showErrorModal(err.response.data.message);
        } else {
            showErrorModal("Validation error. Please check your input.");
        }
    });
}


function verifyOtp() {
    let otp = document.getElementById('otpInput').value;

    axios.post("{{ route('customer.register.verifyOtp') }}", { otp: otp })
    .then(res => {
        if (res.data.success) {
            window.location.href = "{{ route('customer.login') }}";
        }
    })
    .catch(err => {
        document.getElementById('otpError').innerText = "Invalid OTP";
        document.getElementById('otpError').classList.remove('hidden');
    });
}

function showErrorModal(message) {
    document.getElementById("errorMessage").innerText = message;
    document.getElementById("errorModal").classList.remove("hidden");

    // Show login/forgot links only for specific message
    if (message.includes("Email already exists")) {
        document.getElementById("errorLinks").classList.remove("hidden");
    } else {
        document.getElementById("errorLinks").classList.add("hidden");
    }
}


function closeErrorModal() {
    document.getElementById("errorModal").classList.add("hidden");
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
