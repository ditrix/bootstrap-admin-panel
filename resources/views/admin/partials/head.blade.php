<meta charset="utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
<meta name="description" content="" />
<meta name="author" content="" />
<title>@yield('title', 'SB Admin')</title>
<meta name="csrf-token" content="{{ csrf_token() }}">
@vite('resources/themes/admin/assets/css/app.scss')
<script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
@stack('head')
