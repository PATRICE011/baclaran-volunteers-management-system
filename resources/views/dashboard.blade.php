<h1>Welcome to the Dashboard</h1>

<p>
    @if(auth()->user()->role === 'admin')
        Welcome, Admin! You have full access.
    @elseif(auth()->user()->role === 'staff')
        Welcome, Staff! You have limited access.
    @endif
    <form method="POST" action="{{ route('logout') }}">
    @csrf
    <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition duration-150 ease-in-out">
        Logout
    </button>
</form>

</p>
