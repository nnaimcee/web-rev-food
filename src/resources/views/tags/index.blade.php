@extends('layouts.app')

@section('title', 'Hashtags')

@section('content')
<div class="container py-4">
  <h1 class="mb-4">Hashtags</h1>
  <div class="list-group">
    @foreach($tags as $t)
      <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" href="{{ route('home.index', ['tag' => $t->tag]) }}">
        <span>#{{ $t->tag }}</span>
        <span class="badge bg-primary rounded-pill">{{ $t->total }}</span>
      </a>
    @endforeach
  </div>

  <div class="mt-3">
    {{ $tags->links('pagination::bootstrap-5') }}
  </div>
</div>
@endsection

