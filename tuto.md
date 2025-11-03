# TP Laravel - Création d'une Application Todo List

## Objectifs pédagogiques
- Comprendre le pattern MVC avec Laravel
- Maîtriser le système de templates Blade avec `@yield` et `@section`
- Manipuler Eloquent ORM pour les opérations CRUD
- Gérer les routes et les contrôleurs
- Valider les données utilisateur

## Durée estimée
3-4 heure

---

## Partie 1 : Installation et Configuration (20 min)

### 1.1 Créer le projet Laravel

```bash
composer create-project laravel/laravel todo-app
cd todo-app
php artisan serve
```

**Explication** : Ces commandes créent un nouveau projet Laravel et démarrent le serveur de développement sur `http://localhost:8000`.

### 1.2 Configurer la base de données

Ouvrez le fichier `.env` et configurez votre base de données :

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=todo_app
DB_USERNAME=root
DB_PASSWORD=
```

**Explication** : Le fichier `.env` contient les variables d'environnement. Laravel l'utilise pour configurer la connexion à la base de données.

Créez la base de données :
```bash
mysql -u root -p
CREATE DATABASE todo_app;
exit;
```

---

## Partie 2 : Création du Modèle et Migration (30 min)

### 2.1 Générer le modèle avec migration

```bash
php artisan make:model Todo -m
```

**Explication** : 
- `make:model Todo` crée le modèle `Todo` dans `app/Models/Todo.php`
- L'option `-m` génère automatiquement la migration associée dans `database/migrations/`

### 2.2 Définir la structure de la table

Ouvrez le fichier de migration dans `database/migrations/xxxx_create_todos_table.php` :

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('todos', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->boolean('completed')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('todos');
    }
};
```

**Explication** :
- `id()` : Clé primaire auto-incrémentée
- `string('title')` : Colonne VARCHAR pour le titre
- `text('description')->nullable()` : Description optionnelle
- `boolean('completed')->default(false)` : Statut de la tâche (false par défaut)
- `timestamps()` : Ajoute `created_at` et `updated_at` automatiquement

### 2.3 Exécuter la migration

```bash
php artisan migrate
```

**Explication** : Cette commande exécute toutes les migrations en attente et crée les tables dans la base de données.

### 2.4 Configurer le modèle

Ouvrez `app/Models/Todo.php` :

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Todo extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'completed'
    ];

    protected $casts = [
        'completed' => 'boolean',
    ];
}
```

**Explication** :
- `$fillable` : Liste les champs assignables en masse (protection contre l'injection de masse)
- `$casts` : Convertit automatiquement `completed` en booléen

---

## Partie 3 : Création du Layout Principal (20 min)

### 3.1 Créer le layout de base

Créez le fichier `resources/views/layouts/app.blade.php` :

```php
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Todo App')</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            overflow: hidden;
        }
        
        header {
            background: #667eea;
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        header h1 {
            font-size: 2.5em;
            margin-bottom: 10px;
        }
        
        .content {
            padding: 30px;
        }
        
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            font-weight: 500;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        footer {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            color: #6c757d;
            border-top: 1px solid #dee2e6;
        }
    </style>
    @yield('styles')
</head>
<body>
    <div class="container">
        <header>
            <h1>📝 Ma Todo List</h1>
            <p>Organisez vos tâches efficacement</p>
        </header>
        
        <div class="content">
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
            
            @if($errors->any())
                <div class="alert alert-error">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            @yield('content')
        </div>
        
        <footer>
            <p>&copy; 2024 Todo App - Réalisé avec Laravel</p>
        </footer>
    </div>
    
    @yield('scripts')
</body>
</html>
```

**Explication détaillée du système `@yield` et `@section`** :

1. **`@yield('content')`** : C'est un emplacement réservé (placeholder) dans le layout. Les vues enfants vont "remplir" cet emplacement avec leur contenu spécifique.

2. **`@yield('title', 'Todo App')`** : Même principe, mais avec une valeur par défaut. Si la vue enfant ne définit pas de titre, "Todo App" sera utilisé.

3. **`@yield('styles')` et `@yield('scripts')`** : Permettent aux vues enfants d'ajouter du CSS ou du JavaScript spécifique.

4. **Comment ça fonctionne** :
   - Le layout est le "moule" de base
   - Chaque vue enfant va "étendre" ce layout avec `@extends('layouts.app')`
   - Les vues enfants remplissent les zones `@yield` avec `@section` et `@endsection`

---

## Partie 4 : Création du Contrôleur (30 min)

### 4.1 Générer le contrôleur

```bash
php artisan make:controller TodoController
```

### 4.2 Implémenter les méthodes CRUD

Ouvrez `app/Http/Controllers/TodoController.php` :

```php
<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use Illuminate\Http\Request;

class TodoController extends Controller
{
    /**
     * Affiche la liste de toutes les tâches
     */
    public function index()
    {
        $todos = Todo::orderBy('created_at', 'desc')->get();
        return view('todos.index', compact('todos'));
    }

    /**
     * Affiche le formulaire de création
     */
    public function create()
    {
        return view('todos.create');
    }

    /**
     * Enregistre une nouvelle tâche
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'description' => 'nullable|max:1000',
        ], [
            'title.required' => 'Le titre est obligatoire',
            'title.max' => 'Le titre ne peut pas dépasser 255 caractères',
        ]);

        Todo::create($validated);

        return redirect()->route('todos.index')
            ->with('success', 'Tâche créée avec succès !');
    }

    /**
     * Affiche le formulaire d'édition
     */
    public function edit(Todo $todo)
    {
        return view('todos.edit', compact('todo'));
    }

    /**
     * Met à jour une tâche existante
     */
    public function update(Request $request, Todo $todo)
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'description' => 'nullable|max:1000',
        ]);

        $todo->update($validated);

        return redirect()->route('todos.index')
            ->with('success', 'Tâche mise à jour avec succès !');
    }

    /**
     * Supprime une tâche
     */
    public function destroy(Todo $todo)
    {
        $todo->delete();

        return redirect()->route('todos.index')
            ->with('success', 'Tâche supprimée avec succès !');
    }

    /**
     * Bascule le statut de complétion d'une tâche
     */
    public function toggle(Todo $todo)
    {
        $todo->update([
            'completed' => !$todo->completed
        ]);

        return redirect()->route('todos.index')
            ->with('success', 'Statut mis à jour !');
    }
}
```

**Explication des concepts** :

1. **Route Model Binding** : `Todo $todo` dans les paramètres injecte automatiquement le modèle correspondant à l'ID dans l'URL.

2. **Validation** : `$request->validate()` valide les données avant traitement. Si la validation échoue, Laravel redirige automatiquement avec les erreurs.

3. **Messages flash** : `with('success', '...')` stocke un message temporaire dans la session, affiché une seule fois.

4. **compact()** : Raccourci pour passer des variables aux vues. `compact('todos')` équivaut à `['todos' => $todos]`.

---

## Partie 5 : Définition des Routes (15 min)

Ouvrez `routes/web.php` :

```php
<?php

use App\Http\Controllers\TodoController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('todos.index');
});

// Routes resourceful (CRUD automatique)
Route::resource('todos', TodoController::class);

// Route personnalisée pour basculer le statut
Route::patch('todos/{todo}/toggle', [TodoController::class, 'toggle'])
    ->name('todos.toggle');
```

**Explication** :

1. **`Route::resource()`** : Crée automatiquement 7 routes RESTful :
   - GET `/todos` → index (liste)
   - GET `/todos/create` → create (formulaire création)
   - POST `/todos` → store (enregistrement)
   - GET `/todos/{id}` → show (affichage)
   - GET `/todos/{id}/edit` → edit (formulaire édition)
   - PUT/PATCH `/todos/{id}` → update (mise à jour)
   - DELETE `/todos/{id}` → destroy (suppression)

2. **Route nommée** : `->name('todos.toggle')` permet d'utiliser `route('todos.toggle', $todo)` dans les vues.

Vérifiez vos routes :
```bash
php artisan route:list
```

---

## Partie 6 : Création des Vues (45 min)

### 6.1 Vue Index (Liste des tâches)

Créez `resources/views/todos/index.blade.php` :

```php
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
    
    .todos-header h2 {
        font-size: 2em;
        color: #333;
    }
    
    .btn {
        padding: 12px 24px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-weight: 600;
        text-decoration: none;
        display: inline-block;
        transition: all 0.3s;
    }
    
    .btn-primary {
        background: #667eea;
        color: white;
    }
    
    .btn-primary:hover {
        background: #5568d3;
        transform: translateY(-2px);
    }
    
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #6c757d;
    }
    
    .empty-state p {
        font-size: 1.5em;
        margin-bottom: 20px;
    }
    
    .todos-list {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }
    
    .todo-item {
        background: #f8f9fa;
        padding: 20px;
        border-radius: 8px;
        border-left: 4px solid #667eea;
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: all 0.3s;
    }
    
    .todo-item:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        transform: translateX(5px);
    }
    
    .todo-item.completed {
        opacity: 0.6;
        border-left-color: #28a745;
    }
    
    .todo-content {
        display: flex;
        align-items: flex-start;
        gap: 15px;
        flex: 1;
    }
    
    .toggle-form input[type="checkbox"] {
        width: 20px;
        height: 20px;
        cursor: pointer;
        margin-top: 5px;
    }
    
    .todo-text h3 {
        font-size: 1.2em;
        margin-bottom: 5px;
        color: #333;
    }
    
    .todo-item.completed .todo-text h3 {
        text-decoration: line-through;
    }
    
    .todo-text p {
        color: #6c757d;
        margin-bottom: 5px;
    }
    
    .todo-text small {
        color: #adb5bd;
        font-size: 0.85em;
    }
    
    .todo-actions {
        display: flex;
        gap: 10px;
    }
    
    .btn-edit {
        background: #ffc107;
        color: #333;
        font-size: 0.9em;
        padding: 8px 16px;
    }
    
    .btn-edit:hover {
        background: #e0a800;
    }
    
    .btn-delete {
        background: #dc3545;
        color: white;
        font-size: 0.9em;
        padding: 8px 16px;
    }
    
    .btn-delete:hover {
        background: #c82333;
    }
</style>
@endsection
```

**Explication de la structure Blade** :

1. **`@extends('layouts.app')`** : Indique que cette vue hérite du layout `app.blade.php`.

2. **`@section('title', 'Liste des tâches')`** : Remplit le `@yield('title')` du layout.

3. **`@section('content')` ... `@endsection`** : Définit le contenu qui remplacera `@yield('content')` dans le layout.

4. **`{{ }}`** : Affiche du contenu en l'échappant (sécurité XSS).

5. **`@if`, `@foreach`, `@method`, `@csrf`** : Directives Blade pour la logique, boucles, et sécurité.

### 6.2 Vue Create (Formulaire de création)

Créez `resources/views/todos/create.blade.php` :

```php
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
```

**Explication des fonctionnalités du formulaire** :

1. **`@csrf`** : Token de protection CSRF (obligatoire pour tous les formulaires POST/PUT/DELETE).

2. **`old('title')`** : Récupère la valeur précédente en cas d'erreur de validation (pour ne pas perdre les données saisies).

3. **`@error('title')`** : Affiche le message d'erreur si le champ `title` a échoué à la validation.

4. **`route('todos.store')`** : Génère l'URL de la route nommée `todos.store`.

### 6.3 Vue Edit (Formulaire d'édition)

Créez `resources/views/todos/edit.blade.php` :

```php
@extends('layouts.app')

@section('title', 'Modifier une tâche')

@section('content')
<div class="form-header">
    <h2>✏️ Modifier la tâche</h2>
    <a href="{{ route('todos.index') }}" class="btn btn-secondary">
        ← Retour à la liste
    </a>
</div>

<form action="{{ route('todos.update', $todo) }}" method="POST" class="todo-form">
    @csrf
    @method('PUT')
    
    <div class="form-group">
        <label for="title">Titre de la tâche *</label>
        <input type="text" 
               id="title" 
               name="title" 
               value="{{ old('title', $todo->title) }}"
               class="form-control @error('title') is-invalid @enderror"
               required>
        @error('title')
            <span class="error-message">{{ $message }}</span>
        @enderror
    </div>
    
    <div class="form-group">
        <label for="description">Description</label>
        <textarea id="description" 
                  name="description" 
                  rows="4"
                  class="form-control @error('description') is-invalid @enderror">{{ old('description', $todo->description) }}</textarea>
        @error('description')
            <span class="error-message">{{ $message }}</span>
        @enderror
    </div>
    
    <div class="form-group">
        <label>
            <input type="checkbox" 
                   name="completed" 
                   {{ old('completed', $todo->completed) ? 'checked' : '' }}>
            Marquer comme terminée
        </label>
    </div>
    
    <div class="form-actions">
        <button type="submit" class="btn btn-primary">
            💾 Enregistrer les modifications
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
    
    .btn {
        padding: 12px 24px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-weight: 600;
        text-decoration: none;
        display: inline-block;
        transition: all 0.3s;
    }
    
    .btn-primary {
        background: #667eea;
        color: white;
    }
    
    .btn-primary:hover {
        background: #5568d3;
    }
</style>
@endsection
```

**Explication** :

1. **`@method('PUT')`** : Les formulaires HTML ne supportent que GET et POST. Cette directive simule une requête PUT pour Laravel.

2. **`old('title', $todo->title)`** : Priorise `old()` (en cas d'erreur), sinon affiche la valeur actuelle du modèle.

---

## Partie 7 : Tests et Améliorations (30 min)

### 7.1 Tester l'application

1. Accédez à `http://localhost:8000`
2. Créez plusieurs tâches
3. Testez la validation (essayez de soumettre un formulaire vide)
4. Modifiez une tâche
5. Marquez des tâches comme terminées
6. Supprimez des tâches

### 7.2 Améliorations suggérées

**Exercice 1** : Ajouter un filtre pour afficher uniquement les tâches terminées ou en cours.

**Exercice 2** : Ajouter une date d'échéance aux tâches.

**Exercice 3** : Créer des catégories de tâches.

**Exercice 4** : Ajouter la pagination si plus de 10
