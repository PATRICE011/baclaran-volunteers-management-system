<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Forgot Password</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@^3/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="rounded-xl border bg-card text-card-foreground shadow w-full max-w-md">
            <div class="p-6 space-y-1 text-center">
                <div class="flex justify-center mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-primary" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2">
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
                        class="w-full inline-flex justify-center rounded-md bg-primary px-4 py-2 text-sm font-medium text-white shadow hover:bg-primary/90 focus:outline-none focus:ring-1 focus:ring-primary">
                        Send OTP
                    </button>
                </form>

                <div class="mt-4 text-center">
                    <a href="{{ url('/login') }}" class="inline-flex items-center text-sm text-primary hover:underline">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2">
                            <path d="m12 19-7-7 7-7" />
                            <path d="M19 12H5" />
                        </svg>
                        Back to Login
                    </a>
                </div>

                <div class="mt-4 text-center text-sm text-muted-foreground">
                    Demo: Any valid email will work
                </div>
            </div>
        </div>
    </div>
</body>

</html>
