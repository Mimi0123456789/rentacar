@extends('layouts.guest')

@section('content')

<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 px-4">

    <div class="w-full max-w-md bg-white rounded-2xl shadow-2xl p-8">

        {{-- Logo / Titre --}}
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-slate-800">Connexion</h1>
        </div>

        {{-- Status session --}}
        @if(session('status'))
            <div class="mb-5 rounded-lg bg-green-100 border border-green-200 text-green-700 px-4 py-3 text-sm">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf

            {{-- Email --}}
            <div>
                <label for="email" class="block text-sm font-medium text-slate-700">
                    Adresse email
                </label>

                <input id="email"
                       type="email"
                       name="email"
                       value="{{ old('email') }}"
                       required
                       autofocus
                       autocomplete="username"
                       placeholder="exemple@email.com"
                       class="mt-2 w-full rounded-xl border border-slate-300 px-4 py-2.5 text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">

                @error('email')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Password --}}
            <div>
                <label for="password" class="block text-sm font-medium text-slate-700">
                    Mot de passe
                </label>

                <input id="password"
                       type="password"
                       name="password"
                       required
                       autocomplete="current-password"
                       placeholder="••••••••"
                       class="mt-2 w-full rounded-xl border border-slate-300 px-4 py-2.5 text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">

                @error('password')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Button --}}
            <button type="submit"
                    class="w-full rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-semibold py-3 transition shadow-lg shadow-indigo-600/30">
                Se connecter
            </button>
        </form>

    </div>
</div>

@endsection

