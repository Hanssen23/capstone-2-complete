<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UidPoolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $uids = [
            'E6415F5F',
            'A69D194E',
            '56438A5F',
            'B696735F',
            'E69F8F40',
            '2665004E',
            'F665785F',
            'E6258C40',
            'B688164E',
        ];

        foreach ($uids as $uid) {
            DB::table('uid_pool')->insert([
                'uid' => $uid,
                'status' => 'available',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('UID Pool seeded with ' . count($uids) . ' UIDs');
    }
}