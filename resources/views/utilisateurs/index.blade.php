@extends('layouts.app')

@section('content')
<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold text-primary mb-0">
            <i class="bi bi-people"></i>
            Gestion des utilisateurs
        </h3>

        <button type="button" class="btn btn-primary rounded-pill shadow-sm" onclick="openUserModal()">
            <i class="bi bi-person-plus"></i>
            Nouvel utilisateur
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm rounded-3">
            <i class="bi bi-check-circle"></i>
            {{ session('success') }}

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger shadow-sm rounded-3">
            <strong>Le formulaire contient des erreurs :</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow border-0 rounded-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr class="text-secondary">
                            <th>Email</th>
                            <th>Login</th>
                            <th>Nom</th>
                            <th>Droit</th>
                            <th class="text-end" style="width: 200px;">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td>{{ $user->email }}</td>
                            <td class="fw-semibold">{{ $user->login }}</td>
                            <td>{{ $user->name ?: 'Non renseigné' }}</td>
                            <td>
                                @php
                                    $badgeClass = match($user->droit) {
                                        'ADMIN' => 'bg-danger',
                                        'EMPLOYE' => 'bg-primary',
                                        'CLIENT' => 'bg-success',
                                        default => 'bg-secondary',
                                    };
                                @endphp
                                <span class="badge {{ $badgeClass }}">{{ $user->droit }}</span>
                            </td>
                            <td class="text-end">
                                <button class="btn btn-sm btn-outline-primary rounded-pill edit-user"
                                    data-id="{{ $user->id }}"
                                    data-name="{{ $user->name }}"
                                    data-email="{{ $user->email }}"
                                    data-login="{{ $user->login }}"
                                    data-droit="{{ $user->droit }}"
                                >
                                    Modifier
                                </button>

                                <form action="{{ route('utilisateurs.destroy', $user) }}" method="POST" class="d-inline" onsubmit="return confirm('Supprimer cet utilisateur ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                Aucun utilisateur enregistré.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>

{{-- Modale : ajout et modification --}}
<div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="userModalTitle" aria-hidden="true" >
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow border-0 rounded-4">
            <form method="POST" id="userForm">
                @csrf
                <input type="hidden" name="_method" id="userMethod" value="POST">

                <div class="modal-header bg-light">
                    <h5 class="modal-title fw-bold text-primary" id="userModalTitle">
                        <i class="bi bi-person"></i>
                        Ajouter un utilisateur
                    </h5>

                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" name="name" id="user_name" class="form-control" placeholder="Nom" maxlength="255">
                                <label for="user_name">Nom</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="email" name="email" id="user_email" class="form-control" placeholder="Email" maxlength="255" required>
                                <label for="user_email"> Email * </label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" name="login" id="user_login" class="form-control" placeholder="Login" maxlength="100" required>
                                <label for="user_login">Login *</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <select name="droit" id="user_droit" class="form-select" required>
                                    <option value="CLIENT">Client</option>
                                    <option value="EMPLOYE">Employé</option>
                                    <option value="ADMIN">Administrateur</option>
                                </select>
                                <label for="user_droit"> Droit *</label>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="password" name="password" id="user_password" class="form-control" placeholder="Mot de passe" minlength="6">
                                <label for="user_password">Mot de passe</label>
                            </div>
                            <small class="text-muted" id="passwordHelp">Laisser vide pour conserver le mot de passe actuel.</small>
                        </div>

                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="password" name="password_confirmation" id="user_password_confirmation" class="form-control" placeholder="Confirmation" minlength="6">
                                <label for="user_password_confirmation">Confirmer le mot de passe</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary rounded-pill">
                        <i class="bi bi-save"></i>
                        Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openUserModal(user = null)
{
    const modalElement = document.getElementById('userModal');
    const modal = bootstrap.Modal.getOrCreateInstance(modalElement);

    const form = document.getElementById('userForm');
    const method = document.getElementById('userMethod');
    const title = document.getElementById('userModalTitle');
    const passwordHelp = document.getElementById('passwordHelp');

    document.getElementById('user_name').value = user?.name ?? '';
    document.getElementById('user_email').value = user?.email ?? '';
    document.getElementById('user_login').value = user?.login ?? '';
    document.getElementById('user_droit').value = user?.droit ?? 'CLIENT';
    document.getElementById('user_password').value = '';
    document.getElementById('user_password_confirmation').value = '';

    if (user?.id) {
        title.innerHTML =
            '<i class="bi bi-person"></i> Modifier un utilisateur';

        method.value = 'PUT';
        form.action = "{{ url('/utilisateurs') }}/" + user.id;

        document.getElementById('user_password').required = false;
        document.getElementById('user_password_confirmation').required = false;

        passwordHelp.classList.remove('d-none');
    } else {
        title.innerHTML =
            '<i class="bi bi-person-plus"></i> Ajouter un utilisateur';

        method.value = 'POST';
        form.action = "{{ route('utilisateurs.store') }}";

        document.getElementById('user_password').required = true;
        document.getElementById('user_password_confirmation').required = true;

        passwordHelp.classList.add('d-none');
    }

    modal.show();
}

document.querySelectorAll('.edit-user').forEach(button => {
    button.addEventListener('click', function () {
        const user = {
            id: this.dataset.id,
            name: this.dataset.name,
            email: this.dataset.email,
            login: this.dataset.login,
            droit: this.dataset.droit
        };

        openUserModal(user);
    });
});
</script>
@endsection