@extends('layouts.admin')
@section('content')
    <div class="ml-2">
        <div class="flex justify-between items-center">
            <div class="text-3xl font-bold">
                Adding site...
            </div>
            <div>
                <a href="{{ route('admin.categories.sites.index', $category) }}"
                    class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5">
                    Back
                </a>
            </div>
        </div>
        <div class="bg-white mt-4 pt-1 rounded-xl shadow-xl">
            <form action="{{route('admin.categories.sites.store', $category)}}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="ms-auto me-auto items-center w-8/12 mt-2">
                    <label for="url" class="block mb-2 text-sm font-medium text-gray-900">URL (to category page)</label>
                    <input value="{{ old('url') }}" type="text" name="url" id="url"
                        class="bg-gray-50 border @if ($errors->has('url')) border-red-500 @else border-gray-300 @endif text-gray-900 text-sm rounded-lg w-full">
                    @error('url')
                        <div class="text-red-700 relative" role="alert">
                            <span class="block sm:inline">{{ $errors->first('url') }}</span>
                        </div>
                    @enderror
                </div>
                <div class="ms-auto me-auto items-center w-8/12 mt-2">
                    <label for="site_name"
                        class="block mb-2 text-sm font-medium text-gray-900">Site (FILL CORRECTLY VERY IMPORTANT)</label>
                    <select id="site_name" name="site_name"
                        class="bg-gray-50 border @if ($errors->has('site_name')) border-red-500 @else border-gray-300 @endif text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                        <option selected value="">Select site</option>
                        <option value="amazon.nl">Amazon</option>
                        <option value="bol.com">Bol</option>
                        <option value="coolblue.nl">Coolblue</option>
                    </select>
                    @error('site_name')
                        <div class="text-red-700 relative" role="alert">
                            <span class="block sm:inline">{{ $errors->first('site_name') }}</span>
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
