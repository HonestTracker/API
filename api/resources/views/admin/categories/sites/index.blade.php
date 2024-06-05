@extends('layouts.admin')
@section('content')
    <div class="ml-2">
        <div class="flex justify-between items-center">
            <div class="text-3xl font-bold">
                Sites
            </div>
            <div>
                <a href="{{ route('admin.categories.sites.create', $category) }}"
                    class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5">
                    Add site
                </a>
            </div>
        </div>
        <ul class="flex flex-wrap text-sm font-medium text-center text-gray-500 border-b border-gray-200 mt-4 mx-3">
            <li class="me-2">
                <a href="{{ route('admin.categories.edit', $category) }}" aria-current="page"
                    class="inline-block p-4 rounded-t-lg hover:text-gray-600 hover:bg-gray-50">Edit</a>
            </li>
            <li class="me-2">
                <a href="{{ route('admin.categories.sites.index', $category) }}"
                    class="inline-block p-4 text-blue-600 bg-gray-100 rounded-t-lg active">Sites</a>
            </li>
        </ul>
        @if (count($category->sites) <= 0)
            <div class="font-semibold italic text-xl bg-white p-4 rounded-lg">
                No sites found!</div>
        @else
            <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                <table class="w-full text-sm text-left rtl:text-right text-gray-500">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3">
                                Site
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Last crawled
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Products
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
                        @foreach ($category->sites as $site)
                            <tr class="bg-white border-b">
                                <th scope="row" class="px-6 py-4 whitespace-nowrap">
                                    <div class="italic font-light text-gray-900">
                                        <span class="font-bold"> {{ $site->site_name }} </span>
                                    </div>
                                </th>
                                <td class="px-6 py-4">
                                    {{ $site->last_crawled }}
                                </td>
                                <td class="px-6 py-4">
                                    {{ count($site->products) }}
                                </td>
                                <td class="px-6 py-4">
                                    {{ $site->created_at }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex space-x-2 items-center justify-end">
                                        <a href="{{ route('admin.categories.sites.fetch_products', [$category, $site]) }}"
                                            class="font-medium text-blue-600 hover:underline">
                                            CRAWL
                                        </a>
                                        <a href="#" class="font-medium text-red-600 hover:underline">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                            </svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection
