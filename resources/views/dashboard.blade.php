<h1>Welcome to the Dashboard</h1>

<p>
    @if(auth()->user()->role === 'admin')
        Welcome, Admin! You have full access.
    @elseif(auth()->user()->role === 'staff')
        Welcome, Staff! You have limited access.
    @endif
</p>
