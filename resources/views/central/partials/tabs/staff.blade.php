<!-- Staff Management Tab -->
<div x-show="tab === 'staff'" x-cloak class="space-y-6">
  <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
    <h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-6">👨‍💼 Manage Admins</h2>
    
    <!-- Create New SFAO Admin -->
    <div class="mb-8">
      <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-4">Create New SFAO Admin</h3>
      
      <form method="POST" action="{{ route('central.staff.invite') }}" class="space-y-4">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <!-- Name -->
          <div>
            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
              Full Name
            </label>
            <input type="text" id="name" name="name" required
                   value="{{ old('name') }}"
                   class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 
                          focus:ring-2 focus:ring-bsu-red focus:border-transparent
                          dark:bg-gray-700 dark:text-white">
            @error('name')
              <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
          </div>

          <!-- Email -->
          <div>
            <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
              Email Address
            </label>
            <input type="email" id="email" name="email" required
                   value="{{ old('email') }}"
                   class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 
                          focus:ring-2 focus:ring-bsu-red focus:border-transparent
                          dark:bg-gray-700 dark:text-white">
            @error('email')
              <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
          </div>
        </div>

        <!-- Campus Assignment -->
        <div>
          <label for="campus_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
            Campus Assignment
          </label>
          <select id="campus_id" name="campus_id" required
                  class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 
                         focus:ring-2 focus:ring-bsu-red focus:border-transparent
                         dark:bg-gray-700 dark:text-white">
            <option value="">Select Campus</option>
            @foreach(\App\Models\Campus::constituent()->withSfaoAdmin()->get() as $campus)
              <option value="{{ $campus->id }}" {{ old('campus_id') == $campus->id ? 'selected' : '' }}>
                {{ $campus->name }}
              </option>
            @endforeach
          </select>
          @error('campus_id')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
          @enderror
        </div>

        <!-- Submit Button -->
        <div class="flex justify-end">
          <button type="submit" 
                  class="bg-bsu-red text-white px-6 py-2 rounded-lg hover:bg-bsu-redDark 
                         transition duration-200 flex items-center space-x-2">
            <span>👤</span>
            <span>Create Account & Send Verification</span>
          </button>
        </div>
      </form>
    </div>

    <!-- Current Staff -->
    <div class="mb-8">
      <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-4">Current SFAO Staff</h3>
      
      <div class="overflow-x-auto">
        <table class="min-w-full bg-white dark:bg-gray-700 rounded-lg shadow">
          <thead class="bg-gray-50 dark:bg-gray-600">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                Name
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                Email
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                Campus
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                Status
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                Actions
              </th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200 dark:divide-gray-600">
            @forelse(\App\Models\User::where('role', 'sfao')->whereNotNull('email_verified_at')->with(['campus', 'invitation'])->get() as $staff)
              <tr class="hover:bg-gray-50 dark:hover:bg-gray-600">
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex items-center">
                    <div class="flex-shrink-0 h-8 w-8">
                      <div class="h-8 w-8 rounded-full bg-bsu-red flex items-center justify-center">
                        <span class="text-white text-sm font-medium">
                          {{ strtoupper(substr($staff->name, 0, 1)) }}
                        </span>
                      </div>
                    </div>
                    <div class="ml-3">
                      <div class="text-sm font-medium text-gray-900 dark:text-white">
                        {{ $staff->name }}
                      </div>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                  {{ $staff->email }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                  {{ $staff->campus->name ?? 'Not Assigned' }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                               bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                    Active
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                  <form method="POST" action="{{ route('central.staff.deactivate', $staff->id) }}" 
                        class="inline" 
                        onsubmit="return confirm('⚠️ WARNING: This will permanently remove {{ $staff->name }} from the system. Their account will be completely deleted and cannot be recovered. Are you sure you want to proceed?')">
                    @csrf
                    <button type="submit" 
                            class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 
                                   hover:underline font-semibold">
                      🗑️ Remove Staff
                    </button>
                  </form>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="5" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                  No SFAO staff members found.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    <!-- Invitation Status -->
    <div>
      <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-4">Invitation Status</h3>
      
      <div class="overflow-x-auto">
        <table class="min-w-full bg-white dark:bg-gray-700 rounded-lg shadow">
          <thead class="bg-gray-50 dark:bg-gray-600">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                Name
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                Email
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                Campus
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                Invited
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                Status
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                Verified
              </th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200 dark:divide-gray-600">
            @forelse(\App\Models\Invitation::with(['campus', 'inviter'])->orderBy('created_at', 'desc')->get() as $invitation)
              <tr class="hover:bg-gray-50 dark:hover:bg-gray-600">
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex items-center">
                    <div class="flex-shrink-0 h-8 w-8">
                      <div class="h-8 w-8 rounded-full 
                        @if($invitation->status === 'active') bg-green-400
                        @elseif($invitation->status === 'pending') bg-yellow-400
                        @elseif($invitation->status === 'removed') bg-gray-400
                        @else bg-red-400
                        @endif flex items-center justify-center">
                        <span class="text-white text-sm font-medium">
                          {{ strtoupper(substr($invitation->name, 0, 1)) }}
                        </span>
                      </div>
                    </div>
                    <div class="ml-3">
                      <div class="text-sm font-medium text-gray-900 dark:text-white">
                        {{ $invitation->name }}
                      </div>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                  {{ $invitation->email }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                  {{ $invitation->campus->name ?? 'Not Assigned' }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                  {{ $invitation->created_at->format('M d, Y') }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                    @if($invitation->status === 'active') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                    @elseif($invitation->status === 'pending') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                    @elseif($invitation->status === 'removed') bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200
                    @else bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                    @endif">
                    {{ ucfirst($invitation->status) }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                  @if($invitation->accepted_at)
                    {{ $invitation->accepted_at->format('M d, Y H:i') }}
                  @else
                    <span class="text-gray-400">Not verified</span>
                  @endif
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="6" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                  No invitations found.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
