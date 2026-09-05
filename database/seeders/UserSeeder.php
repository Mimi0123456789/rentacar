<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::truncate();

        User::create([
            'name' => 'Administrateur',
            'email' => 'admin@entreprise.fr',
            'login' => 'admin',
            'droit' => 'ADMIN',
            'password' => Hash::make('password'),
        ]);

        User::create([
            'name' => 'Gestionnaire',
            'email' => 'gestion@entreprise.fr',
            'login' => 'gestion',
            'droit' => 'GESTIONNAIRE',
            'password' => Hash::make('password'),
        ]);

        $users = [
            ['Paul Martin','paul.martin@entreprise.fr'],
            ['Julie Bernard','julie.bernard@entreprise.fr'],
            ['Lucas Petit','lucas.petit@entreprise.fr'],
            ['Emma Robert','emma.robert@entreprise.fr'],
            ['Thomas Richard','thomas.richard@entreprise.fr'],
            ['Camille Dubois','camille.dubois@entreprise.fr'],
            ['Nicolas Garcia','nicolas.garcia@entreprise.fr'],
            ['Laura Moreau','laura.moreau@entreprise.fr'],
        ];

        foreach ($users as $user){

            User::create([
                'name'=>$user[0],
                'email'=>$user[1],
                'login'=>strtolower(str_replace(' ','',$user[0])),
                'droit'=>'COLLABORATEUR',
                'password'=>Hash::make('password')
            ]);

        }
    }
}