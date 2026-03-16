<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Kursi',
                'slug' => 'kursi',
                'description' => 'Berbagai jenis kursi untuk ruang tamu, ruang makan, dan ruang kerja dengan desain modern dan klasik',
                'is_active' => true,
            ],
            [
                'name' => 'Meja',
                'slug' => 'meja',
                'description' => 'Meja untuk berbagai keperluan seperti meja makan, meja kerja, meja belajar, dan meja tamu',
                'is_active' => true,
            ],
            [
                'name' => 'Lemari',
                'slug' => 'lemari',
                'description' => 'Lemari pakaian, lemari buku, lemari sepatu, dan lemari penyimpanan dengan berbagai ukuran',
                'is_active' => true,
            ],
            [
                'name' => 'Tempat Tidur',
                'slug' => 'tempat-tidur',
                'description' => 'Ranjang dan tempat tidur dengan berbagai ukuran mulai dari single, queen, hingga king size',
                'is_active' => true,
            ],
            [
                'name' => 'Rak',
                'slug' => 'rak',
                'description' => 'Rak display, rak buku, rak pajangan, dan rak penyimpanan untuk berbagai keperluan',
                'is_active' => true,
            ],
            [
                'name' => 'Sofa',
                'slug' => 'sofa',
                'description' => 'Sofa nyaman untuk ruang tamu dan ruang keluarga dengan berbagai model dan ukuran',
                'is_active' => true,
            ],
            [
                'name' => 'Nakas',
                'slug' => 'nakas',
                'description' => 'Meja samping tempat tidur atau nakas dengan desain minimalis dan klasik',
                'is_active' => true,
            ],
            [
                'name' => 'Bufet',
                'slug' => 'bufet',
                'description' => 'Bufet TV, bufet dapur, dan lemari hias untuk ruang makan dan ruang keluarga',
                'is_active' => true,
            ],
            [
                'name' => 'Kitchen Set',
                'slug' => 'kitchen-set',
                'description' => 'Kitchen set modern dan klasik dengan desain custom sesuai kebutuhan dapur Anda',
                'is_active' => true,
            ],
            [
                'name' => 'Partisi',
                'slug' => 'partisi',
                'description' => 'Partisi ruangan, sekat ruangan, dan pembatas ruangan dengan desain minimalis',
                'is_active' => true,
            ],
            [
                'name' => 'Meja Konsol',
                'slug' => 'meja-konsol',
                'description' => 'Meja konsol untuk dekorasi entrance, ruang tamu, atau lorong dengan desain elegan',
                'is_active' => true,
            ],
            [
                'name' => 'Credenza',
                'slug' => 'credenza',
                'description' => 'Credenza atau meja panjang untuk penyimpanan dan display dengan desain modern',
                'is_active' => true,
            ],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['slug' => $category['slug']], // Cari berdasarkan slug
                $category // Data yang akan diupdate atau create
            );
        }
    }
}
