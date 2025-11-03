@extends('layouts.app')

@section('title', 'Créer une tâche')

@section('content')
<div class="form-header">
    <h2>➕ Créer une nouvelle tâche</h2>
    <a href="{{ route('todos.index') }}" class="btn btn-secondary">
        ← Retour à la liste
    </a>
</div>

<form action="{{ route('todos.store') }}" method="POST" class="todo-form">
    @csrf

    <div class="form-group">
        <label for="title">Titre de la tâche *</label>
        <input type="text"
               id="title"
               name="title"
               value="{{ old('title') }}"
               class="form-control @error('title') is-invalid @enderror"
               placeholder="Ex: Acheter du lait"
               required>
        @error('title')
            <span class="error-message">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-group">
        <label for="description">Description (optionnelle)</label>
        <textarea id="description"
                  name="description"
                  rows="4"
                  class="form-control @error('description') is-invalid @enderror"
                  placeholder="Ajoutez des détails sur cette tâche...">{{ old('description') }}</textarea>
        @error('description')
            <span class="error-message">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn-primary">
            ✅ Créer la tâche
        </button>
        <a href="{{ route('todos.index') }}" class="btn btn-secondary">
            Annuler
        </a>
    </div>
</form>
@endsection

@section('styles')
<style>
    .form-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
        padding-bottom: 20px;
        border-bottom: 2px solid #e9ecef;
    }

    .form-header h2 {
        font-size: 1.8em;
        color: #333;
    }

    .btn-secondary {
        background: #6c757d;
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 5px;
        text-decoration: none;
        font-weight: 600;
    }

    .btn-secondary:hover {
        background: #5a6268;
    }

    .todo-form {
        background: #f8f9fa;
        padding: 30px;
        border-radius: 8px;
    }

    .form-group {
        margin-bottom: 25px;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: #333;
    }

    .form-control {
        width: 100%;
        padding: 12px;
        border: 2px solid #dee2e6;
        border-radius: 5px;
        font-size: 1em;
        transition: border-color 0.3s;
    }

    .form-control:focus {
        outline: none;
        border-color: #667eea;
    }

    .form-control.is-invalid {
        border-color: #dc3545;
    }

    .error-message {
        display: block;
        color: #dc3545;
        font-size: 0.9em;
        margin-top: 5px;
    }

    .form-actions {
        display: flex;
        gap: 15px;
        margin-top: 30px;
    }
</style>
@endsection
