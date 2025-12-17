<footer class="bg-[#005F56] text-white mt-20">
    <div class="max-w-7xl mx-auto px-6 lg:px-8 py-14 grid grid-cols-2 md:grid-cols-4 gap-10 text-sm">

        <!-- Tentang -->
        <div>
            <div class="mb-4">
                <a href="{{ route('home') }}" class="text-2xl font-bold hover:opacity-80 transition">
                    <span class="text-white">UpGreenius</span>
                </a>
            </div>
            <p class="text-white/80 leading-relaxed">
                Platform pembelajaran digital dari Politeknik Negeri Jakarta.
            </p>
        </div>

        <!-- Jelajahi -->
        <div>
            <h4 class="font-semibold text-white mb-4">Jelajahi</h4>
            <ul class="space-y-2 text-white/80">
                <li><a href="{{ route('courses.all') }}" class="hover:text-white transition">Kursus</a></li>
                <li><a href="{{ route('instructors.index') }}" class="hover:text-white transition">Instruktur</a>
                </li>
                <li><a href="{{ route('home') }}#categories" class="hover:text-white transition">Kategori</a></li>
                <li><a href="{{ route('home') }}#about" class="hover:text-white transition">Tentang Kami</a></li>
            </ul>
        </div>

        <!-- Bantuan -->
        <div>
            <h4 class="font-semibold text-white mb-4">Bantuan</h4>
            <ul class="space-y-2 text-white/80">
                <li><a href="{{ route('home') }}#faq" class="hover:text-white transition">Pusat Bantuan</a></li>
                <li><a href="{{ route('home') }}#privacy" class="hover:text-white transition">Kebijakan Privasi</a>
                </li>
                <li><a href="{{ route('home') }}#terms" class="hover:text-white transition">Syarat & Ketentuan</a>
                </li>
                <li><a href="mailto:info@pnj.ac.id" class="hover:text-white transition">Kontak Kami</a></li>
            </ul>
        </div>

        <!-- Hubungi Kami -->
        <div>
            <h4 class="font-semibold text-white mb-4">Hubungi Kami</h4>
            <ul class="space-y-2 text-white/80">
                <li>Jl. Prof. DR. G.A. Siwabessy, Depok</li>
                <li>info@pnj.ac.id</li>
                <li>(021) 7270036</li>
            </ul>
        </div>
    </div>

    <div class="bg-[#004940] text-white/70 text-xs text-center py-4">
        © 2025 UpGreenius — Bersama membangun kompetensi digital bangsa.
    </div>
</footer>