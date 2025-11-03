@extends('layouts.app')

@section('title', 'Liste des tâches')

@section('content')
<div class="todos-header">
    <h2>Mes Tâches</h2>
    <a href="{{ route('todos.create') }}" class="btn btn-primary">
        ➕ Nouvelle tâche
    </a>
</div>

@if($todos->isEmpty())
    <div class="empty-state">
        <p>🎉 Aucune tâche ! Vous êtes libre comme l'air.</p>
        <a href="{{ route('todos.create') }}" class="btn btn-primary">
            Créer votre première tâche
        </a>
    </div>
@else
    <div class="todos-list">
        @foreach($todos as $todo)
            <div class="todo-item {{ $todo->completed ? 'completed' : '' }}">
                <div class="todo-content">
                    <form action="{{ route('todos.toggle', $todo) }}" method="POST" class="toggle-form">
                        @csrf
                        @method('PATCH')
                        <input type="checkbox"
                               {{ $todo->completed ? 'checked' : '' }}
                               onchange="this.form.submit()">
                    </form>

                    <div class="todo-text">
                        <h3>{{ $todo->title }}</h3>
                        @if($todo->description)
                            <p>{{ $todo->description }}</p>
                        @endif
                        <small>Créée le {{ $todo->created_at->format('d/m/Y à H:i') }}</small>
                    </div>
                </div>

                <div class="todo-actions">
                    <a href="{{ route('todos.edit', $todo) }}" class="btn btn-edit">
                        ✏️ Modifier
                    </a>

                    <form action="{{ route('todos.destroy', $todo) }}"
                          method="POST"
                          onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette tâche ?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-delete">
                            🗑️ Supprimer
                        </button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>
@endif
@endsection

@section('styles')
<style>
    .todos-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
        padding-bottom: 20px;
        border-bottom: 2px solid #e9ecef;
    }

    .todos-list {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    .todo-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px;
        border: 1px solid #dee2e6;
        border-radius: 5px;
        background: #ffffff;
        transition: background 0.3s;
    }

    .todo-item.completed {
        background: #e2e3e5;
        text-decoration: line-through;
        color: #6c757d;
    }

    .todo-content {
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .todo-text h3 {
        margin: 0 0 5px 0;
    }

    .todo-actions {
        display: flex;
        gap: 10px;
    }
</style>
@endsection
