@extends('layouts.admin')
@section('content')
    <div class="ml-2">
        <div class="flex justify-between items-center">
            <div class="text-3xl font-bold">
                Editing category...
            </div>
            <div>
                <a href="{{ route('admin.categories.index') }}"
                    class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5">
                    Back
                </a>
            </div>
        </div>
        <ul class="flex flex-wrap text-sm font-medium text-center text-gray-500 border-b border-gray-200 mt-4 mx-3">
            <li class="me-2">
                <a href="{{route('admin.categories.edit', $category)}}" aria-current="page" class="inline-block p-4 text-blue-600 bg-gray-100 rounded-t-lg active">Edit</a>
            </li>
            <li class="me-2">
                <a href="{{route('admin.categories.sites.index', $category)}}" class="inline-block p-4 rounded-t-lg hover:text-gray-600 hover:bg-gray-50">Sites</a>
            </li>
        </ul>
        <div class="bg-white pt-1 rounded-xl shadow-xl">
            <form action="{{route('admin.categories.update', $category)}}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="ms-auto me-auto items-center w-8/12 mt-2">
                    <label for="name" class="block mb-2 text-sm font-medium text-gray-900">Name</label>
                    <input value="{{ old('name', $category->name) }}" type="text" name="name" id="name"
                        class="bg-gray-50 border @if ($errors->has('name')) border-red-500 @else border-gray-300 @endif text-gray-900 text-sm rounded-lg w-full">
                    @error('name')
                        <div class="text-red-700 relative" role="alert">
                            <span class="block sm:inline">{{ $errors->first('name') }}</span>
                        </div>
                    @enderror
                </div>
                <div class="flex justify-end ms-auto me-auto space-x-2 border-gray-200 rounded-b w-8/12 mt-3">
                    <button type="submit"
                        class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 mb-2">Save</button>
                </div>
            </form>
        </div>
    </div>
@endsection
