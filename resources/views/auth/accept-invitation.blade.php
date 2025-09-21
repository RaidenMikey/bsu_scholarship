<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accept Invitation - BSU Scholarship System</title>
    <link rel="icon" type="image/png" href="{{ asset('images/Batangas_State_Logo.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'bsu-red': '#8B0000',
                        'bsu-redDark': '#660000',
                    }
                }
            }
        }
    </script>
</head>
<body class="h-full">
    <div class="min-h-full flex flex-col justify-center py-12 sm:px-6 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <div class="flex justify-center">
                <img class="h-16 w-auto" src="{{ asset('images/Batangas_State_Logo.png') }}" alt="BSU Logo">
            </div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Accept Invitation
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Set up your SFAO admin account
            </p>
        </div>

        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
            <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
                <!-- Invitation Details -->
                <div class="mb-6 p-4 bg-blue-50 rounded-lg">
                    <h3 class="text-lg font-medium text-blue-900 mb-2">Invitation Details</h3>
                    <div class="space-y-1 text-sm text-blue-800">
                        <p><strong>Name:</strong> {{ $invitation->name }}</p>
                        <p><strong>Email:</strong> {{ $invitation->email }}</p>
                        <p><strong>Campus:</strong> {{ $invitation->campus->name }}</p>
                        <p><strong>Invited by:</strong> {{ $invitation->inviter->name }}</p>
                        <p><strong>Expires:</strong> {{ $invitation->expires_at->format('M d, Y h:i A') }}</p>
                    </div>
                </div>

                <!-- Error Messages -->
                @if($errors->any())
                    <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded">
                        <ul class="list-disc list-inside">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Success Messages -->
                @if(session('success'))
                    <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded">
                        {{ session('success') }}
                    </div>
                @endif

                <!-- Error Messages -->
                @if(session('error'))
                    <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded">
                        {{ session('error') }}
                    </div>
                @endif

                <!-- Password Setup Form -->
                <form class="space-y-6" method="POST" action="{{ route('invitation.accept', $invitation->token) }}">
                    @csrf
                    
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">
                            Password
                        </label>
                        <div class="mt-1">
                            <input id="password" name="password" type="password" required
                                   class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md 
                                          placeholder-gray-400 focus:outline-none focus:ring-bsu-red focus:border-bsu-red 
                                          sm:text-sm">
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Minimum 8 characters</p>
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700">
                            Confirm Password
                        </label>
                        <div class="mt-1">
                            <input id="password_confirmation" name="password_confirmation" type="password" required
                                   class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md 
                                          placeholder-gray-400 focus:outline-none focus:ring-bsu-red focus:border-bsu-red 
                                          sm:text-sm">
                        </div>
                    </div>

                    <div>
                        <button type="submit"
                                class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md 
                                       shadow-sm text-sm font-medium text-white bg-bsu-red hover:bg-bsu-redDark 
                                       focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-bsu-red">
                            Create Account & Sign In
                        </button>
                    </div>
                </form>

                <!-- Terms and Conditions -->
                <div class="mt-6 text-center">
                    <p class="text-xs text-gray-500">
                        By creating an account, you agree to the terms and conditions of the BSU Scholarship System.
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
