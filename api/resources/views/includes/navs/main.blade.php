<nav class="bg-[#20c1aa] border-gray-200">
    <div class="container grid grid-cols-5 mx-auto py-2">
        <a href="{{ route('admin.index') }}" class="flex col-span-3 items-center space-x-3">
            <img src="{{ asset('img/logo.svg') }}" class="h-16 w-16" alt="Logo">
            <span class="text-2xl font-semibold text-white">Admin Panel</span>
        </a>

        @auth
            <div class="flex col-span-2 justify-end items-center space-x-4 md:order-2">
                <a href="{{ route('admin.index') }}" class="block bg-opacity-40 py-2 px-3 text-xl text-white rounded-lg hover:text-white bg-gray-50 hover:bg-gray-300"
                    aria-current="page">Admin</a>
                <a class="block bg-opacity-40 py-2 px-3 text-xl text-white rounded-lg hover:text-white bg-gray-50 hover:bg-gray-300" href="{{ route('logout') }}"
                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Log out</a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </div>
        @endauth
    </div>
</nav>

