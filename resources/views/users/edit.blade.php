@extends('layouts.app')

@section('title', 'Edit User')

@section('content')
<div class="max-w-2xl mx-auto">
  <div class="mb-6">
    <a href="{{ route('users.index') }}" class="text-blue-600 hover:underline text-sm">← Back to Users</a>
    <h1 class="text-2xl font-bold mt-2">Edit User</h1>
  </div>

  <div class="bg-white rounded-xl shadow p-6">
    <form method="POST" action="{{ route('users.update', $user) }}">
      @csrf
      @method('PUT')
      
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Name *</label>
          <input type="text" name="name" value="{{ old('name', $user->name) }}" required 
                 class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
          @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
          <input type="email" name="email" value="{{ old('email', $user->email) }}" required 
                 class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
          @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">New Password (leave blank to keep current)</label>
          <input type="password" name="password" 
                 class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
          @error('password')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Confirm New Password</label>
          <input type="password" name="password_confirmation" 
                 class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Role *</label>
          <select name="role" required class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
            <option value="employee" {{ old('role', $user->role) === 'employee' ? 'selected' : '' }}>Employee</option>
            <option value="supervisor" {{ old('role', $user->role) === 'supervisor' ? 'selected' : '' }}>Supervisor</option>
            <option value="md" {{ old('role', $user->role) === 'md' ? 'selected' : '' }}>MD</option>
          </select>
          @error('role')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Department</label>
          <input type="text" name="department" value="{{ old('department', $user->department) }}" 
                 class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
          @error('department')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Job Grade</label>
          <input type="text" name="job_grade" value="{{ old('job_grade', $user->job_grade) }}" 
                 class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
          @error('job_grade')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
      </div>

      <div class="flex justify-end gap-2">
        <a href="{{ route('users.index') }}" class="px-4 py-2 border rounded-lg hover:bg-gray-50">Cancel</a>
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">Update User</button>
      </div>
    </form>
  </div>
</div>
@endsection