@if (Session::has('success'))
<div class="p-4 mb-4 text-sm text-green-600 rounded-lg bg-green-100" role="alert">
    <span class="font-medium">Success!</span> {{ Session::get('success') }}
  </div>
@endif
@if (Session::has('error'))
    <div class="p-4 mb-4 text-sm text-red-600 rounded-lg bg-red-100" role="alert">
        <span class="font-medium">Error!</span> {{ Session::get('error') }}
    </div>>
@endif
