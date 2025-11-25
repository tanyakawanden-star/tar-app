<!doctype html>
<html>
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title>@yield('title', 'TAR System')</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <style>
    /* Small polish */
    .badge { @apply inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium; }
  </style>
</head>
<body class="bg-gray-50 text-gray-800">
  <nav class="bg-white border-b shadow-sm">
    <div class="max-w-6xl mx-auto px-4 py-3 flex items-center justify-between">
      <div class="flex items-center gap-3">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
          <img src="https://raw.githubusercontent.com/simple-icons/simple-icons/develop/icons/laravel.svg" alt="logo" class="w-8 h-8">
          <div>
            <div class="font-semibold text-lg">TAR System</div>
            <div class="text-xs text-gray-500">Travel Authorization Requests</div>
          </div>
        </a>
      </div>

      <div class="flex items-center gap-4">
        @auth
          <div class="text-sm text-gray-600">
            <span class="font-medium">{{ auth()->user()->name }}</span>
            <span class="text-xs text-gray-400">• {{ auth()->user()->role }}</span>
          </div>
          <form method="POST" action="{{ route('logout') }}">@csrf<button class="bg-red-500 text-white px-3 py-1 rounded">Logout</button></form>
        @endauth
      </div>
    </div>
  </nav>

  <div class="max-w-6xl mx-auto p-6">
    @if(session('success'))
      <div class="bg-green-50 border border-green-200 p-3 rounded mb-4 text-sm text-green-700">{{ session('success') }}</div>
    @endif
    @yield('content')
  </div>
  @if(in_array(auth()->user()->role, ['md', 'supervisor']))
  <a href="{{ route('users.index') }}" class="text-sm text-gray-600 hover:text-gray-900">Users</a>
  @endif
</body>
</html>
