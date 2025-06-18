@extends('components.layout')
@section('title', 'forgot password')
@section('content')

    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="rounded-xl border bg-card text-card-foreground shadow w-full max-w-md">
            <div class="p-6 space-y-1 text-center">
                <div class="flex justify-center mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-primary" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2">
                        <path d="m18 7 4 2v11a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V9l4-2" />
                        <path d="M14 22v-4a2 2 0 0 0-2-2a2 2 0 0 0-2 2v4" />
                        <path d="M18 22V5l-6-3-6 3v17" />
                        <path d="M12 7v5" />
                        <path d="M10 9h4" />
                    </svg>
                </div>
                <h3 class="text-2xl font-bold tracking-tight">Forgot Password</h3>
                <p class="text-sm text-muted-foreground">Enter your email address to receive an OTP</p>
            </div>

            <div class="p-6 pt-0">
                @if (session('status'))
                    <div class="mb-4 text-sm text-green-600">
                        {{ session('status') }}
                    </div>
                @endif

                <form action="{{ url()->current() }}" method="POST" class="space-y-4">
                    @csrf

                    <div class="space-y-2">
                        <label for="email" class="block text-sm font-medium">Email Address</label>
                        <input id="email" name="email" type="email" required placeholder="admin@church.org"
                            value="{{ old('email') }}"
                            class="w-full rounded-md border px-3 py-2 text-sm shadow-sm focus:ring-1 focus:ring-primary">
                        @error('email')
                            <p class="text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit"
                        class="w-full inline-flex justify-center rounded-md px-4 py-2 text-sm font-medium text-white bg-gradient-to-r from-blue-500 to-blue-600 shadow-lg shadow-blue-500/20 hover:from-blue-600 hover:to-blue-700 hover:shadow-blue-600/30 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-300 ease-in-out">
                        Send OTP
                    </button>
                </form>

                <div class="mt-4 text-center">
                    <a href="{{ url('/sign-in') }}"
                        class="inline-flex items-center text-sm text-primary hover:font-bold transition-all duration-300 ease-in-out">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2">
                            <path d="m12 19-7-7 7-7" />
                            <path d="M19 12H5" />
                        </svg>
                        Back to Login
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
