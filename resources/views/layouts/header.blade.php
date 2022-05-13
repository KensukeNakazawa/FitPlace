{{-- ヘッダーのView --}}

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

  <head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" id="viewport" content="width=device-width,initial-scale=1">

    <title>FitPlace管理画面</title>

    {{-- Jquery Plugin --}}
    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>

    {{-- 郵便番号から住所を取得するためのプラグイン --}}
    <script src="https://ajaxzip3.github.io/ajaxzip3.js" charset="UTF-8"></script>

    {{-- bootstrap --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-ygbV9kiqUc6oa4msXn9868pTtWMgiQaeYH7/t7LECLbyPA2x65Kgf80OJFdroafW" crossorigin="anonymous"></script>

    {{-- fontawesome --}}
    <link href="https://use.fontawesome.com/releases/v5.13.0/css/all.css" rel="stylesheet">



    {{-- Script --}}
  {{--  <script src="{{ asset('/js/original.js') }}"></script>--}}
  {{--  <script src="{{ mix('/js/app.js') }}"></script>--}}

  {{--  --}}{{-- Styles --}}
{{--    <link rel="stylesheet" href="/css/style.css">--}}
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">

  </head>

  <body>
  <nav class="navbar navbar-dark bg-dark">
    <div class="container-fluid">
      <a class="navbar-brand" href={{ route('home') }}>FitPlace</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarText" aria-controls="navbarText" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarText">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
          <li class="nav-item">
            <a class="nav-link" href={{ route('home') }}>Home</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href={{ route('exercise.index') }}>トレーニング一覧</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href={{ route('admin.index') }}>管理者一覧</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href={{ route('body_part.index') }}>部位一覧</a>
          </li>
          <li class="nav-item">
            <form id="logout-form" method="POST" action={{ route('auth.logout') }}>
              @csrf
              <a class="nav-link" href="{{ route('auth.logout') }}" onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                Logout
              </a>
            </form>
          </li>
        </ul>
      </div>
    </div>
  </nav>
  @yield('content')
  </body>

</html>
