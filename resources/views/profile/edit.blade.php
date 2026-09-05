@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <div class="row g-4">
            <div class="col-12 col-lg-6">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <h4 class="mb-3">Profil</h4>

                        <form method="POST" action="{{ route('profile.update') }}">
                            @csrf
                            @method('PATCH')

                            <div class="mb-3">
                                <label for="name" class="form-label">Nom</label>
                                <input id="name" name="name" type="text" class="form-control" value="{{ old('name', $user->name) }}" required>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input id="email" name="email" type="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                            </div>

                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            @if (session('status') === 'profile-updated')
                                <div class="alert alert-success">Profil mis à jour.</div>
                            @endif

                            <button type="submit" class="btn btn-primary">Enregistrer</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-6">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <h4 class="mb-3">Mot de passe</h4>

                        <form method="POST" action="{{ route('password.update') }}">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label for="current_password" class="form-label">Mot de passe actuel</label>
                                <input id="current_password" name="current_password" type="password" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">Nouveau mot de passe</label>
                                <input id="password" name="password" type="password" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label">Confirmation</label>
                                <input id="password_confirmation" name="password_confirmation" type="password" class="form-control" required>
                            </div>

                            @if (session('status') === 'password-updated')
                                <div class="alert alert-success">Mot de passe mis à jour.</div>
                            @endif

                            <button type="submit" class="btn btn-primary">Mettre à jour</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
