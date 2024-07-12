<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class SeedUsers extends Seeder
{
    public function run()
    {
        $data = [
            [
                'name' => 'Administrator',
                'username' => 'admin',
                'email' => 'admin@gmail.com',
                'password' => password_hash('123', PASSWORD_DEFAULT),
                'image' => 'user.png',
                'role_id' => 1,
                'is_active' => 1,
            ],
            [
                'name' => 'Member',
                'username' => 'member',
                'email' => 'member@gmail.com',
                'password' => password_hash('123', PASSWORD_DEFAULT),
                'image' => 'user.png',
                'role_id' => 2,
                'is_active' => 1,
            ],
        ];

        $this->db->table('users')->insertBatch($data);
    }
}
