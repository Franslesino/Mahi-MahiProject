<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Register - UpGreenius</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            ug: '#005F56',
            ugDark: '#014a45',
          }
        }
      }
    }
  </script>
</head>
<body class="min-h-screen bg-[#f3f4f6]">

  <!-- Wrapper -->
  <div class="min-h-screen grid grid-cols-1 lg:grid-cols-2">

    <!-- Left: Brand -->
    <div class="hidden lg:flex items-center justify-center bg-white">
      <div class="max-w-md w-full px-10">
        <div class="flex flex-col items-center text-center">
          <img src="/logo.png" alt="UpGreenius" class="px-3 mb-1"/>
          <p class="mt-1 text-gray-600 text-lg leading-relaxed text-center">
            <span class="font-semibold">Upgrade your brain.</span> Stay greenius
          </p>
        </div>
      </div>
    </div>

    <!-- Right: Form -->
    <div class="flex items-center justify-center bg-[#f7f7f7] relative">

      <!-- Tombol Kembali -->
      <a href="{{ url('/') }}"
         class="fixed left-4 top-4 z-50 inline-flex items-center gap-2 text-gray-700 hover:text-ug transition bg-white/80 backdrop-blur px-3 py-2 rounded-md shadow">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        <span class="text-sm font-medium">Kembali</span>
      </a>

      <div class="w-full max-w-md px-6 py-10">
        <div class="text-center mb-8">
          <h1 class="text-2xl font-bold text-gray-900">Mulai Sekarang</h1>
          <p class="text-[13px] text-ug mt-1">Belajar apapun, kapanpun, di mana pun</p>
        </div>

        <!-- Form Register -->
        <form id="registerForm" action="{{ route('register.post') }}" method="POST" class="space-y-4">
          @csrf

          @if ($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
              <ul class="space-y-1">
                @foreach ($errors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
              </ul>
            </div>
          @endif

          <!-- Hidden full name -->
          <input type="hidden" name="name" id="full_name_hidden" />

          <!-- First Name -->
          <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Nama Depan</label>
            <input
              type="text"
              name="first_name"
              value="{{ old('first_name') }}"
              required
              class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-ug focus:border-ug outline-none text-sm"
              placeholder="masukan Nama Depan"/>
          </div>

          <!-- Last Name -->
          <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Nama Belakang</label>
            <input
              type="text"
              name="last_name"
              value="{{ old('last_name') }}"
              required
              class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-ug focus:border-ug outline-none text-sm"
              placeholder="masukan Nama Belakang"/>
          </div>

          <!-- Email -->
          <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Email</label>
            <input
              type="email"
              name="email"
              value="{{ old('email') }}"
              required
              class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-ug focus:border-ug outline-none text-sm"
              placeholder="masukan email"/>
          </div>

          <!-- DOB -->
          <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Tanggal Lahir</label>
            <div class="relative">
              <input
                type="date"
                name="dob"
                value="{{ old('dob') }}"
                required
                class="w-full rounded-md border border-gray-300 px-3 py-2 pr-9 focus:ring-ug focus:border-ug outline-none text-sm"/>
              <svg class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M8 7V3m8 4V3M3 11h18M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2h-1M6 5H5a2 2 0 00-2 2v12a2 2 0 002 2"/>
              </svg>
            </div>
          </div>

          <!-- Phone -->
          <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Nomor Telepon</label>
            <div class="flex">
              <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-600 text-sm">+62</span>
              <input
                type="tel"
                name="phone"
                value="{{ old('phone') }}"
                required
                class="w-full rounded-r-md border border-gray-300 px-3 py-2 focus:ring-ug focus:border-ug outline-none text-sm"
                placeholder="masukan nomor telepon"/>
            </div>
          </div>

          <!-- Password -->
          <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Kata Sandi</label>
            <div class="relative">
              <input
                type="password"
                name="password"
                id="password"
                required
                minlength="8"
                class="w-full rounded-md border border-gray-300 px-3 py-2 pr-12 focus:ring-ug focus:border-ug outline-none text-sm"
                placeholder="Minimal 8 karakter"/>

              <button type="button" data-eye="password"
                      class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
              </button>
            </div>
          </div>

          <!-- Confirm Password -->
          <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Konfirmasi Kata Sandi</label>
            <div class="relative">
              <input
                type="password"
                name="password_confirmation"
                id="password_confirmation"
                required
                minlength="8"
                class="w-full rounded-md border border-gray-300 px-3 py-2 pr-12 focus:ring-ug focus:border-ug outline-none text-sm"
                placeholder="Ulangi kata sandi"/>

              <button type="button" data-eye="password_confirmation"
                      class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
              </button>
            </div>
          </div>

          <!-- Gender -->
          <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Gender</label>
            <select
              name="gender"
              required
              class="w-full rounded-md border border-gray-300 px-3 py-2 bg-white focus:ring-ug focus:border-ug outline-none text-sm">
              <option value="" disabled {{ old('gender') ? '' : 'selected' }}>Pilih</option>
              <option value="Pria"  {{ old('gender')==='Pria' ? 'selected':'' }}>Pria</option>
              <option value="Wanita" {{ old('gender')==='Wanita' ? 'selected':'' }}>Wanita</option>
            </select>
          </div>

          <!-- Terms -->
          <div class="flex items-start pt-1">
            <input type="checkbox" required class="w-4 h-4 mt-0.5 text-ug border-gray-300 rounded focus:ring-ug">
            <label class="ml-2 text-xs text-gray-600">
              Saya setuju dengan <a href="#" class="text-ug hover:underline font-medium">Syarat & Ketentuan</a> dan
              <a href="#" class="text-ug hover:underline font-medium">Kebijakan Privasi</a>
            </label>
          </div>

          <!-- Submit -->
          <button
            type="submit"
            class="w-full rounded-md py-2.5 text-white text-sm font-semibold
                   bg-gradient-to-r from-ug to-ugDark hover:opacity-95 transition">
            Register
          </button>
        </form>

        <div class="mt-4 text-center">
          <p class="text-[12px] text-gray-600">
            Sudah Punya Akun?
            <a href="{{ route('login') }}" class="text-ug font-medium hover:underline">Masuk</a>
          </p>
        </div>
      </div>
    </div>
  </div>

  <script>
    // Gabungkan nama depan + belakang
    const form = document.getElementById('registerForm');
    form.addEventListener('submit', function () {
      const first = (form.querySelector('[name="first_name"]').value || '').trim();
      const last  = (form.querySelector('[name="last_name"]').value  || '').trim();
      document.getElementById('full_name_hidden').value = (first + ' ' + last).trim();
    });

    // Toggle password & confirm password
    document.querySelectorAll('[data-eye]').forEach(btn => {
      btn.addEventListener('click', () => {
        const input = document.getElementById(btn.getAttribute('data-eye'));
        if (!input) return;
        input.type = input.type === 'password' ? 'text' : 'password';
      });
    });
  </script>

</body>
</html>
