<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Church Volunteers Login</title>
    <!-- Include Tailwind via CDN or npm-built CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@^3/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="rounded-xl border bg-card text-card-foreground shadow w-full max-w-md p-6">
            <div class="text-center space-y-1 mb-6">
                <div class="flex justify-center mb-4">
                    <!-- church icon SVG -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-primary" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2">
                        <path d="m18 7 4 2v11a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V9l4-2" />
                        <path d="M14 22v-4a2 2 0 0 0-2-2a2 2 0 0 0-2 2v4" />
                        <path d="M18 22V5l-6-3-6 3v17" />
                        <path d="M12 7v5" />
                        <path d="M10 9h4" />
                    </svg>
                </div>
                <h3 class="text-2xl font-bold tracking-tight">Church Volunteers</h3>
                <p class="text-sm text-muted-foreground">Sign in to your volunteer management account</p>
            </div>

            <form action="{{ route('login') }}" method="POST" class="space-y-4">
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
                    <input id="password" name="password" type="password" required placeholder="Enter your password"
                        class="w-full rounded-md border px-3 py-2 text-sm shadow-sm focus:ring-1 focus:ring-primary">
                    @error('password')
                        <p class="text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit"
                    class="w-full inline-flex justify-center rounded-md bg-primary px-4 py-2 text-sm font-medium text-white shadow hover:bg-primary/90 focus:outline-none focus:ring-1 focus:ring-primary">
                    Sign In
                </button>
            </form>

            <div class="mt-4 text-center text-sm text-muted-foreground">
                Demo credentials: Any email and password will work
            </div>
            <div class="mt-2 text-center">
                <a href="#" class="text-sm text-primary hover:underline">Forgot Password?</a>
            </div>
        </div>
    </div>
</body>

</html>
