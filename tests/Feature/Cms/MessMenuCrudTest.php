<?php

namespace Tests\Feature\Cms;

use App\Models\Mess\MessMenu;
use App\Models\Mess\MessMenuItem;
use App\Models\Mess\MessMenuSpecialDay;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MessMenuCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_mess_menu(): void
    {
        $admin = User::factory()->create(['two_factor_enabled' => true]);
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)
                         ->withSession(['2fa_passed' => true])
                         ->post(route('admin.mess-menus.store'), [
                             'title'          => 'Aug 2026 Special Menu',
                             'effective_from' => '2026-08-01',
                             'effective_to'   => '2026-08-15',
                             'is_active'      => '1',
                         ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('mess_menus', [
            'title'     => 'Aug 2026 Special Menu',
            'is_active' => true,
        ]);
    }

    public function test_public_mess_menu_page_displays_active_menu(): void
    {
        $menu = MessMenu::create([
            'title'          => 'Weekly Menu',
            'effective_from' => '2026-08-01',
            'is_active'      => true,
        ]);

        MessMenuItem::create([
            'mess_menu_id' => $menu->id,
            'day_of_week'  => 'monday',
            'meal_type'    => 'breakfast',
            'items'        => ['Poha', 'Tea', 'Milk'],
        ]);

        $response = $this->get('/mess-menu');

        $response->assertStatus(200);
        $response->assertSee('Weekly Menu');
        $response->assertSee('Poha');
    }

    public function test_public_mess_menu_pdf_download_returns_pdf_stream(): void
    {
        $menu = MessMenu::create([
            'title'          => 'Weekly Menu PDF Test',
            'effective_from' => '2026-08-01',
            'is_active'      => true,
        ]);

        $response = $this->get('/mess-menu/pdf');

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
    }
}
