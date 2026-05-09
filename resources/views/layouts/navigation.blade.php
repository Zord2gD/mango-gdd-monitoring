<nav class="bg-slate-800 px-6 py-4 flex items-center justify-between">
    <!-- LEFT: TITLE -->
    <div>
        <h1 class="text-white text-lg font-semibold">Dashboard</h1>
        <p class="text-gray-400 text-sm">Mango Supply Chain</p>
    </div>

    <!-- RIGHT: USER -->
    <div class="flex items-center gap-6 ml-auto">

        <!-- Nama -->
        <span class="text-gray-300 text-sm">
            {{ Auth::user()->name }}
        </span>

        <!-- Logout -->
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm transition-colors">
                Log Out
            </button>
        </form>

    </div>
</nav>
