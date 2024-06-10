@extends('layouts.main')
@section('content')
    <div class="flex flex-col items-center justify-center px-6 py-8 mx-auto lg:py-0">
        <div class="w-full bg-white rounded-lg shadow md:mt-0 sm:max-w-md xl:p-0 shadow-lg">
            <div class="p-6 space-y-4 md:space-y-6 sm:p-8">
                <h1 class="text-xl font-bold leading-tight tracking-tight text-gray-900 md:text-2xl">
                    Sign in to the admin panel
                </h1>
                <form class="space-y-4 md:space-y-6" method="POST" action="{{ route('login_web') }}">
                    @csrf
                    <div>
                        <label for="email" class="block mb-2 text-sm font-medium text-gray-900">Your email</label>
                        <input type="email" name="email" id="email"
                            class="bg-gray-50 border @if ($errors->has('email')) border-red-500 @else border-gray-300 @endif text-gray-900 sm:text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5"
                            placeholder="name@company.com" required="">
                        @error('email')
                            <div class="text-red-700 relative" role="alert">
                                <span class="block sm:inline">{{ $errors->first('email') }}</span>
                            </div>
                        @enderror
                    </div>
                    <div>
                        <label for="password" class="block mb-2 text-sm font-medium text-gray-900">Password</label>
                        <input type="password" name="password" id="password" placeholder="Password..."
                            class="bg-gray-50 border @if ($errors->has('password')) border-red-500 @else border-gray-300 @endif  text-gray-900 sm:text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5"
                            required="">
                            @error('password')
                            <div class="text-red-700 relative" role="alert">
                                <span class="block sm:inline">{{ $errors->first('password') }}</span>
                            </div>
                        @enderror
                    </div>
                    <button type="submit"
                        class="w-full text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:outline-none focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">Sign
                        in</button>
                </form>
            </div>
        </div>
    </div>
@endsection
