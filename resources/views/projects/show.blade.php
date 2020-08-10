@extends('layouts.app')

@section('content')
<header class="flex items-center mb-3 py-4">
    <div class="flex justify-between items-end w-full">
        <p class="text-grey text-sm font-normal">
            <a href="/projects" class="text-grey text-sm font-normal no-underline hover:underline">My Projects</a> /
            {{ $project->title }}
            <a href="/projects/create" class="button ml-2">Add Task</a>
        </p>

        <a href="/projects/create" class="button">Invite to Project</a>
    </div>
</header>

<main>
    <div class="lg:flex -mx-3">
        <div class="lg:w-3/4 px-3 mb-6">
            <div class="mb-8">
                <h2 class="text-lg text-grey font-normal mb-3">Tasks</h2>

                {{-- tasks --}}
                @foreach($project->tasks as $task)
                <form action="{{ $task->path() }}" method="post">
                    @csrf
                    @method('PATCH')
                    <div class="card mb-3 flex">
                        <input type="text" name="body" id="" class="w-full {{ $task->completed ? 'text-grey' : '' }}"
                            value="{{ $task->body }}">
                        <input type="checkbox" name="completed" id="" value="1" onchange="this.form.submit()"
                            {{ $task->completed ? 'checked' : '' }}>
                    </div>
                </form>
                @endforeach
                <div class="card mb-3">
                    <form action="{{ route('tasks.store', $project) }}" method="post">
                        @csrf
                        <input type="text" name="body" id="" class="w-full" placeholder="Add a new task...">
                    </form>
                </div>
            </div>

            <div>
                <h2 class="text-lg text-grey font-normal mb-3">General Notes</h2>

                {{-- general notes --}}
                <textarea class="card w-full" style="min-height: 200px">Lorem ipsum.</textarea>
            </div>
        </div>

        <div class="lg:w-1/4 px-3 lg:py-8">
            @include ('projects._card')
        </div>
    </div>
</main>
@endsection
