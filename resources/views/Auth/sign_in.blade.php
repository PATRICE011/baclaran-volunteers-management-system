@extends('components.layout')
@section('title', 'Login')
@section('content')

<div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="rounded-xl border bg-card text-card-foreground shadow w-full max-w-md p-6">
        <div class="text-center space-y-1 mb-6">
            <div class="flex justify-center mb-4">
                <!-- church icon SVG -->
                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-primary" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2">
                    <path d="m18 7 4 2v11a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V9l4-2" />
                    <path d="M14 22v-4a2 2 0 0 0-2-2a2 2 0 0 0-2 2v4" />
                    <path d="M18 22V5l-6-3-6 3v17" />
                    <path d="M12 7v5" />
                    <path d="M10 9h4" />
                </svg>
            </div>
            <h3 class="text-2xl font-bold tracking-tight">Baclaran Church</h3>
            <p class="text-sm text-muted-foreground">Volunteer Management System</p>
        </div>

        <form action="{{ route('authorizeUser') }}" method="POST" class="space-y-4">
            @csrf

            <div class="space-y-2">
                <label for="email" class="block text-sm font-medium">Email</label>
                <input id="email" name="email" type="email" required placeholder="admin@church.org"
                    value="{{ old('email') }}"
                    class="w-full rounded-md border px-3 py-2 text-sm shadow-sm focus:ring-1 focus:ring-primary">
                @error('email')
                <p class="text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="space-y-2">
                <label for="password" class="block text-sm font-medium">Password</label>
                <div class="relative">
                    <input id="current_password" name="password" type="password" required placeholder="Enter your password"
                        class="w-full rounded-md border px-3 py-2 text-sm shadow-sm focus:ring-1 focus:ring-primary">
                    <button type="button" id="toggleCurrentPassword" class="absolute inset-y-0 right-0 flex items-center px-3 text-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88"></path>
                        </svg>
                    </button>
                </div>

                @error('password')
                <p class="text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit"
                class="w-full inline-flex justify-center rounded-md px-4 py-2 text-sm font-medium text-white 
           bg-gradient-to-r from-blue-500 to-blue-600 shadow-lg shadow-blue-500/20
           hover:from-blue-600 hover:to-blue-700 hover:shadow-blue-600/30
           focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2
           transition-all duration-300 ease-in-out">
                Sign In
            </button>

        </form>

        <div class="mt-2 text-center">
            <a href="{{ url('/find-email') }}"
                class="text-sm text-primary hover:font-bold transition-all duration-300 ease-in-out">
                Forgot Password?
            </a>
        </div>

    </div>
</div>
@endsection
@section('scripts')
<script>
    // Toggle password visibility functions
    function togglePasswordVisibility(inputId, toggleButtonId) {
        const input = document.getElementById(inputId);
        const toggleButton = document.getElementById(toggleButtonId);
        const icon = toggleButton.querySelector("svg");

        if (input.type === "password") {
            input.type = "text";
            icon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"></path><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>`;
        } else {
            input.type = "password";
            icon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88">`;
        }
    }

    // Attach event listeners for password visibility toggles
    document.getElementById("toggleCurrentPassword")?.addEventListener("click", function() {
        togglePasswordVisibility(
            "current_password",
            "toggleCurrentPassword"
        );
    });
</script>
@endsection