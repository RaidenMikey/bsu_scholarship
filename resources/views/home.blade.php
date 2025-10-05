<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Spartan Scholarship</title>
  <link rel="icon" href="{{ asset('favicon.ico') }}">
  <link rel="stylesheet" href="{{ asset('css/style.css') }}">
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/alpinejs" defer></script>
</head>

<body class="bg-gray-100 text-center">
  <!-- Navbar -->
  <nav class="fixed top-0 left-0 w-full bg-gray-800 shadow z-50" x-data="{ open: false }">
    <div class="px-4 sm:px-6 lg:px-8"> <!-- removed max-w-7xl mx-auto -->
      <div class="flex justify-between items-center h-16 w-full">
        
        <!-- Logo (Left) -->
        <div class="flex items-center">
          <a href="#home" class="flex items-center space-x-2 transition-transform duration-200 hover:scale-105">
            <img src="{{ asset('images/Batangas_State_Logo.png') }}" alt="BSU Logo" class="h-10 w-auto">
            <div class="text-white leading-tight text-left">
              <div class="text-xl font-bold">Batangas State University</div>
              <div class="text-sm italic text-red-600">The National Engineering University</div>
            </div>
          </a>
        </div>

        <!-- Desktop Menu (Right) -->
        <div class="hidden md:flex items-center space-x-4">
          <a href="#home" class="px-3 py-2 text-white hover:text-red-600 transition">Home</a>
          <a href="#about" class="px-3 py-2 text-white hover:text-red-600 transition">About</a>
          <a href="#contact" class="px-3 py-2 text-white hover:text-red-600 transition">Contact</a>
          <a href="{{ url('/login') }}" 
            class="bg-gray-800 text-white px-5 py-2 rounded-full border-2 border-gray-800 
                    hover:bg-white hover:text-red-600 hover:border-red-600 transition duration-300">
            Login
          </a>
        </div>

        <!-- Mobile Hamburger (Right) -->
        <div class="flex items-center md:hidden">
          <button @click="open = !open" class="text-white hover:text-red-600 focus:outline-none">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path :class="{ 'hidden': open }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                    d="M4 6h16M4 12h16M4 18h16" />
              <path :class="{ 'hidden': !open }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                    d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

      </div>
    </div>

    <!-- Mobile Menu -->
    <div x-show="open" class="md:hidden px-4 pt-2 pb-4 space-y-2 bg-white">
      <a href="#home" class="block text-gray-800 hover:text-red-600 px-3 py-2">Home</a>
      <a href="#about" class="block text-gray-800 hover:text-red-600 px-3 py-2">About</a>
      <a href="#contact" class="block text-gray-800 hover:text-red-600 px-3 py-2">Contact</a>
      <a href="{{ url('/login') }}" 
        class="block bg-gray-800 text-white px-5 py-2 rounded-full border-2 border-gray-800 
                hover:bg-white hover:text-red-600 hover:border-red-600 transition duration-300">
        Login
      </a>
    </div>
  </nav>


  <!-- Home Section -->
  <section id="home" class="pt-24 min-h-screen bg-gray-100">
    <div class="p-6">
      <h1 class="text-4xl font-bold font-serif">Your journey to success begins with a</h1>
      <h1 class="text-4xl font-bold font-serif">
        <span class="text-red-600">Red Spartan</span> Scholarship
      </h1>
    </div>

    <!-- Carousel -->
    <div class="flex justify-center mt-8 mb-12 w-full px-4">
      <div class="flex flex-col items-center space-y-4 w-full max-w-xs">
        
        <div id="carousel" class="w-full aspect-square overflow-x-auto scroll-smooth snap-x snap-mandatory flex scrollbar-hide rounded-xl" style="box-shadow: 0 10px 20px rgba(220, 38, 38, 0.5), 0 6px 6px rgba(220, 38, 38, 0.4);">
          <img src="{{ asset('images/Batangas_State_Logo.png') }}" class="w-full h-full object-cover flex-shrink-0 snap-center" alt="Image 1">
          <img src="{{ asset('images/Batangas_State_Logo.png') }}" class="w-full h-full object-cover flex-shrink-0 snap-center" alt="Image 2">
          <img src="{{ asset('images/Batangas_State_Logo.png') }}" class="w-full h-full object-cover flex-shrink-0 snap-center" alt="Image 3">
        </div>

        <!-- Apply Button -->
        <a href="{{ url('student/form') }}" 
          class="bg-red-600 text-white font-semibold px-6 py-2 rounded-full shadow border-2 border-red-600 
                  hover:bg-white hover:text-red-600 hover:border-red-600 transition duration-300 w-full sm:w-auto text-center">
          Apply Now
        </a>

      </div>
    </div>
  </section>

  <!-- About Section -->
  <section id="about" class="pt-24 min-h-screen bg-white">
    <div class="p-6">
      <h2 class="text-4xl font-bold">About Us</h2>
      <p class="mt-4 text-gray-600">We are a premier university...</p>
    </div>
  </section>

  <!-- Contact Section -->
  <section id="contact" class="pt-24 min-h-screen bg-gray-50">
    <div class="p-6 max-w-4xl mx-auto">
      <h2 class="text-4xl font-bold text-red-700">Contact Us</h2>
      <p class="mt-2 text-gray-600">We’re here to assist you with your scholarship concerns.</p>

      <!-- Pablo Borbon Campus -->
      <div class="mt-10 bg-white border-2 border-red-200 rounded-xl shadow-lg p-8">
        <h3 class="text-2xl font-semibold text-red-700 border-b-2 border-red-100 pb-3">📍 Pablo Borbon Campus</h3>

        <div class="mt-6 space-y-5 text-left">
          <p class="flex items-start text-gray-700">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-red-600 mr-3 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c1.104 0 2-.896 2-2s-.896-2-2-2-2 .896-2 2 .896 2 2 2z" />
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 22s8-4.5 8-10c0-4.418-3.582-8-8-8S4 7.582 4 12c0 5.5 8 10 8 10z" />
            </svg>
            <span>
              <span class="font-semibold text-red-700">Address:</span>
              Scholarship Office - PB, Ground Floor, SSC 2 Bldg., Batangas State University - Pablo Borbon, Rizal Ave., Batangas City, Batangas
            </span>
          </p>

          <p class="flex items-center text-gray-700">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-red-600 mr-3 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12H8m0 0H5a2 2 0 01-2-2V8a2 2 0 012-2h14a2 2 0 012 2v2a2 2 0 01-2 2h-3m-4 0v6m0 0H8m4 0h4" />
            </svg>
            <span>
              <span class="font-semibold text-red-700">Email:</span>
              <a href="mailto:scholarship.pb@g.batstate-u.edu.ph" class="text-red-600 hover:text-red-800 underline decoration-red-300">
                scholarship.pb@g.batstate-u.edu.ph
              </a>
            </span>
          </p>

          <p class="flex items-center text-gray-700">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-red-600 mr-3 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.212 3.635a1 1 0 01-.272 1.045L8.414 10.586a16.001 16.001 0 006 6l2.222-2.222a1 1 0 011.045-.272l3.635 1.212a1 1 0 01.684.948V19a2 2 0 01-2 2h-1C9.163 21 3 14.837 3 7V5z" />
            </svg>
            <span>
              <span class="font-semibold text-red-700">Tel. no.:</span>
              (043) 980-0385 loc. 1834
            </span>
          </p>
        </div>
      </div>

      <!-- Alangilan Campus -->
      <div class="mt-8 bg-white border-2 border-red-200 rounded-xl shadow-lg p-8">
        <h3 class="text-2xl font-semibold text-red-700 border-b-2 border-red-100 pb-3">📍 Alangilan Campus</h3>

        <div class="mt-6 space-y-5 text-left">
          <p class="flex items-start text-gray-700">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-red-600 mr-3 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c1.104 0 2-.896 2-2s-.896-2-2-2-2 .896-2 2 .896 2 2 2z" />
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 22s8-4.5 8-10c0-4.418-3.582-8-8-8S4 7.582 4 12c0 5.5 8 10 8 10z" />
            </svg>
            <span>
              <span class="font-semibold text-red-700">Address:</span>
              Scholarship Office - Alangilan, Ground Floor, CIT Bldg., Batangas State University - Alangilan, Golden Country Homes., Brgy. Alangilan, Batangas City, Batangas
            </span>
          </p>

          <p class="flex items-center text-gray-700">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-red-600 mr-3 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12H8m0 0H5a2 2 0 01-2-2V8a2 2 0 012-2h14a2 2 0 012 2v2a2 2 0 01-2 2h-3m-4 0v6m0 0H8m4 0h4" />
            </svg>
            <span>
              <span class="font-semibold text-red-700">Email:</span>
              <a href="mailto:sfao.alangilan@g.batstate-u.edu.ph" class="text-red-600 hover:text-red-800 underline decoration-red-300">
                sfao.alangilan@g.batstate-u.edu.ph
              </a>
            </span>
          </p>

          <p class="flex items-center text-gray-700">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-red-600 mr-3 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.212 3.635a1 1 0 01-.272 1.045L8.414 10.586a16.001 16.001 0 006 6l2.222-2.222a1 1 0 011.045-.272l3.635 1.212a1 1 0 01.684.948V19a2 2 0 01-2 2h-1C9.163 21 3 14.837 3 7V5z" />
            </svg>
            <span>
              <span class="font-semibold text-red-700">Tel. no.:</span>
              (043) 774-2526 loc. 3113 / 3103
            </span>
          </p>
        </div>
      </div>
    </div>
  </section>


  <!-- JS -->
  <script src="{{ asset('js/script.js') }}"></script>
</body>
</html>
