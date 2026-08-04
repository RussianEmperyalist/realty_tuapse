<?php

return [
    'company_name' => env('APP_NAME', 'АН Туапсе'),
    'company_display_name' => env('REALTY_COMPANY_DISPLAY_NAME', 'Агентство недвижимости "Туапсе"'),
    'operator_name' => env('REALTY_OPERATOR_NAME', 'ИП Шляхов Александр Владимирович'),
    'operator_inn' => env('REALTY_OPERATOR_INN', '232200229212'),
    'operator_address' => env('REALTY_OPERATOR_ADDRESS', '352800, Краснодарский край, г. Туапсе, ул. К. Маркса, д. 16/1'),
    'site_domain' => env('REALTY_SITE_DOMAIN', 'realty-tuapse.ru'),
    'contact_email' => env('REALTY_CONTACT_EMAIL', 'tuapse-tuapse@mail.ru'),
    'support_email' => env('REALTY_SUPPORT_EMAIL', 'tuapse-tuapse@mail.ru'),
    'mail_test_email' => env('MAIL_TEST_TO', env('REALTY_CONTACT_EMAIL', 'tuapse-tuapse@mail.ru')),
    'phones' => [
        '+7 (86167) 25555',
        '+7 (86167) 37777',
        '+7 (86167) 47777',
        '+7 (918) 3727777',
        '+7 (918) 4337777',
        '+7 (988) 4177777',
    ],
    'city_options' => [
        'tuapse' => 'Туапсе',
        'tuapsinskij-rajon' => 'Туапсинский район',
    ],
    'deal_type_options' => [
        'rent' => 'Аренда',
        'sale' => 'Продажа',
    ],
    'property_type_options' => [
        'apartment' => 'квартира',
        'room' => 'комната',
        'house' => 'дом',
        'land' => 'земельный участок',
        'new_building' => 'новостройка',
        'hotel' => 'гостиница',
        'garage' => 'гараж',
        'commercial' => 'коммерция',
    ],
];
