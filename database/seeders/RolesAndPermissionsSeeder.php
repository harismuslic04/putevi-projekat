<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Spatie\Permission\Models\Role;
use App\Models\User;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create Roles
        $roleVozac = Role::create(['name' => 'vozac']);
        $roleRadnik = Role::create(['name' => 'terenski_radnik']);
        $roleDispecer = Role::create(['name' => 'dispecer']);
        $roleMenadzer = Role::create(['name' => 'menadzer']);

        // Create Default Users
        $admin = User::create([
            'name' => 'Menadzer',
            'email' => 'menadzer@test.com',
            'password' => bcrypt('password'),
        ]);
        $admin->assignRole($roleMenadzer);

        $dispecer = User::create([
            'name' => 'Dispecer',
            'email' => 'dispecer@test.com',
            'password' => bcrypt('password'),
        ]);
        $dispecer->assignRole($roleDispecer);

        $radnik = User::create([
            'name' => 'Radnik',
            'email' => 'radnik@test.com',
            'password' => bcrypt('password'),
        ]);
        $radnik->assignRole($roleRadnik);

        $vozac = User::create([
            'name' => 'Vozac',
            'email' => 'vozac@test.com',
            'password' => bcrypt('password'),
        ]);
        $vozac->assignRole($roleVozac);
    }
}
