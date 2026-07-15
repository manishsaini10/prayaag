<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::create([
            'name'     => 'Admin',
            'email'    => 'admin@demo.test',
            'password' => Hash::make('secret'),
        ]);
    }

    public function test_login_screen_renders(): void
    {
        $this->get('/login')->assertOk()->assertSee('Sign in');
    }

    public function test_a_user_can_log_in(): void
    {
        $this->post('/login', ['email' => 'admin@demo.test', 'password' => 'secret'])
            ->assertRedirect('/admin');

        $this->assertAuthenticatedAs($this->user);

        $this->assertDatabaseHas('admin_notifications', [
            'type'  => 'login',
            'title' => 'User Admin logged in successfully',
        ]);
    }

    public function test_login_fails_with_a_bad_password(): void
    {
        $this->post('/login', ['email' => 'admin@demo.test', 'password' => 'wrong'])
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_guest_is_redirected_from_the_dashboard(): void
    {
        $this->get('/admin')->assertRedirect('/login');
    }

    public function test_authenticated_user_sees_the_dashboard(): void
    {
        $this->actingAs($this->user)
            ->get('/admin')
            ->assertOk()
            ->assertSee('Dashboard')
            ->assertSee('Overview of content, admissions and site activity');
    }

    public function test_admin_dashboard_loads_built_assets_when_vite_hot_file_exists(): void
    {
        file_put_contents(public_path('hot'), 'http://[::1]:5173');

        $this->actingAs($this->user)
            ->get('/admin')
            ->assertOk()
            ->assertSee('build/assets/app-', false)
            ->assertDontSee('[::1]:5173', false);
    }

    public function test_a_user_can_log_out(): void
    {
        $this->actingAs($this->user)
            ->post('/logout')
            ->assertRedirect('/login');

        $this->assertGuest();
    }
}
