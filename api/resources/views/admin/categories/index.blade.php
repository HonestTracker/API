@extends('layouts.admin')
@section('content')
    <div class="ml-2">
        <div class="flex justify-between items-center">
            <div class="text-3xl font-bold">
                Categories
            </div>
            <div class="flex">
                <a href="{{ route('admin.categories.fetch_all_products') }}"
                    class="text-white bg-green-500 hover:bg-green-700 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 mr-3">
                    Fetch ALL products
                </a>
                <a href="{{ route('admin.categories.create') }}"
                    class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5">
                    Create category
                </a>
            </div>
        </div>
        @if (count($categories) <= 0)
            <div class="font-semibold italic text-xl bg-white p-4 rounded-lg mt-3">No categories found!</div>
            @else
            <div class="relative overflow-x-auto shadow-md sm:rounded-lg mt-3">
                <table class="w-full text-sm text-left rtl:text-right text-gray-500">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3">
                                Name
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Coupled sites
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Created at
                            </th>
                            <th scope="col" class="px-6 py-3">
                                <span class="sr-only">Edit</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($categories as $category)
                            <tr class="bg-white border-b">
                                <th scope="row" class="px-6 py-4 whitespace-nowrap">
                                    <div class="italic font-light text-gray-900">
                                        <span class="font-bold"> {{ $category->name }} </span>
                                    </div>
                                </th>
                                <td class="px-6 py-4">
                                    @foreach ($category->sites as $site)
                                        +{{ $site->site_name }}
                                    @endforeach
                                </td>
                                <td class="px-6 py-4">
                                    {{ $category->created_at }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex space-x-2 justify-end">
                                        <a href="{{ route('admin.categories.edit', $category) }}"
                                            class="font-medium text-blue-600 hover:underline">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                            </svg>
                                        </a>
                                        <form id="category-delete-form-{{ $category->id }}" action="{{ route('admin.categories.delete', $category) }}"
                                            method="POST" style="display: none;">
                                            @csrf
                                            @method('delete')
                                        </form>
                                        <button type="submit"
                                            onclick="if (confirm('Weet je zeker dat je {{ $category->name }} wilt verwijderen?')) { event.preventDefault(); document.getElementById('user-delete-form-{{ $user->id }}').submit(); }""
                                            class="font-medium text-red-600 hover:underline"><svg
                                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                            </svg></button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $categories->links() }}
            </div>
        @endif
    </div>
@endsection
