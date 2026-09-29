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
            [
                'name' => 'Petugas Logistik / Pengadaan',
                'description' => 'Menerima bahan baku dari pemasok, memeriksa kualitas bahan, dan mengelola tempat penyimpanan (dry/cold storage).',
            ],
            [
                'name' => 'Petugas Persiapan Bahan',
                'description' => 'Mencuci, memotong, dan meracik bahan makanan mentah sebelum dimasak sesuai takaran.',
            ],
            [
                'name' => 'Juru Masak Utama & Anggota (Chef/Koki)',
                'description' => 'Mengolah dan memasak makanan dalam jumlah besar sesuai resep dan standar higienitas',
            ],
            [
                'name' => 'Petugas Pemorsian',
                'description' => 'Menimbang dan menakar porsi makanan matang ke dalam wadah secara presisi sesuai standar gizi.',
            ],
            [
                'name' => 'Petugas Distribusi',
                'description' => 'Mengantar paket makanan bergizi ke satuan pendidikan atau titik sasaran tepat waktu dan mengambil kembali perlengkapan kosong.',
            ],
            [
                'name' => 'Petugas Kebersihan dan Sanitasi',
                'description' => 'Menjaga kebersihan area dapur, mencuci peralatan makan/masak, dan mengelola limbah untuk mencegah kontaminasi.',
            ],
        ];

        foreach ($divisions as $d) {
            Division::firstOrCreate(['name' => $d['name']], $d);
        }
    }
}
