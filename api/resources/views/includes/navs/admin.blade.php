<div class="bg-gray-100 mr-4 rounded-lg border-2 shadow">
    <ul>
        <a href="{{ route('admin.users.index') }}" class="block">
            <li class="p-4 hover:bg-gray-200 cursor-pointer">
                Users
            </li>
        </a>
        <a href="{{ route('admin.categories.index') }}" class="block">
            <li class="p-4 hover:bg-gray-200 cursor-pointer">
                Categories
            </li>
        </a>
        <a href="{{ route('admin.products.index') }}" class="block">
            <li class="p-4 hover:bg-gray-200 cursor-pointer">
                Products
            </li>
        </a>
        <a href="#" class="block">
            <li class="p-4 hover:bg-gray-200 cursor-pointer">
                Comments
            </li>
        </a>
    </ul>
</div>