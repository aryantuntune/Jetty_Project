

<div class="container">
    <h1>Welcome, {{ Auth::guard('customer')->user()->name }}</h1>

    <p>Your email: {{ Auth::guard('customer')->user()->email }}</p>

    <form id="customer-logout-form" action="{{ route('customer.logout') }}" method="POST">
        @csrf
        <button type="submit" class="btn btn-danger">Logout</button>
    </form>
</div>

