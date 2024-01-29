<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
      {{ __('Users') }}
    </h2>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
  </x-slot>

  <div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
      <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900 dark:text-gray-100">
          <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-800">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider">Username</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider">Email</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider">Role</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider">Action</th>
              </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
              @foreach ($users as $user)
              <tr>
                <td class="px-6 py-4 whitespace-nowrap text-gray-900 dark:text-gray-100">{{ $user->username }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-gray-900 dark:text-gray-100">{{ $user->email }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-gray-900 dark:text-gray-100">{{ $user->role->name }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-gray-900 dark:text-gray-100">{{ $user->active ? 'Active' : 'Inactive' }}</td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <form method="post" action="{{ route('users.destroy', $user->id) }}">
                    @csrf
                    @method('delete')

                    <button type="submit" class="px-4 py-2 rounded-md text-sm font-medium border-0 focus:outline-none">
                      @if($user->active)
                      <i class="fas fa-toggle-on"></i>
                      @else
                      <i class="fas fa-toggle-off"></i>
                      @endif
                    </button>
                  </form>
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
          <x-input-error class="mt-2" :messages="$errors->get('error')" />
        </div>
      </div>
    </div>
  </div>
</x-app-layout>