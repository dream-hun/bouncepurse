<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\Country;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

final class PlayerSeeder extends Seeder
{
    public function run(): void
    {
        /** @var Collection<int, int> $countryIds */
        $countryIds = Country::query()->where('iso_code', 'RWA')->pluck('id');

        $rwandanNames = ['Mugisha', 'Nkurunziza', 'Hakizimana', 'Niyonzima', 'Uwase', 'Mukamana', 'Ishimwe', 'Irakoze'];
        $givenNames = ['James', 'Jordan', 'Maya', 'Luis', 'Grace', 'Samuel', 'David', 'Alice'];

        $adultPlayers = [
            ['name' => 'Mugisha James', 'email' => 'player@bouncepurse.test', 'password' => DemoCredentials::PASSWORD],
            ['name' => 'Nkurunziza Jordan', 'email' => 'jordan@bouncepurse.test', 'password' => DemoCredentials::PASSWORD],
            ['name' => 'Uwase Maya', 'email' => 'maya@bouncepurse.test', 'password' => DemoCredentials::PASSWORD],
            ['name' => 'Hakizimana Luis', 'email' => 'luis@bouncepurse.test', 'password' => DemoCredentials::PASSWORD],
        ];

        foreach ($adultPlayers as $data) {
            $this->createPlayer($data, $countryIds, fake()->dateTimeBetween('-35 years', '-18 years')->format('Y-m-d'));
        }

        $minorPlayers = [
            ['name' => 'Ishimwe Grace', 'email' => 'minor@bouncepurse.test', 'password' => DemoCredentials::PASSWORD],
            ['name' => 'Irakoze Samuel', 'email' => 'minor2@bouncepurse.test', 'password' => DemoCredentials::PASSWORD],
        ];

        foreach ($minorPlayers as $data) {
            $this->createPlayer($data, $countryIds, fake()->dateTimeBetween('-17 years', '-14 years')->format('Y-m-d'));
        }

        for ($i = 0; $i < 24; $i++) {
            $this->createPlayer(
                [
                    'name' => $rwandanNames[array_rand($rwandanNames)].' '.$givenNames[array_rand($givenNames)],
                    'email' => fake()->unique()->safeEmail(),
                ],
                $countryIds,
                fake()->dateTimeBetween('-40 years', '-18 years')->format('Y-m-d'),
            );
        }

        for ($i = 0; $i < 2; $i++) {
            $this->createPlayer(
                [
                    'name' => $rwandanNames[array_rand($rwandanNames)].' '.$givenNames[array_rand($givenNames)],
                    'email' => fake()->unique()->safeEmail(),
                ],
                $countryIds,
                fake()->dateTimeBetween('-17 years', '-13 years')->format('Y-m-d'),
            );
        }

        $admin = User::query()->role(Role::Administrator->value)->first();

        $deactivated = $this->createPlayer(
            [
                'name' => 'Niyonzima David',
                'email' => 'deactivated@bouncepurse.test',
                'password' => DemoCredentials::PASSWORD,
            ],
            $countryIds,
            fake()->dateTimeBetween('-30 years', '-18 years')->format('Y-m-d'),
        );

        $deactivated->update([
            'deactivated_at' => now(),
            'deactivated_by' => $admin?->id,
            'deactivation_reason' => 'Sample account deactivated for local development.',
        ]);
    }

    /**
     * @param  array{name: string, email: string, password?: string}  $data
     * @param  Collection<int, int>  $countryIds
     */
    private function createPlayer(array $data, Collection $countryIds, string $dateOfBirth): User
    {
        $user = User::factory()->create($data)->assignRole(Role::Player->value);

        Profile::factory()->create([
            'player_id' => $user->id,
            'country_id' => $countryIds->random(),
            'city' => fake()->randomElement(['Kigali', 'Huye', 'Musanze', 'Rubavu', 'Muhanga', 'Rusizi']),
            'date_of_birth' => $dateOfBirth,
        ]);

        return $user;
    }
}
