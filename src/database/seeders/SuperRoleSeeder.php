<?php

namespace ZaghloulSoft\LaravelAuthorization\database\seeders;
use Illuminate\Database\Seeder;
use ZaghloulSoft\LaravelAuthorization\Facades\Role;

class SuperRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Role::seedSuperRole();
    }
}
