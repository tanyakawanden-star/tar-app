@extends('layouts.app')

@section('title', 'User Management')

@section('content')
<div class="mb-6 flex items-center justify-between">
  <h1 class="text-2xl font-bold">User Management</h1>
  <a href="{{ route('users.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium">
    + Add User
  </a>
</div>

<div class="bg-white rounded-xl shadow overflow-hidden">
  <table class="w-full text-sm">
    <thead class="bg-gray-50 text-left text-gray-600">
      <tr>
        <th class="px-4 py-3">Name</th>
        <th class="px-4 py-3">Email</th>
        <th class="px-4 py-3">Role</th>
        <th class="px-4 py-3">Department</th>
        <th class="px-4 py-3">Job Grade</th>
        <th class="px-4 py-3">Actions</th>
      </tr>
    </thead>
    <tbody class="divide-y">
      @forelse($users as $user)
      <tr class="hover:bg-gray-50">
        <td class="px-4 py-3 font-medium">{{ $user->name }}</td>
        <td class="px-4 py-3">{{ $user->email }}</td>
        <td class="px-4 py-3">
          <span class="px-2 py-1 rounded-full text-xs font-medium 
            @if($user->role === 'md') bg-purple-100 text-purple-800
            @elseif($user->role === 'supervisor') bg-green-100 text-green-800
            @else bg-blue-100 text-blue-800 @endif">
            {{ ucfirst($user->role) }}
          </span>
        </td>
        <td class="px-4 py-3">{{ $user->department ?? '-' }}</td>
        <td class="px-4 py-3">{{ $user->job_grade ?? '-' }}</td>
        <td class="px-4 py-3 space-x-2">
          <a href="{{ route('users.edit', $user) }}" class="text-blue-600 hover:underline">Edit</a>
          @if($user->id !== auth()->id())
          <form method="POST" action="{{ route('users.destroy', $user) }}" class="inline" onsubmit="return confirm('Delete this user?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="text-red-600 hover:underline">Delete</button>
          </form>
          @endif
        </td>
      </tr>
      @empty
      <tr>
        <td colspan="6" class="px-4 py-8 text-center text-gray-500">No users found</td>
      </tr>
      @endforelse
    </tbody>
  </table>
</div>
@endsection