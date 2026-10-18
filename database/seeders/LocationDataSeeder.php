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
             * rate_to_egp means how many EGP equal one unit of this currency.
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
                    array_merge($data, [
                        'is_active' => true,
                    ])
                );
            }

            /*
             * Countries
             */
            $countriesData = [
                [
                    'name_ar' => 'مصر',
                    'name_en' => 'Egypt',
                    'code' => 'EG',
                    'currency' => 'EGP',
                    'cities' => [
                        'القاهرة' => ['Cairo', 'مدينة نصر', 'مصر الجديدة', 'المعادي', 'التجمع الخامس'],
                        'الجيزة' => ['Giza', 'الدقي', 'المهندسين', '6 أكتوبر', 'الشيخ زايد'],
                        'الإسكندرية' => ['Alexandria', 'سموحة', 'سيدي جابر', 'العجمي', 'المنتزه'],
                        'المنصورة' => ['Mansoura', 'طلخا', 'ميت غمر'],
                        'طنطا' => ['Tanta', 'قطور', 'كفر الزيات'],
                    ],
                ],
                [
                    'name_ar' => 'الكويت',
                    'name_en' => 'Kuwait',
                    'code' => 'KW',
                    'currency' => 'KWD',
                    'cities' => [
                        'مدينة الكويت' => ['Kuwait City', 'شرق', 'القبلة', 'المرقاب'],
                        'حولي' => ['Hawalli', 'السالمية', 'الرميثية', 'الجابرية'],
                        'الفروانية' => ['Farwaniya', 'خيطان', 'جليب الشيوخ', 'العارضية'],
                        'الأحمدي' => ['Ahmadi', 'الفحيحيل', 'المنقف', 'أبو حليفة'],
                        'الجهراء' => ['Jahra', 'القصر', 'النعيم'],
                    ],
                ],
                [
                    'name_ar' => 'السعودية',
                    'name_en' => 'Saudi Arabia',
                    'code' => 'SA',
                    'currency' => 'SAR',
                    'cities' => [
                        'الرياض' => ['Riyadh', 'الملز', 'العليا', 'النرجس'],
                        'جدة' => ['Jeddah', 'الحمراء', 'الروضة', 'أبحر'],
                        'مكة المكرمة' => ['Makkah', 'العزيزية', 'الشوقية'],
                        'المدينة المنورة' => ['Madinah', 'قباء', 'العوالي'],
                        'الدمام' => ['Dammam', 'الفيصلية', 'الشاطئ'],
                    ],
                ],
                [
                    'name_ar' => 'الإمارات',
                    'name_en' => 'United Arab Emirates',
                    'code' => 'AE',
                    'currency' => 'AED',
                    'cities' => [
                        'دبي' => ['Dubai', 'ديرة', 'بر دبي', 'جبل علي', 'البرشاء'],
                        'أبوظبي' => ['Abu Dhabi', 'العين', 'الظفرة'],
                        'الشارقة' => ['Sharjah', 'خورفكان', 'كلباء'],
                        'عجمان' => ['Ajman', 'النعيمية', 'الراشدية'],
                    ],
                ],
                [
                    'name_ar' => 'قطر',
                    'name_en' => 'Qatar',
                    'code' => 'QA',
                    'currency' => 'QAR',
                    'cities' => [
                        'الدوحة' => ['Doha', 'السد', 'الدفنة', 'الوعب'],
                        'الريان' => ['Al Rayyan', 'الغرافة', 'معيذر'],
                        'الوكرة' => ['Al Wakrah', 'المشاف'],
                        'الخور' => ['Al Khor', 'الذخيرة'],
                    ],
                ],
                [
                    'name_ar' => 'البحرين',
                    'name_en' => 'Bahrain',
                    'code' => 'BH',
                    'currency' => 'BHD',
                    'cities' => [
                        'المنامة' => ['Manama', 'الجفير', 'السيف', 'العدلية'],
                        'المحرق' => ['Muharraq', 'قلالي', 'الحد'],
                        'الرفاع' => ['Riffa', 'الرفاع الشرقي', 'الرفاع الغربي'],
                    ],
                ],
                [
                    'name_ar' => 'عمان',
                    'name_en' => 'Oman',
                    'code' => 'OM',
                    'currency' => 'OMR',
                    'cities' => [
                        'مسقط' => ['Muscat', 'مطرح', 'بوشر', 'السيب'],
                        'صلالة' => ['Salalah', 'عوقد', 'السعادة'],
                        'صحار' => ['Sohar', 'فلج القبائل'],
                    ],
                ],
                [
                    'name_ar' => 'الأردن',
                    'name_en' => 'Jordan',
                    'code' => 'JO',
                    'currency' => 'JOD',
                    'cities' => [
                        'عمان' => ['Amman', 'العبدلي', 'الصويفية', 'خلدا'],
                        'إربد' => ['Irbid', 'الحصن', 'الرمثا'],
                        'العقبة' => ['Aqaba', 'الشامية'],
                        'الزرقاء' => ['Zarqa', 'الرصيفة'],
                    ],
                ],
            ];

            $currencies = Currency::query()->pluck('id', 'code');

            foreach ($countriesData as $countryData) {
                $country = Country::updateOrCreate(
                    ['code' => $countryData['code']],
                    [
                        'name_ar' => $countryData['name_ar'],
                        'name_en' => $countryData['name_en'],
                        'currency_id' => $currencies[$countryData['currency']],
                        'is_active' => true,
                    ]
                );

                foreach ($countryData['cities'] as $cityNameAr => $areasNames) {
                    $cityNameEn = array_shift($areasNames);

                    $city = City::updateOrCreate(
                        [
                            'country_id' => $country->id,
                            'name_ar' => $cityNameAr,
                        ],
                        [
                            'name_en' => $cityNameEn,
                            'is_active' => true,
                        ]
                    );

                    foreach ($areasNames as $areaNameAr) {
                        Area::updateOrCreate(
                            [
                                'city_id' => $city->id,
                                'name_ar' => $areaNameAr,
                            ],
                            [
                                'name_en' => $areaNameAr,
                                'is_active' => true,
                            ]
                        );
                    }
                }
            }
        });
    }
}
