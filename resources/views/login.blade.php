<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Login | YourApp</title>

  <!-- Tailwind CSS CDN -->
  <script src="https://cdn.tailwindcss.com"></script>

  <style>
    .login-container {
      background-image: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
  </style>
</head>
<body class="bg-gray-100 h-screen font-sans">
  <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 login-container">
    <div class="max-w-md w-full bg-white rounded-xl shadow-2xl overflow-hidden p-10">
      <div class="text-center">
        <h2 class="mt-6 text-3xl font-extrabold text-gray-900">Welcome back</h2>
        <p class="mt-2 text-sm text-gray-600">Sign in to your account</p>
      </div>

      <!-- Login Form -->
      <form class="mt-8 space-y-6" method="POST" action="#">
        <!-- Error message example (can be toggled with JS if needed) -->
        <!--
        <div class="bg-red-50 text-red-500 px-4 py-3 rounded-lg text-sm">
          <ul class="list-disc pl-5">
            <li>Invalid email or password</li>
          </ul>
        </div>
        -->

        <div class="rounded-md -space-y-px">
          <div class="mb-4">
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email address</label>
            <input id="email" name="email" type="email" required placeholder="name@company.com"
              class="appearance-none block w-full px-3 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 rounded focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
          </div>

          <div class="mb-1">
            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
            <input id="password" name="password" type="password" required placeholder="••••••••"
              class="appearance-none block w-full px-3 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 rounded focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
          </div>

          <div class="flex items-center justify-between mt-4">
            <div class="flex items-center">
              <input id="remember_me" name="remember" type="checkbox"
                class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
              <label for="remember_me" class="ml-2 block text-sm text-gray-900">
                Remember me
              </label>
            </div>

            <div class="text-sm">
              <a href="#" class="font-medium text-indigo-600 hover:text-indigo-500">Forgot your password?</a>
            </div>
          </div>
        </div>

        <div>
          <button type="submit"
            class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out">
            Sign in
          </button>
        </div>

        <div class="text-center mt-4">
          <p class="text-sm text-gray-600">
            Don't have an account?
            <a href="#" class="font-medium text-indigo-600 hover:text-indigo-500">Sign up</a>
          </p>
        </div>

        <!-- Social login -->
        <div class="mt-6">
          <div class="relative">
            <div class="absolute inset-0 flex items-center">
              <div class="w-full border-t border-gray-300"></div>
            </div>
            <div class="relative flex justify-center text-sm">
              <span class="px-2 bg-white text-gray-500">Or continue with</span>
            </div>
          </div>

          <div class="mt-6 grid grid-cols-2 gap-3">
            <!-- Google Login -->
            <div>
              <a href="#" class="w-full inline-flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                  <path d="M12.24 10.285V14.4h6.806c-.275 1.765-2.056 5.174-6.806 5.174-4.095 0-7.439-3.389-7.439-7.574s3.345-7.574 7.439-7.574c2.33 0 3.891.989 4.785 1.849l3.254-3.138C18.189 1.186 15.479 0 12.24 0c-6.635 0-12 5.365-12 12s5.365 12 12 12c6.926 0 11.52-4.869 11.52-11.726 0-.788-.085-1.39-.189-1.989H12.24z"/>
                </svg>
              </a>
            </div>

            <!-- GitHub Login -->
            <div>
              <a href="#" class="w-full inline-flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                  <path fill-rule="evenodd" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z" clip-rule="evenodd" />
                </svg>
              </a>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
</body>
</html>
