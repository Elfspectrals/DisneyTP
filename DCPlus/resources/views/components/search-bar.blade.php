<!-- Search bar component -->
<form method="GET" action="{{ route('search') }}" class="relative">
    <input
        type="text"
        name="search"
        placeholder="Rechercher un film..."
        value="{{ request('search') }}"
        class="w-48 lg:w-64 bg-[#0f0f0f] text-black placeholder-gray-400 rounded-full px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#0063e5]"
    >
</form>