@extends('layouts.admin')
@section('content')
    <div class="ml-2">
        <div class="flex justify-between items-center">
            <div class="text-3xl font-bold">
                Creating category...
            </div>
            <div>
                <a href="{{ route('admin.categories.index') }}"
                    class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5">
                    Back
                </a>
            </div>
        </div>
        <div class="bg-white mt-4 pt-1 rounded-xl shadow-xl">
            <form action="{{route('admin.categories.store')}}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="ms-auto me-auto items-center w-8/12 mt-2">
                    <label for="name" class="block mb-2 text-sm font-medium text-gray-900">Name</label>
                    <input value="{{ old('name') }}" type="text" name="name" id="name"
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
