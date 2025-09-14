<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Required Documents | BSU Scholarship</title>
    <link rel="icon" type="image/png" href="{{ asset('images/Batangas_State_Logo.png') }}">
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Tailwind Custom Config -->
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        bsu: {
                            red: '#b91c1c',
                            redDark: '#991b1b',
                            light: '#fef2f2'
                        }
                    }
                }
            }
        };
    </script>
</head>
<body class="bg-bsu-light font-sans">

    <!-- Header -->
    <header class="bg-bsu-red text-white shadow-md">
        <div class="max-w-7xl mx-auto px-4 py-4 flex items-center justify-between">
            <h1 class="text-2xl font-bold">BSU Scholarship System</h1>
            <a href="{{ route('student.dashboard') }}" class="bg-white text-bsu-red font-semibold px-3 py-2 rounded hover:bg-gray-100">Dashboard</a>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-4xl mx-auto mt-12 p-6 bg-white rounded-lg shadow-md">
        <h2 class="text-3xl font-bold text-bsu-red mb-2 text-center">Upload Required Documents</h2>
        <p class="text-center text-lg text-gray-700 mb-6">
            For <span class="font-semibold text-bsu-red">{{ $scholarship->scholarship_name }}</span>
        </p>

        <!-- Success Message -->
        @if(session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 p-4 mb-6 rounded">
                <p class="text-green-700 font-medium">{{ session('success') }}</p>
            </div>
        @endif

        <!-- Validation Errors -->
        @if($errors->any())
            <div class="bg-red-100 border-l-4 border-red-500 p-4 mb-6 rounded">
                <ul class="list-disc pl-5 text-red-700">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Upload Form -->
        <form action="{{ route('student.upload-documents.submit', ['scholarship_id' => $scholarship->id]) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <div>
                <label class="block font-semibold mb-2 text-gray-700">Form 137 <span class="text-red-500">*</span></label>
                <input type="file" name="form_137" accept=".pdf,.jpg,.png" required
                       class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-bsu-red">
            </div>

            <div>
                <label class="block font-semibold mb-2 text-gray-700">Copy of Grades (Previous Semester) <span class="text-red-500">*</span></label>
                <input type="file" name="grades" accept=".pdf,.jpg,.png" required
                       class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-bsu-red">
            </div>

            <div>
                <label class="block font-semibold mb-2 text-gray-700">Certificate of Employment (if government employee)</label>
                <input type="file" name="certificate" accept=".pdf,.jpg,.png"
                       class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-bsu-red">
                <p class="text-sm text-gray-500 mt-1">Optional if not applicable</p>
            </div>

            <div>
                <label class="block font-semibold mb-2 text-gray-700">Application Form <span class="text-red-500">*</span></label>
                <input type="file" name="application_form" accept=".pdf,.jpg,.png" required
                       class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-bsu-red">
            </div>

            <button type="submit" class="w-full bg-bsu-red text-white font-semibold px-4 py-3 rounded hover:bg-bsu-redDark transition-colors">
                Submit Documents
            </button>
        </form>
    </main>

    <!-- Footer -->
    <footer class="bg-bsu-light text-gray-600 mt-12 py-6 text-center">
        &copy; {{ date('Y') }} Batangas State University. All rights reserved.
    </footer>

</body>
</html>
