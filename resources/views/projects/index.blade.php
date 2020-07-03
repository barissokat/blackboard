@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Projects') }}</div>

                <div class="card-body">
                    <div class="links">
                        @forelse ($projects as $project)
                        <a href="{{ $project->path() }}">{{ $project->title }}</a>
                        @if (!$loop->last)
                        <hr>
                        @endif
                        @empty
                        There are no projects at this time.
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
