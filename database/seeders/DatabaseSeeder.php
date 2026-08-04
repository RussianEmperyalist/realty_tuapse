<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\GalleryAlbum;
use App\Models\GalleryItem;
use App\Models\NewsPost;
use App\Models\Property;
use App\Models\PropertyImage;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $seedDemoUsers = app()->environment(['local', 'testing'])
            || filter_var((string) env('REALTY_SEED_DEMO_USERS', 'false'), FILTER_VALIDATE_BOOL);

        $users = $seedDemoUsers ? [
            'alexander-vladimirovich-shlyakhov' => User::query()->updateOrCreate(
                ['email' => 'shlyakhov@realty-tuapse.local'],
                [
                    'name' => 'Александр Владимирович Шляхов',
                    'password' => Hash::make('password'),
                    'role' => 'admin',
                    'is_active' => true,
                ],
            ),
            'irina-aleksandrovna-skrypnik' => User::query()->updateOrCreate(
                ['email' => 'skrypnik@realty-tuapse.local'],
                [
                    'name' => 'Ирина Александровна Скрыпник',
                    'password' => Hash::make('password'),
                    'role' => 'admin',
                    'is_active' => true,
                ],
            ),
            'natalya-valentinovna-evseeva' => User::query()->updateOrCreate(
                ['email' => 'evseeva@realty-tuapse.local'],
                [
                    'name' => 'Наталья Валентиновна Евсеева',
                    'password' => Hash::make('password'),
                    'role' => 'employee',
                    'is_active' => false,
                ],
            ),
        ] : [];

        $employees = [
            [
                'legacy_id' => 4,
                'slug' => 'alexander-vladimirovich-shlyakhov',
                'full_name' => 'Александр Владимирович Шляхов',
                'position' => 'Руководитель',
                'sort_order' => 1,
                'phone_primary' => '+79884177777',
                'phone_secondary' => '+78616747777',
                'email' => 'tuapse-tuapse@mail.ru',
                'photo_path' => 'legacy/images/contact/c3e0ea4f724d96baf93c5d706cf11525.jpg',
                'is_admin' => true,
                'user_id' => $users['alexander-vladimirovich-shlyakhov']->id ?? null,
            ],
            [
                'legacy_id' => 5,
                'slug' => 'natalya-valentinovna-evseeva',
                'full_name' => 'Наталья Валентиновна Евсеева',
                'position' => 'Заместитель руководителя',
                'sort_order' => 2,
                'phone_primary' => '+79183727777',
                'phone_secondary' => '+78616725555',
                'email' => 'tuapse77777@mail.ru',
                'photo_path' => 'legacy/images/contact/2493d82a1fb0dfdc208da02a5a51a240.jpg',
                'is_admin' => false,
                'user_id' => $users['natalya-valentinovna-evseeva']->id ?? null,
            ],
            [
                'legacy_id' => 9,
                'slug' => 'irina-aleksandrovna-skrypnik',
                'full_name' => 'Ирина Александровна Скрыпник',
                'position' => 'Заместитель руководителя',
                'sort_order' => 3,
                'phone_primary' => '+79886745577',
                'phone_secondary' => '+78616745577',
                'email' => 'tuapse55555@mail.ru',
                'photo_path' => 'legacy/images/contact/d1be6581dad19743feb66a1034a80dfc.jpg',
                'is_admin' => true,
                'user_id' => $users['irina-aleksandrovna-skrypnik']->id ?? null,
            ],
            [
                'legacy_id' => 6,
                'slug' => 'vladislav-slavikovich-kuadzhe',
                'full_name' => 'Владислав Славикович Куадже',
                'position' => 'Риелтор',
                'sort_order' => 4,
                'phone_primary' => '+79881459777',
                'phone_secondary' => '+78616725555',
                'email' => 'tuapse9881459777@mail.ru',
                'photo_path' => 'legacy/images/contact/118205652ef9776fd3358c755cb5f013.jpeg',
                'is_admin' => false,
                'user_id' => null,
            ],
            [
                'legacy_id' => 10,
                'slug' => 'glyusa-robertovna-nikolaeva',
                'full_name' => 'Глюса Робертовна Николаева',
                'position' => 'Риелтор',
                'sort_order' => 5,
                'phone_primary' => '+79182073777',
                'phone_secondary' => '+78616725555',
                'email' => 'tuapse9182073777@mail.ru',
                'photo_path' => 'legacy/images/contact/25989b664f2b71f225002d7f741a7f03.jpg',
                'is_admin' => false,
                'user_id' => null,
            ],
            [
                'legacy_id' => 13,
                'slug' => 'aleksey-vladimirovich-balakin',
                'full_name' => 'Алексей Владимирович Балакин',
                'position' => 'Риелтор',
                'sort_order' => 6,
                'phone_primary' => '+79884066777',
                'phone_secondary' => '+78616725555',
                'email' => 'tuapse9884066777@mail.ru',
                'photo_path' => 'legacy/themes/dolphin/assets/images/no_photo_entry.png',
                'is_admin' => false,
                'user_id' => null,
            ],
            [
                'legacy_id' => 7,
                'slug' => 'irina-mihajlovna-shvecova',
                'full_name' => 'Ирина Михайловна Швецова',
                'position' => 'Риелтор',
                'sort_order' => 7,
                'phone_primary' => '+79884064777',
                'phone_secondary' => '+78616725555',
                'email' => 'tuapse9884064777@mail.ru',
                'photo_path' => 'legacy/images/contact/e905978c6bd9c3a911c34fb6ef2793c9.jpg',
                'is_admin' => false,
                'user_id' => null,
            ],
            [
                'legacy_id' => 8,
                'slug' => 'olga-petrovna-shevchenko',
                'full_name' => 'Ольга Петровна Шевченко',
                'position' => 'Риелтор',
                'sort_order' => 8,
                'phone_primary' => '+79884029777',
                'phone_secondary' => '+78616725555',
                'email' => 'tuapse9884029777@mail.ru',
                'photo_path' => 'legacy/uploads/editor/images/%20%D0%A8%D0%B5%D0%B2%D1%87%D0%B5%D0%BD%D0%BA%D0%BE.jpg',
                'is_admin' => false,
                'user_id' => null,
            ],
        ];

        foreach ($employees as $employeeData) {
            Employee::query()->updateOrCreate(
                ['slug' => $employeeData['slug']],
                $employeeData + ['bio' => null, 'is_active' => true],
            );
        }

        SiteSetting::query()->updateOrCreate(
            ['key' => 'about_company'],
            [
                'type' => 'text',
                'value' => 'Агентство недвижимости Туапсе – Ваш проводник в мир выбора идеального жилья. Мы видим в каждом клиенте уникальную личность, стремимся понять ваши запросы и мечты, чтобы найти жилище, отвечающее именно вашим представлениям об идеале.',
            ],
        );

        SiteSetting::query()->updateOrCreate(
            ['key' => 'contact_email'],
            [
                'type' => 'string',
                'value' => config('realty.contact_email'),
            ],
        );

        $skrypnik = Employee::query()->where('slug', 'irina-aleksandrovna-skrypnik')->firstOrFail();
        $evseeva = Employee::query()->where('slug', 'natalya-valentinovna-evseeva')->firstOrFail();
        $shlyakhov = Employee::query()->where('slug', 'alexander-vladimirovich-shlyakhov')->firstOrFail();

        $properties = [
            [
                'legacy_id' => 189,
                'employee_id' => $skrypnik->id,
                'title' => 'Продам 3-ком.квартиру ЦЕНТР',
                'slug' => 'prodam-3-komkvartiru-centr',
                'deal_type' => 'sale',
                'property_type' => 'apartment',
                'city' => 'tuapse',
                'address' => 'Туапсе, Маршала Жукова 18',
                'price' => 9490000,
                'price_label' => '9.49 млн.',
                'currency' => 'руб.',
                'rooms' => 3,
                'floor' => 4,
                'floors_total' => 5,
                'square' => 55.50,
                'windows' => 'во двор',
                'description' => "Продам 3-ком.квартиру, 55,5м2, ЦЕНТР г.Туапсе, ул. Маршала Жукова.\n\nОсновные характеристики:\nэтаж: 4/5 (кирпичный дом, середина здания);\nплощадь: 55,5 м2;\nпланировка: зал, 2 раздельные комнаты, 2 гардеробные, оборудованная кухня, балкон, совместный санузел, коридор.\n\nПреимущества:\nсветлая и теплая квартира;\nразвитая инфраструктура: магазины, рынок, кафе, школы, детские сады;\nпрогулочная аллея, парк и набережная в шаговой доступности, до моря 15-20 минут пешком;\nгараж во дворе идет в подарок.",
                'latitude' => 44.0977250,
                'longitude' => 39.0809190,
                'phone_override' => '+79886745577',
                'is_published' => true,
                'is_featured' => true,
                'published_at' => now()->subDays(9),
            ],
            [
                'legacy_id' => 104,
                'employee_id' => $evseeva->id,
                'title' => 'Продаю 3-ком.квартиру в Центре с ремонтом и техникой',
                'slug' => 'prodayu-3-komkvartiru-v-centre-s-remontom-i-tehnikoj',
                'deal_type' => 'sale',
                'property_type' => 'apartment',
                'city' => 'tuapse',
                'address' => 'Туапсе, центр города',
                'price' => 10000000,
                'price_label' => '10 млн.',
                'currency' => 'руб.',
                'rooms' => 3,
                'floor' => 5,
                'floors_total' => 9,
                'square' => 72.00,
                'description' => 'Просторная квартира с ремонтом и техникой в центре Туапсе. Подходит для постоянного проживания и инвестиции.',
                'phone_override' => '+79183727777',
                'is_published' => true,
                'is_featured' => true,
                'published_at' => now()->subDays(5),
            ],
            [
                'legacy_id' => 201,
                'employee_id' => $shlyakhov->id,
                'title' => 'Дом 180 м2 в пригороде',
                'slug' => 'dom-180-m2-v-prigorode',
                'deal_type' => 'sale',
                'property_type' => 'house',
                'city' => 'tuapsinskij-rajon',
                'address' => 'Туапсинский район, пригород',
                'price' => 12500000,
                'price_label' => '12.5 млн.',
                'currency' => 'руб.',
                'rooms' => 5,
                'floors_total' => 2,
                'square' => 180.00,
                'description' => 'Дом для большой семьи с участком, парковкой и хорошим подъездом. Тихое место рядом с природой.',
                'phone_override' => '+79884177777',
                'is_published' => true,
                'is_featured' => false,
                'published_at' => now()->subDays(2),
            ],
        ];

        foreach ($properties as $propertyData) {
            Property::query()->updateOrCreate(
                ['slug' => $propertyData['slug']],
                $propertyData,
            );
        }

        $propertyImagesBySlug = [
            'prodam-3-komkvartiru-centr' => [
                ['path' => 'legacy/uploads/objects/189/modified/full_46e763bbbae9a037320a5677d1cd9fbf.jpeg', 'thumb_path' => 'legacy/uploads/objects/189/modified/thumb_205x107_46e763bbbae9a037320a5677d1cd9fbf.jpeg'],
                ['path' => 'legacy/uploads/objects/189/modified/full_96a78d5f9a7170302647a37ff2d3298f.jpeg', 'thumb_path' => 'legacy/uploads/objects/189/modified/thumb_205x107_96a78d5f9a7170302647a37ff2d3298f.jpeg'],
                ['path' => 'legacy/uploads/objects/189/modified/full_cb21e078a1fb61b09eee773efae4e100.jpeg', 'thumb_path' => 'legacy/uploads/objects/189/modified/thumb_205x107_cb21e078a1fb61b09eee773efae4e100.jpeg'],
                ['path' => 'legacy/uploads/objects/189/modified/full_163aa8bb0fdd2d90a7cb4cc53caf6222.jpeg', 'thumb_path' => 'legacy/uploads/objects/189/modified/thumb_205x107_163aa8bb0fdd2d90a7cb4cc53caf6222.jpeg'],
                ['path' => 'legacy/uploads/objects/189/modified/full_3ec1cb535701886d3ea61ed5c0d3a41a.jpeg', 'thumb_path' => 'legacy/uploads/objects/189/modified/thumb_205x107_3ec1cb535701886d3ea61ed5c0d3a41a.jpeg'],
            ],
            'prodayu-3-komkvartiru-v-centre-s-remontom-i-tehnikoj' => [
                ['path' => 'legacy/uploads/objects/104/modified/full_0d3a7944975213a598408654924f66fe.jpg', 'thumb_path' => 'legacy/uploads/objects/104/modified/thumb_205x107_0d3a7944975213a598408654924f66fe.jpg'],
                ['path' => 'legacy/uploads/objects/104/modified/full_852b8f776679b646a8fdcd36a5da733e.jpg', 'thumb_path' => 'legacy/uploads/objects/104/modified/thumb_205x107_852b8f776679b646a8fdcd36a5da733e.jpg'],
                ['path' => 'legacy/uploads/objects/104/modified/full_9efd01f438404d0bd3c52ad15187ba74.jpg', 'thumb_path' => 'legacy/uploads/objects/104/modified/thumb_205x107_9efd01f438404d0bd3c52ad15187ba74.jpg'],
                ['path' => 'legacy/uploads/objects/104/modified/full_c195a13750097aa02384b1ad89fe966a.jpg', 'thumb_path' => 'legacy/uploads/objects/104/modified/thumb_205x107_c195a13750097aa02384b1ad89fe966a.jpg'],
                ['path' => 'legacy/uploads/objects/104/modified/full_e4f4b101fe770107a50f749d7cf333f9.jpg', 'thumb_path' => 'legacy/uploads/objects/104/modified/thumb_205x107_e4f4b101fe770107a50f749d7cf333f9.jpg'],
            ],
            'dom-180-m2-v-prigorode' => [
                ['path' => 'legacy/uploads/objects/180/modified/full_8f173d9ad6f89205b13052ecc84aa157.jpg', 'thumb_path' => 'legacy/uploads/objects/180/modified/thumb_205x107_8f173d9ad6f89205b13052ecc84aa157.jpg'],
                ['path' => 'legacy/uploads/objects/180/modified/full_b862b09f8672dd3e6a94439b721c5503.jpg', 'thumb_path' => 'legacy/uploads/objects/180/modified/thumb_205x107_b862b09f8672dd3e6a94439b721c5503.jpg'],
                ['path' => 'legacy/uploads/objects/180/modified/full_4f9d7de1299ca45e6cb9c0871f1e4116.jpg', 'thumb_path' => 'legacy/uploads/objects/180/modified/thumb_205x107_4f9d7de1299ca45e6cb9c0871f1e4116.jpg'],
                ['path' => 'legacy/uploads/objects/180/modified/full_3e59f9da775e219c4f6b2c0af91a9bbb.jpg', 'thumb_path' => 'legacy/uploads/objects/180/modified/thumb_205x107_3e59f9da775e219c4f6b2c0af91a9bbb.jpg'],
                ['path' => 'legacy/uploads/objects/180/modified/full_8eb157e9d39f8d97b81318c6cc9e24e9.jpg', 'thumb_path' => 'legacy/uploads/objects/180/modified/thumb_205x107_8eb157e9d39f8d97b81318c6cc9e24e9.jpg'],
            ],
        ];

        foreach ($propertyImagesBySlug as $propertySlug => $propertyImages) {
            $property = Property::query()->where('slug', $propertySlug)->firstOrFail();

            foreach ($propertyImages as $index => $image) {
                PropertyImage::query()->updateOrCreate(
                    [
                        'property_id' => $property->id,
                        'sort_order' => $index + 1,
                    ],
                    $image + [
                        'alt' => $property->title,
                        'is_cover' => $index === 0,
                    ],
                );
            }
        }

        $news = [
            [
                'title' => 'Рынок недвижимости Туапсе: весна 2026',
                'slug' => 'rynok-nedvizhimosti-tuapse-vesna-2026',
                'legacy_path' => 'news/rynok-nedvizhimosti-tuapse-vesna-2026',
                'excerpt' => 'Разбираем активный спрос на квартиры у моря, загородные дома и коммерческие объекты.',
                'body' => '<p>Весной 2026 года рынок недвижимости Туапсе показывает устойчивый интерес к квартирам в центре, объектам у моря и готовым домам для постоянного проживания.</p><p>Покупатели чаще всего ищут понятные по документам объекты с прозрачной историей и удобной локацией.</p>',
                'image_path' => 'legacy/uploads/objects/189/modified/thumb_300x200_46e763bbbae9a037320a5677d1cd9fbf.jpeg',
                'is_published' => true,
                'published_at' => now()->subDays(3),
            ],
            [
                'title' => 'Как подготовить объект к продаже без лишних затрат',
                'slug' => 'kak-podgotovit-obekt-k-prodazhe-bez-lishnih-zatrat',
                'legacy_path' => 'news/kak-podgotovit-obekt-k-prodazhe-bez-lishnih-zatrat',
                'excerpt' => 'Небольшие улучшения могут ускорить сделку и повысить привлекательность объявления.',
                'body' => '<p>Перед публикацией объявления важно привести объект в аккуратный вид, сделать качественные фотографии и подготовить точное описание.</p><p>Даже базовая предпродажная подготовка повышает доверие покупателей и помогает быстрее выйти на показ.</p>',
                'image_path' => 'legacy/uploads/gallery/new/001.jpg',
                'is_published' => true,
                'published_at' => now()->subDay(),
            ],
        ];

        foreach ($news as $newsItem) {
            NewsPost::query()->updateOrCreate(
                ['slug' => $newsItem['slug']],
                $newsItem,
            );
        }

        $album = GalleryAlbum::query()->updateOrCreate(
            ['slug' => 'fotogalereya-agentstva'],
            [
                'title' => 'Фотогалерея агентства',
                'description' => 'Подборка фотографий офиса, команды и объектов недвижимости.',
                'cover_image_path' => 'legacy/uploads/gallery/new/001.jpg',
                'sort_order' => 1,
                'is_published' => true,
            ],
        );

        foreach (range(1, 6) as $index) {
            $number = str_pad((string) $index, 3, '0', STR_PAD_LEFT);

            GalleryItem::query()->updateOrCreate(
                [
                    'gallery_album_id' => $album->id,
                    'sort_order' => $index,
                ],
                [
                    'title' => 'Фото ' . $number,
                    'image_path' => "legacy/uploads/gallery/new/{$number}.jpg",
                    'thumb_path' => "legacy/uploads/gallery/new/{$number}_thumb.jpg",
                    'is_published' => true,
                ],
            );
        }

        $this->call(LegacyContentSeeder::class);
    }
}
