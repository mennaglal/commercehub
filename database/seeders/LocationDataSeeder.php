<?php

namespace Database\Seeders;

use App\Models\Area;
use App\Models\City;
use App\Models\Country;
use App\Models\Currency;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LocationDataSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {

            /*
             * Currencies
             * rate_to_egp means how many EGP equal one unit.
             */
            $currenciesData = [
                [
                    'name_ar' => 'الجنيه المصري',
                    'name_en' => 'Egyptian Pound',
                    'code' => 'EGP',
                    'symbol' => 'ج.م',
                    'decimal_places' => 2,
                    'rate_to_egp' => 1,
                ],
                [
                    'name_ar' => 'الدينار الكويتي',
                    'name_en' => 'Kuwaiti Dinar',
                    'code' => 'KWD',
                    'symbol' => 'د.ك',
                    'decimal_places' => 3,
                    'rate_to_egp' => 160,
                ],
                [
                    'name_ar' => 'الريال السعودي',
                    'name_en' => 'Saudi Riyal',
                    'code' => 'SAR',
                    'symbol' => 'ر.س',
                    'decimal_places' => 2,
                    'rate_to_egp' => 13.3,
                ],
                [
                    'name_ar' => 'الدرهم الإماراتي',
                    'name_en' => 'UAE Dirham',
                    'code' => 'AED',
                    'symbol' => 'د.إ',
                    'decimal_places' => 2,
                    'rate_to_egp' => 13.6,
                ],
                [
                    'name_ar' => 'الريال القطري',
                    'name_en' => 'Qatari Riyal',
                    'code' => 'QAR',
                    'symbol' => 'ر.ق',
                    'decimal_places' => 2,
                    'rate_to_egp' => 13.7,
                ],
                [
                    'name_ar' => 'الدينار البحريني',
                    'name_en' => 'Bahraini Dinar',
                    'code' => 'BHD',
                    'symbol' => 'د.ب',
                    'decimal_places' => 3,
                    'rate_to_egp' => 132,
                ],
                [
                    'name_ar' => 'الريال العماني',
                    'name_en' => 'Omani Rial',
                    'code' => 'OMR',
                    'symbol' => 'ر.ع',
                    'decimal_places' => 3,
                    'rate_to_egp' => 129,
                ],
                [
                    'name_ar' => 'الدينار الأردني',
                    'name_en' => 'Jordanian Dinar',
                    'code' => 'JOD',
                    'symbol' => 'د.أ',
                    'decimal_places' => 3,
                    'rate_to_egp' => 73,
                ],
            ];

            foreach ($currenciesData as $data) {
                Currency::updateOrCreate(
                    ['code' => $data['code']],
                    array_merge($data, ['is_active' => true])
                );
            }

            /*
             * Countries, Cities and Areas
             *
             * Each city:
             * name_ar, name_en, areas
             *
             * Each area:
             * name_ar, name_en, shipping_price (in the country currency)
             */
            $countriesData = [

                // Egypt
                [
                    'name_ar' => 'مصر',
                    'name_en' => 'Egypt',
                    'code' => 'EG',
                    'currency' => 'EGP',
                    'cities' => [
                        [
                            'name_ar' => 'القاهرة',
                            'name_en' => 'Cairo',
                            'areas' => [
                                ['name_ar' => 'مدينة نصر', 'name_en' => 'Nasr City', 'shipping_price' => 50],
                                ['name_ar' => 'مصر الجديدة', 'name_en' => 'Heliopolis', 'shipping_price' => 50],
                                ['name_ar' => 'المعادي', 'name_en' => 'Maadi', 'shipping_price' => 50],
                                ['name_ar' => 'التجمع الخامس', 'name_en' => 'Fifth Settlement', 'shipping_price' => 50],
                            ],
                        ],
                        [
                            'name_ar' => 'الجيزة',
                            'name_en' => 'Giza',
                            'areas' => [
                                ['name_ar' => 'الدقي', 'name_en' => 'Dokki', 'shipping_price' => 50],
                                ['name_ar' => 'المهندسين', 'name_en' => 'Mohandessin', 'shipping_price' => 50],
                                ['name_ar' => 'السادس من أكتوبر', 'name_en' => '6th of October', 'shipping_price' => 50],
                                ['name_ar' => 'الشيخ زايد', 'name_en' => 'Sheikh Zayed', 'shipping_price' => 50],
                            ],
                        ],
                        [
                            'name_ar' => 'الإسكندرية',
                            'name_en' => 'Alexandria',
                            'areas' => [
                                ['name_ar' => 'سموحة', 'name_en' => 'Smouha', 'shipping_price' => 50],
                                ['name_ar' => 'سيدي جابر', 'name_en' => 'Sidi Gaber', 'shipping_price' => 50],
                                ['name_ar' => 'العجمي', 'name_en' => 'Agami', 'shipping_price' => 50],
                                ['name_ar' => 'المنتزه', 'name_en' => 'Montaza', 'shipping_price' => 50],
                            ],
                        ],
                        [
                            'name_ar' => 'المنصورة',
                            'name_en' => 'Mansoura',
                            'areas' => [
                                ['name_ar' => 'طلخا', 'name_en' => 'Talkha', 'shipping_price' => 50],
                                ['name_ar' => 'ميت غمر', 'name_en' => 'Mit Ghamr', 'shipping_price' => 50],
                            ],
                        ],
                        [
                            'name_ar' => 'طنطا',
                            'name_en' => 'Tanta',
                            'areas' => [
                                ['name_ar' => 'قطور', 'name_en' => 'Qutur', 'shipping_price' => 50],
                                ['name_ar' => 'كفر الزيات', 'name_en' => 'Kafr El Zayat', 'shipping_price' => 50],
                            ],
                        ],
                    ],
                ],

                // Kuwait
                [
                    'name_ar' => 'الكويت',
                    'name_en' => 'Kuwait',
                    'code' => 'KW',
                    'currency' => 'KWD',
                    'cities' => [
                        [
                            'name_ar' => 'مدينة الكويت',
                            'name_en' => 'Kuwait City',
                            'areas' => [
                                ['name_ar' => 'شرق', 'name_en' => 'Sharq', 'shipping_price' => 2],
                                ['name_ar' => 'القبلة', 'name_en' => 'Qibla', 'shipping_price' => 2],
                                ['name_ar' => 'المرقاب', 'name_en' => 'Mirqab', 'shipping_price' => 2],
                            ],
                        ],
                        [
                            'name_ar' => 'حولي',
                            'name_en' => 'Hawalli',
                            'areas' => [
                                ['name_ar' => 'السالمية', 'name_en' => 'Salmiya', 'shipping_price' => 2],
                                ['name_ar' => 'الرميثية', 'name_en' => 'Rumaithiya', 'shipping_price' => 2],
                                ['name_ar' => 'الجابرية', 'name_en' => 'Jabriya', 'shipping_price' => 2],
                            ],
                        ],
                        [
                            'name_ar' => 'الفروانية',
                            'name_en' => 'Farwaniya',
                            'areas' => [
                                ['name_ar' => 'خيطان', 'name_en' => 'Khaitan', 'shipping_price' => 2],
                                ['name_ar' => 'جليب الشيوخ', 'name_en' => 'Jleeb Al-Shuyoukh', 'shipping_price' => 2],
                                ['name_ar' => 'العارضية', 'name_en' => 'Ardiya', 'shipping_price' => 2],
                            ],
                        ],
                        [
                            'name_ar' => 'الأحمدي',
                            'name_en' => 'Ahmadi',
                            'areas' => [
                                ['name_ar' => 'الفحيحيل', 'name_en' => 'Fahaheel', 'shipping_price' => 2],
                                ['name_ar' => 'المنقف', 'name_en' => 'Mangaf', 'shipping_price' => 2],
                                ['name_ar' => 'أبو حليفة', 'name_en' => 'Abu Halifa', 'shipping_price' => 2],
                            ],
                        ],
                        [
                            'name_ar' => 'الجهراء',
                            'name_en' => 'Jahra',
                            'areas' => [
                                ['name_ar' => 'القصر', 'name_en' => 'Al Qasr', 'shipping_price' => 2],
                                ['name_ar' => 'النعيم', 'name_en' => 'Al Naim', 'shipping_price' => 2],
                            ],
                        ],
                    ],
                ],

                // Saudi Arabia
                [
                    'name_ar' => 'السعودية',
                    'name_en' => 'Saudi Arabia',
                    'code' => 'SA',
                    'currency' => 'SAR',
                    'cities' => [
                        [
                            'name_ar' => 'الرياض',
                            'name_en' => 'Riyadh',
                            'areas' => [
                                ['name_ar' => 'الملز', 'name_en' => 'Al Malaz', 'shipping_price' => 20],
                                ['name_ar' => 'العليا', 'name_en' => 'Al Olaya', 'shipping_price' => 20],
                                ['name_ar' => 'النرجس', 'name_en' => 'Al Narjis', 'shipping_price' => 20],
                            ],
                        ],
                        [
                            'name_ar' => 'جدة',
                            'name_en' => 'Jeddah',
                            'areas' => [
                                ['name_ar' => 'الحمراء', 'name_en' => 'Al Hamra', 'shipping_price' => 20],
                                ['name_ar' => 'الروضة', 'name_en' => 'Al Rawdah', 'shipping_price' => 20],
                                ['name_ar' => 'أبحر', 'name_en' => 'Obhur', 'shipping_price' => 20],
                            ],
                        ],
                        [
                            'name_ar' => 'مكة المكرمة',
                            'name_en' => 'Makkah',
                            'areas' => [
                                ['name_ar' => 'العزيزية', 'name_en' => 'Al Aziziyah', 'shipping_price' => 20],
                                ['name_ar' => 'الشوقية', 'name_en' => 'Al Shawqiyah', 'shipping_price' => 20],
                            ],
                        ],
                        [
                            'name_ar' => 'المدينة المنورة',
                            'name_en' => 'Madinah',
                            'areas' => [
                                ['name_ar' => 'قباء', 'name_en' => 'Quba', 'shipping_price' => 20],
                                ['name_ar' => 'العوالي', 'name_en' => 'Al Awali', 'shipping_price' => 20],
                            ],
                        ],
                        [
                            'name_ar' => 'الدمام',
                            'name_en' => 'Dammam',
                            'areas' => [
                                ['name_ar' => 'الفيصلية', 'name_en' => 'Al Faisaliyah', 'shipping_price' => 20],
                                ['name_ar' => 'الشاطئ', 'name_en' => 'Al Shati', 'shipping_price' => 20],
                            ],
                        ],
                    ],
                ],

                // United Arab Emirates
                [
                    'name_ar' => 'الإمارات',
                    'name_en' => 'United Arab Emirates',
                    'code' => 'AE',
                    'currency' => 'AED',
                    'cities' => [
                        [
                            'name_ar' => 'دبي',
                            'name_en' => 'Dubai',
                            'areas' => [
                                ['name_ar' => 'ديرة', 'name_en' => 'Deira', 'shipping_price' => 20],
                                ['name_ar' => 'بر دبي', 'name_en' => 'Bur Dubai', 'shipping_price' => 20],
                                ['name_ar' => 'جبل علي', 'name_en' => 'Jebel Ali', 'shipping_price' => 20],
                                ['name_ar' => 'البرشاء', 'name_en' => 'Al Barsha', 'shipping_price' => 20],
                            ],
                        ],
                        [
                            'name_ar' => 'أبوظبي',
                            'name_en' => 'Abu Dhabi',
                            'areas' => [
                                ['name_ar' => 'العين', 'name_en' => 'Al Ain', 'shipping_price' => 20],
                                ['name_ar' => 'الظفرة', 'name_en' => 'Al Dhafra', 'shipping_price' => 20],
                            ],
                        ],
                        [
                            'name_ar' => 'الشارقة',
                            'name_en' => 'Sharjah',
                            'areas' => [
                                ['name_ar' => 'خورفكان', 'name_en' => 'Khor Fakkan', 'shipping_price' => 20],
                                ['name_ar' => 'كلباء', 'name_en' => 'Kalba', 'shipping_price' => 20],
                            ],
                        ],
                        [
                            'name_ar' => 'عجمان',
                            'name_en' => 'Ajman',
                            'areas' => [
                                ['name_ar' => 'النعيمية', 'name_en' => 'Al Nuaimiya', 'shipping_price' => 20],
                                ['name_ar' => 'الراشدية', 'name_en' => 'Al Rashidiya', 'shipping_price' => 20],
                            ],
                        ],
                    ],
                ],

                // Qatar
                [
                    'name_ar' => 'قطر',
                    'name_en' => 'Qatar',
                    'code' => 'QA',
                    'currency' => 'QAR',
                    'cities' => [
                        [
                            'name_ar' => 'الدوحة',
                            'name_en' => 'Doha',
                            'areas' => [
                                ['name_ar' => 'السد', 'name_en' => 'Al Sadd', 'shipping_price' => 20],
                                ['name_ar' => 'الدفنة', 'name_en' => 'Al Dafna', 'shipping_price' => 20],
                                ['name_ar' => 'الوعب', 'name_en' => 'Al Waab', 'shipping_price' => 20],
                            ],
                        ],
                        [
                            'name_ar' => 'الريان',
                            'name_en' => 'Al Rayyan',
                            'areas' => [
                                ['name_ar' => 'الغرافة', 'name_en' => 'Al Gharrafa', 'shipping_price' => 20],
                                ['name_ar' => 'معيذر', 'name_en' => 'Muaither', 'shipping_price' => 20],
                            ],
                        ],
                        [
                            'name_ar' => 'الوكرة',
                            'name_en' => 'Al Wakrah',
                            'areas' => [
                                ['name_ar' => 'المشاف', 'name_en' => 'Al Mashaf', 'shipping_price' => 20],
                            ],
                        ],
                        [
                            'name_ar' => 'الخور',
                            'name_en' => 'Al Khor',
                            'areas' => [
                                ['name_ar' => 'الذخيرة', 'name_en' => 'Al Thakhira', 'shipping_price' => 20],
                            ],
                        ],
                    ],
                ],

                // Bahrain
                [
                    'name_ar' => 'البحرين',
                    'name_en' => 'Bahrain',
                    'code' => 'BH',
                    'currency' => 'BHD',
                    'cities' => [
                        [
                            'name_ar' => 'المنامة',
                            'name_en' => 'Manama',
                            'areas' => [
                                ['name_ar' => 'الجفير', 'name_en' => 'Juffair', 'shipping_price' => 2],
                                ['name_ar' => 'السيف', 'name_en' => 'Seef', 'shipping_price' => 2],
                                ['name_ar' => 'العدلية', 'name_en' => 'Adliya', 'shipping_price' => 2],
                            ],
                        ],
                        [
                            'name_ar' => 'المحرق',
                            'name_en' => 'Muharraq',
                            'areas' => [
                                ['name_ar' => 'قلالي', 'name_en' => 'Galali', 'shipping_price' => 2],
                                ['name_ar' => 'الحد', 'name_en' => 'Al Hidd', 'shipping_price' => 2],
                            ],
                        ],
                        [
                            'name_ar' => 'الرفاع',
                            'name_en' => 'Riffa',
                            'areas' => [
                                ['name_ar' => 'الرفاع الشرقي', 'name_en' => 'East Riffa', 'shipping_price' => 2],
                                ['name_ar' => 'الرفاع الغربي', 'name_en' => 'West Riffa', 'shipping_price' => 2],
                            ],
                        ],
                    ],
                ],

                // Oman
                [
                    'name_ar' => 'عمان',
                    'name_en' => 'Oman',
                    'code' => 'OM',
                    'currency' => 'OMR',
                    'cities' => [
                        [
                            'name_ar' => 'مسقط',
                            'name_en' => 'Muscat',
                            'areas' => [
                                ['name_ar' => 'مطرح', 'name_en' => 'Muttrah', 'shipping_price' => 2],
                                ['name_ar' => 'بوشر', 'name_en' => 'Bawshar', 'shipping_price' => 2],
                                ['name_ar' => 'السيب', 'name_en' => 'Seeb', 'shipping_price' => 2],
                            ],
                        ],
                        [
                            'name_ar' => 'صلالة',
                            'name_en' => 'Salalah',
                            'areas' => [
                                ['name_ar' => 'عوقد', 'name_en' => 'Awqad', 'shipping_price' => 2],
                                ['name_ar' => 'السعادة', 'name_en' => 'Al Saadah', 'shipping_price' => 2],
                            ],
                        ],
                        [
                            'name_ar' => 'صحار',
                            'name_en' => 'Sohar',
                            'areas' => [
                                ['name_ar' => 'فلج القبائل', 'name_en' => 'Falaj Al Qabail', 'shipping_price' => 2],
                            ],
                        ],
                    ],
                ],

                // Jordan
                [
                    'name_ar' => 'الأردن',
                    'name_en' => 'Jordan',
                    'code' => 'JO',
                    'currency' => 'JOD',
                    'cities' => [
                        [
                            'name_ar' => 'عمان',
                            'name_en' => 'Amman',
                            'areas' => [
                                ['name_ar' => 'العبدلي', 'name_en' => 'Abdali', 'shipping_price' => 2],
                                ['name_ar' => 'الصويفية', 'name_en' => 'Sweifieh', 'shipping_price' => 2],
                                ['name_ar' => 'خلدا', 'name_en' => 'Khalda', 'shipping_price' => 2],
                            ],
                        ],
                        [
                            'name_ar' => 'إربد',
                            'name_en' => 'Irbid',
                            'areas' => [
                                ['name_ar' => 'الحصن', 'name_en' => 'Al Husn', 'shipping_price' => 2],
                                ['name_ar' => 'الرمثا', 'name_en' => 'Al Ramtha', 'shipping_price' => 2],
                            ],
                        ],
                        [
                            'name_ar' => 'العقبة',
                            'name_en' => 'Aqaba',
                            'areas' => [
                                ['name_ar' => 'الشامية', 'name_en' => 'Al Shamiyah', 'shipping_price' => 2],
                            ],
                        ],
                        [
                            'name_ar' => 'الزرقاء',
                            'name_en' => 'Zarqa',
                            'areas' => [
                                ['name_ar' => 'الرصيفة', 'name_en' => 'Russeifa', 'shipping_price' => 2],
                            ],
                        ],
                    ],
                ],
            ];

            $currencies = Currency::query()->pluck('id', 'code');

            foreach ($countriesData as $countryData) {

                if (!isset($currencies[$countryData['currency']])) {
                    throw new \RuntimeException(
                        "Currency {$countryData['currency']} was not found."
                    );
                }

                $country = Country::updateOrCreate(
                    ['code' => $countryData['code']],
                    [
                        'name_ar' => $countryData['name_ar'],
                        'name_en' => $countryData['name_en'],
                        'currency_id' => $currencies[$countryData['currency']],
                        'is_active' => true,
                    ]
                );

                foreach ($countryData['cities'] as $cityData) {

                    $city = City::updateOrCreate(
                        [
                            'country_id' => $country->id,
                            'name_ar' => $cityData['name_ar'],
                        ],
                        [
                            'name_en' => $cityData['name_en'],
                            'is_active' => true,
                        ]
                    );

                    foreach ($cityData['areas'] as $areaData) {

                        Area::updateOrCreate(
                            [
                                'city_id' => $city->id,
                                'name_ar' => $areaData['name_ar'],
                            ],
                            [
                                'name_en' => $areaData['name_en'],
                                'shipping_price' => $areaData['shipping_price'],
                                'is_active' => true,
                            ]
                        );
                    }
                }
            }
        });
    }
}
