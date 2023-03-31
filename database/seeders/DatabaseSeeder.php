<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(ReportSeeder::class);
        $this->call(ReportVisitorSeeder::class);
        $this->call(MeetingSeeder::class);
        $this->call(MeetingVisitorSeeder::class);
        $this->call(NoticeSeeder::class);
        $this->call(OfficeTodoSeeder::class);
        $this->call(SalesTodoSeeder::class);
    }
}
