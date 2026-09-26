<?php

namespace Database\Seeders;

use App\Models\Division;
use Illuminate\Database\Seeder;

class DivisionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $divisions = [
            ['name' => 'IT & Development', 'description' => 'Departemen pengembangan sistem, perangkat lunak dan infrastruktur IT'],
            ['name' => 'Human Resources (HRD)', 'description' => 'Departemen pengelolaan sumber daya manusia, absensi dan rekrutmen'],
            ['name' => 'Keuangan & Akuntansi', 'description' => 'Departemen perencanaan anggaran, pembukuan dan kas keuangan'],
            ['name' => 'Pemasaran & Bisnis', 'description' => 'Departemen strategi branding, promosi dan kemitraan bisnis'],
            ['name' => 'Operasional', 'description' => 'Departemen pengelolaan kegiatan operasional harian kantor'],
        ];

        foreach ($divisions as $d) {
            Division::firstOrCreate(['name' => $d['name']], $d);
        }
    }
}
