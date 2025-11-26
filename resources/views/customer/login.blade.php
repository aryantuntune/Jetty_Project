@extends('layouts.app')

@section('content')
<div class="container d-flex justify-content-center mt-5">
    <div class="card p-4 shadow-sm" style="width: 420px; border-radius: 15px;">
        
        <h3 class="text-primary fw-bold mb-2">Log In</h3>
       

     <form method="POST" action="{{ route('customer.login.submit') }}">

            @csrf

            {{-- Email Input --}}
            <div class="mb-3">
                <label class="form-label fw-semibold">Email Address</label>
                <input 
                    type="email" 
                    name="email"
                    class="form-control @error('email') is-invalid @enderror"
                    placeholder="Enter Email Address"
                    required
                >
                @error('email')
                    <span class="text-danger small">{{ $message }}</span>
                @enderror
            </div>

            {{-- Password --}}
            <div class="mb-3">
                <label class="form-label fw-semibold">Password</label>
                <input 
                    type="password" 
                    name="password"
                    class="form-control @error('password') is-invalid @enderror"
                    placeholder="Enter Password"
                    required
                >
                @error('password')
                    <span class="text-danger small">{{ $message }}</span>
                @enderror
            </div>

           

            {{-- Submit Button --}}
            <button 
                type="submit" 
                class="btn btn-primary w-100 mt-3"
                style="border-radius: 25px;"
            >
                Log In
            </button>

            <div class="text-center mt-3">
                <small>Don't Have An Account? 
                    <a href="{{ route('customer.register') }}" class="text-decoration-none">Sign Up</a>
                </small>
            </div>

        </form>
    </div>
</div>
@endsection
