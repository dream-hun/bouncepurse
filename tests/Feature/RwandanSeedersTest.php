<?php

declare(strict_types=1);

use App\Enums\Role;
use App\Models\Country;
use App\Models\Court;
use App\Models\Game;
use App\Models\Profile;
use App\Models\User;
use Database\Seeders\AllocationConfigurationSeeder;
use Database\Seeders\CountrySeeder;
use Database\Seeders\CourtSeeder;
use Database\Seeders\GameSeeder;
use Database\Seeders\PlayerSeeder;
use Database\Seeders\RankingConfigurationSeeder;
use Database\Seeders\RolesAndPermissionsSeeder;
use Database\Seeders\TeamSeeder;

test('courts players and games use Rwandan sample names and locations', function (): void {
    $this->seed([
        CountrySeeder::class,
        RolesAndPermissionsSeeder::class,
        RankingConfigurationSeeder::class,
        AllocationConfigurationSeeder::class,
    ]);
    User::factory()->create()->assignRole(Role::Administrator->value);
    User::factory()->create()->assignRole(Role::Moderator->value);

    $this->seed([PlayerSeeder::class, CourtSeeder::class, TeamSeeder::class, GameSeeder::class]);

    $rwanda = Country::query()->where('iso_code', 'RWA')->firstOrFail();
    $this->assertDatabaseCount('courts', 15);
    $this->assertDatabaseCount('profiles', 33);

    expect(Court::query()->where('country_id', '!=', $rwanda->id)->count())->toBe(0);
    expect(Profile::query()->where('country_id', '!=', $rwanda->id)->count())->toBe(0);
    expect(Profile::query()->whereNotIn('city', ['Kigali', 'Huye', 'Musanze', 'Rubavu', 'Muhanga', 'Rusizi'])->count())->toBe(0);
    expect(Court::query()->whereNotIn('name', [
        'Amahoro', 'Nyamirambo', 'Kimisagara', 'Ngoma', 'Tumba', 'Matyazo',
        'Muhoza', 'Cyuve', 'Kimonyi', 'Gisenyi', 'Rugero', 'Umuganda',
        'Gitarama', 'Nyamabuye', 'Shyogwe',
    ])->count())->toBe(0);

    foreach (User::query()->role(Role::Player->value)->pluck('name') as $name) {
        expect($name)->toMatch('/^(Mugisha|Nkurunziza|Hakizimana|Niyonzima|Uwase|Mukamana|Ishimwe|Irakoze) (James|Jordan|Maya|Luis|Grace|Samuel|David|Alice)$/');
    }

    foreach (Court::query()->pluck('host_name') as $name) {
        expect($name)->toMatch('/^(Mugisha|Nkurunziza|Hakizimana|Niyonzima|Uwase|Mukamana|Ishimwe|Irakoze|Uwamahoro|Nsengimana|Nyiransabimana|Ndayisaba|Munyaneza|Umutoni|Mukarurangwa) (Amahoro|Shema|Ineza|Ganza|Keza|Hirwa|Mugabo|Umutoni)$/');
    }

    expect(Game::query()->count())->toBeGreaterThan(0);
    expect(Game::query()->whereNotIn('title', [
        'Amahoro', 'Ubumwe', 'Imena', 'Intwari', 'Imihigo', 'Agaciro', 'Ishema', 'Ihuriro',
    ])->count())->toBe(0);

    $this->assertDatabaseHas('users', ['email' => 'player@bouncepurse.test', 'name' => 'Mugisha James']);
    $this->assertDatabaseHas('users', ['email' => 'minor@bouncepurse.test', 'name' => 'Ishimwe Grace']);
    $this->assertDatabaseHas('users', ['email' => 'deactivated@bouncepurse.test', 'name' => 'Niyonzima David']);
});
