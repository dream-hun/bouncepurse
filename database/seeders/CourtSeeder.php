<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\Country;
use App\Models\Court;
use App\Models\User;
use Illuminate\Database\Seeder;

final class CourtSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::query()->role(Role::Administrator->value)->firstOrFail();

        $courts = [
            ['country' => 'Rwanda', 'city' => 'Kigali', 'name' => 'Amahoro', 'host_name' => 'Mugisha Shema', 'status' => 'active'],
            ['country' => 'Rwanda', 'city' => 'Kigali', 'name' => 'Nyamirambo', 'host_name' => 'Uwase Ineza', 'status' => 'pilot'],
            ['country' => 'Rwanda', 'city' => 'Kigali', 'name' => 'Kimisagara', 'host_name' => 'Hakizimana Ganza', 'status' => 'priority'],
            ['country' => 'Rwanda', 'city' => 'Huye', 'name' => 'Ngoma', 'host_name' => 'Mukamana Keza', 'status' => 'active'],
            ['country' => 'Rwanda', 'city' => 'Huye', 'name' => 'Tumba', 'host_name' => 'Niyonzima Hirwa', 'status' => 'pilot'],
            ['country' => 'Rwanda', 'city' => 'Huye', 'name' => 'Matyazo', 'host_name' => 'Ishimwe Amahoro', 'status' => 'priority'],
            ['country' => 'Rwanda', 'city' => 'Musanze', 'name' => 'Muhoza', 'host_name' => 'Irakoze Mugabo', 'status' => 'active'],
            ['country' => 'Rwanda', 'city' => 'Musanze', 'name' => 'Cyuve', 'host_name' => 'Nkurunziza Shema', 'status' => 'pilot'],
            ['country' => 'Rwanda', 'city' => 'Musanze', 'name' => 'Kimonyi', 'host_name' => 'Uwamahoro Ineza', 'status' => 'priority'],
            ['country' => 'Rwanda', 'city' => 'Rubavu', 'name' => 'Gisenyi', 'host_name' => 'Nsengimana Ganza', 'status' => 'active'],
            ['country' => 'Rwanda', 'city' => 'Rubavu', 'name' => 'Rugero', 'host_name' => 'Nyiransabimana Keza', 'status' => 'pilot'],
            ['country' => 'Rwanda', 'city' => 'Rubavu', 'name' => 'Umuganda', 'host_name' => 'Ndayisaba Hirwa', 'status' => 'priority'],
            ['country' => 'Rwanda', 'city' => 'Muhanga', 'name' => 'Gitarama', 'host_name' => 'Munyaneza Mugabo', 'status' => 'active'],
            ['country' => 'Rwanda', 'city' => 'Muhanga', 'name' => 'Nyamabuye', 'host_name' => 'Umutoni Amahoro', 'status' => 'pilot'],
            ['country' => 'Rwanda', 'city' => 'Muhanga', 'name' => 'Shyogwe', 'host_name' => 'Mukarurangwa Ineza', 'status' => 'priority'],
        ];

        foreach ($courts as $data) {
            $country = Country::query()->firstWhere('name', $data['country'])
                ?? Country::factory()->create(['name' => $data['country']]);

            $factory = Court::factory()->{$data['status']}();

            $factory->create([
                'name' => $data['name'],
                'host_name' => $data['host_name'],
                'country_id' => $country->id,
                'city' => $data['city'],
                'court_code' => Court::generateCourtCode($country, $data['city']),
                'created_by' => $admin->id,
            ]);
        }
    }
}
