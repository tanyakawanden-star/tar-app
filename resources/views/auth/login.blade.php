<!doctype html>
<html>
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title>Login - TAR System</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gradient-to-br from-slate-900 to-slate-800 min-h-screen flex items-center justify-center p-4">
  <div class="bg-white rounded-2xl shadow-2xl p-8 w-full max-w-md">
    <div class="text-center mb-8">
      <div class="w-16 h-16 bg-blue-600 rounded-xl flex items-center justify-center mx-auto mb-4">
        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
        </svg>
      </div>
      <h1 class="text-2xl font-bold text-gray-800">TAR System</h1>
      <p class="text-gray-500 text-sm">Travel Authorization Request</p>
    </div>

    @if(session('error'))
      <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded mb-4 text-sm">
        {{ session('error') }}
      </div>
    @endif

    @if(session('success'))
      <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded mb-4 text-sm">
        {{ session('success') }}
      </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
      @csrf
      <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
        <input type="email" name="email" value="{{ old('email') }}" required autofocus 
               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none">
        @error('email')
          <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
        @enderror
      </div>

      <div class="mb-6">
        <label class="block text-sm font-medium text-gray-700 mb-2">Password</label>
        <input type="password" name="password" required 
               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none">
        @error('password')
          <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
        @enderror
      </div>

      <div class="mb-6">
        <label class="flex items-center">
          <input type="checkbox" name="remember" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
          <span class="ml-2 text-sm text-gray-600">Remember me</span>
        </label>
      </div>

      <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-lg transition duration-200">
        Login
      </button>
    </form>

    <div class="mt-6 text-center text-sm text-gray-500">
      <p>Demo Accounts:</p>
      <p class="text-xs mt-2">john@local.test / jane@local.test / robert@local.test</p>
      <p class="text-xs">Password: <strong>password</strong></p>
    </div>
  </div>
</body>
</html>