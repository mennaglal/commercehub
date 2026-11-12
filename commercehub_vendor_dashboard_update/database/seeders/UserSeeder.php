<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Country;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use RuntimeException;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Get active countries with active cities and areas.
        $countries = Country::query()
            ->where('is_active', true)
            ->whereHas('cities', function ($query) {
                $query->where('is_active', true)
                    ->whereHas('areas', function ($query) {
                        $query->where('is_active', true);
                    });
            })
            ->with([
                'cities' => function ($query) {
                    $query->where('is_active', true)
                        ->with([
                            'areas' => function ($query) {
                                $query->where('is_active', true);
                            }
                        ]);
                }
            ])
            ->get();

        if ($countries->isEmpty()) {
            throw new RuntimeException(
                'No active country, city, and area combinations found.'
            );
        }

        // Get existing customers.
        $customers = User::role('customer')->get();

        // Update existing customers with missing information.
        foreach ($customers as $customer) {
            $data = [];

            if (empty($customer->phone)) {
                $data['phone'] = fake()->numerify('########');
            }

            if (
                empty($customer->country_id) ||
                empty($customer->city_id) ||
                empty($customer->area_id)
            ) {
                $location = $this->getRandomLocation($countries);

                $data['country_id'] = $location['country_id'];
                $data['city_id'] = $location['city_id'];
                $data['area_id'] = $location['area_id'];
            }

            if (!empty($data)) {
                $customer->update($data);
            }
        }

        // Create customers until we reach 20.
        $remaining = max(0, 20 - $customers->count());

        for ($i = 0; $i < $remaining; $i++) {
            $location = $this->getRandomLocation($countries);

            $customer = User::factory()->create([
                'phone' => fake()->numerify('########'),
                'country_id' => $location['country_id'],
                'city_id' => $location['city_id'],
                'area_id' => $location['area_id'],
            ]);

            $customer->assignRole('customer');
        }

        $this->command->info(
            "User seeder completed. {$customers->count()} existing customers updated, {$remaining} new customers created."
        );
    }

    private function getRandomLocation(Collection $countries): array
    {
        $country = $countries->random();

        $city = $country->cities->random();

        $area = $city->areas->random();

        return [
            'country_id' => $country->id,
            'city_id' => $city->id,
            'area_id' => $area->id,
        ];
    }
}
