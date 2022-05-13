{{-- ログイン画面の View --}}

@extends('layouts.header')
@section('content')
  <div class="container">
    <div class="card">
      <div class="card-header">ログイン</div>
      <div class="card-body">
        @if (isset($errors) && $errors->any())
          <div class="card-text text-left alert alert-danger">
            <div class="mb-0">
              @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
              @endforeach
            </div>
          </div>
        @endif
        <form method="POST" action={{ route('auth.login') }}>
          @csrf
          <div class="card-text">
            <div class="mb-3">

              <label for="login_id">ログインID</label>
              <input type="text" class="form-control" id="login_id" name="login_id" value="{{ old('login_id')}}"
                     required
                     autofocus><br>
            </div>
          </div>
          <div class="card-text">
            <div class="mb-3">
              <label for="password">パスワード</label>
              <input type="password" class="form-control" name="password" required autocomplete="current-password">
            </div>
          </div>

          <button type="submit" class="btn btn-primary mb-3">Login</button>
        </form>
      </div>
    </div>
  </div>
@endsection