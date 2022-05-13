@extends('layouts.header')
@section('content')
  <div class="container">
    <h1>部位名編集</h1>
    <div class="card">
      <form method="POST" action={{ route('body_part.update', ['body_part_id' => $body_part->id]) }}>
        @csrf
        @method('PUT')

        <div class="card-text">
          <div class="mb-3">
            <label for="name">部位名</label>
            <input id="name" name="name" value="{{ $body_part->name }}" type="text">
          </div>
        </div>

        <button type="submit" class="btn btn-primary mb-3">追加</button>
      </form>

    </div>
  </div>
@endsection