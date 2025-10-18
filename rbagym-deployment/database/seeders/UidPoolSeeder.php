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
            // Additional UIDs to prevent pool exhaustion
            'C1234567',
            'D2345678',
            'E3456789',
            'F4567890',
            'A5678901',
            'B6789012',
            'C7890123',
            'D8901234',
            'E9012345',
            'F0123456',
        ];

        $addedCount = 0;
        foreach ($uids as $uid) {
            // Check if UID already exists
            $exists = DB::table('uid_pool')->where('uid', $uid)->exists();
            
            if (!$exists) {
                DB::table('uid_pool')->insert([
                    'uid' => $uid,
                    'status' => 'available',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $addedCount++;
            }
        }

        $this->command->info('UID Pool seeded with ' . $addedCount . ' new UIDs (total: ' . count($uids) . ' UIDs available)');
    }
}