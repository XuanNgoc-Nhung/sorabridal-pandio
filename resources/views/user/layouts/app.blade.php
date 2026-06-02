<!DOCTYPE html>
<html lang="vi">
@include('user.layouts.partials.head')
<body class="@yield('body_class')">
@include('user.layouts.partials.navbar')
@yield('content')
@include('user.layouts.partials.footer')
@include('user.layouts.partials.scripts')
</body>
</html>
