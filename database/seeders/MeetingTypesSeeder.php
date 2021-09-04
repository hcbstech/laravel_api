<?php

namespace Database\Seeders;
use App\Models\MettingTypes;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class MeetingTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
//         DB::table('meeting_types')->insert([
//            'name' => 'face_to_face'
//             ]);
         DB::table('meeting_types')->insert([
            'name' => 'video'
             ]);
         DB::table('meeting_types')->insert([
            'name' => 'voice'
             ]);
         DB::table('meeting_types')->insert([
            'name' => 'chat'
             ]);
    }
}
