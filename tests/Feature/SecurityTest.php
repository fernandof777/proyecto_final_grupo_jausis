<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Cliente;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_security_headers_are_present(): void
    {
        $response = $this->get(route('login'));

        $response->assertOk();
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('X-Frame-Options', 'DENY');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
        $this->assertStringContainsString("frame-ancestors 'none'", $response->headers->get('Content-Security-Policy'));
    }

    public function test_login_is_temporarily_blocked_after_repeated_failures(): void
    {
        $user = User::factory()->create(['password' => 'password']);

        for ($attempt = 0; $attempt < 5; $attempt++) {
            $this->post(route('login'), ['email' => $user->email, 'password' => 'incorrecta']);
        }

        $this->post(route('login'), ['email' => $user->email, 'password' => 'password'])
            ->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_inactive_user_is_logged_out(): void
    {
        $user = User::factory()->create(['activo' => false]);

        $this->actingAs($user)->get(route('dashboard'))
            ->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_reports_require_admin_role(): void
    {
        $recepcionista = User::factory()->create(['role' => 'recepcionista']);
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($recepcionista)->get(route('reportes.index'))->assertForbidden();
        $this->actingAs($recepcionista)->get(route('reportes.pdf'))->assertForbidden();
        $this->actingAs($admin)->get(route('reportes.index'))->assertOk();
        $this->actingAs($admin)->get(route('reportes.pdf'))->assertOk();
    }

    public function test_changes_are_audited_without_sensitive_password_data(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $cliente = $this->actingAs($admin)->post(route('clientes.store'), [
            'nombre' => 'Cliente auditado',
            'ci_nit' => 'AUD-1',
            'telefono' => '70000000',
            'email' => null,
            'ciudad' => 'Santa Cruz',
            'direccion' => null,
            'activo' => true,
        ]);

        $cliente->assertRedirect(route('clientes.index'));
        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $admin->id,
            'event' => 'created',
            'auditable_type' => Cliente::class,
        ]);
        $this->assertStringNotContainsString(
            'password',
            json_encode(AuditLog::latest()->firstOrFail()->changes, JSON_THROW_ON_ERROR)
        );
    }

    public function test_passwords_are_hashed(): void
    {
        $user = User::factory()->create(['password' => 'una-clave-segura']);

        $this->assertNotSame('una-clave-segura', $user->password);
        $this->assertTrue(Hash::check('una-clave-segura', $user->password));
    }
}
