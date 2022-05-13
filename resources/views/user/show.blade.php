@extends('layouts.header')
@section('content')

  <div class="container">
    <h1>{{ $user->name }}</h1>
    @foreach($user->exerciseTypes as $exercise_type)
    <div class="card">
      <div class="card-title">
        {{ $exercise_type->name }}
      </div>

      <div class="card-text">
        1RM {{ $exercise_type->max_weight }}Kg
      </div>

    </div>
    @endforeach
  </div>
@endsection
