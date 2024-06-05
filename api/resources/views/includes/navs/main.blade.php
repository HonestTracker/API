<nav class="bg-blue-600 border-gray-200">
    <div class="container grid grid-cols-5 mx-auto py-4">
        <a href="{{ route('admin.index') }}" class="flex col-span-4 space-x-3">
            <span class="text-2xl font-semibold text-white">Admin Panel</span>
        </a>
        @guest
            <ul class="flex">
                <a class="nav-link" href="{{ route('login') }}">
                    <li class="nav-item p-2 bg-gray-100 rounded-lg hover:bg-gray-300 hover:cursor-pointer">
                        Log in
                    </li>
                </a>
            </ul>
        @endguest
        @auth
            <div class="flex md:order-2 space-x-4 justify-end">
                <a href="{{ route('admin.index') }}" class="block py-2 px-3 text-xl text-gray-900 rounded-lg hover:text-white bg-gray-50 hover:bg-gray-300"
                    aria-current="page">Admin</a>
                <a class="block py-2 px-3 text-xl text-gray-900 rounded-lg hover:text-white bg-gray-50 hover:bg-gray-300" href="{{ route('logout') }}"
                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Log out</a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </div>
        @endauth
    </div>
</nav>
