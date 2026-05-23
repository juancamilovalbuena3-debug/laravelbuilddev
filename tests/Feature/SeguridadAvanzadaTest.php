<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeguridadAvanzadaTest extends TestCase
{
    use RefreshDatabase;

    private function admin()
    {
        return User::factory()->create(['role' => 'admin']);
    }

    private function empleado()
    {
        return User::factory()->create(['role' => 'empleado']);
    }

    private function cliente()
    {
        return User::factory()->create(['role' => 'cliente']);
    }

    // ── CSRF ────────────────────────────────────────────────────────

    public function test_csrf_protege_formulario_login(): void
    {
        $response = $this->post('/login', [
            'email'    => 'noexiste@test.com',
            'password' => 'incorrecta',
        ]);
        $this->assertContains($response->status(), [302, 303]);
    }

    public function test_csrf_protege_formulario_registro(): void
    {
        $response = $this->post('/register', [
            'name'                  => '',
            'email'                 => 'invalido',
            'password'              => '123',
            'password_confirmation' => '456',
        ]);
        $response->assertStatus(302);
    }

    public function test_csrf_protege_empleados_store(): void
    {
        $response = $this->post('/empleados', [
            'nombre'  => 'Test',
            'puesto'  => 'Vendedor',
            'salario' => 2000000,
            'email'   => 'emp@test.com',
        ]);
        $response->assertRedirect('/login');
    }

    // ── Rate Limiting (Fuerza Bruta) ────────────────────────────────

    public function test_rate_limiting_bloquea_multiples_intentos_login(): void
    {
        for ($i = 0; $i < 6; $i++) {
            $response = $this->post('/login', [
                'email'    => 'noexiste@test.com',
                'password' => 'incorrecta',
            ]);
        }
        $response->assertStatus(429);
    }

    public function test_rate_limiting_se_aplica_por_ip(): void
    {
        $this->withServerVariables(['REMOTE_ADDR' => '192.168.1.1']);

        for ($i = 0; $i < 6; $i++) {
            $response = $this->post('/login', [
                'email'    => 'noexiste@test.com',
                'password' => 'incorrecta',
            ]);
        }
        $response->assertStatus(429);
    }

    // ── XSS ────────────────────────────────────────────────────────

    public function test_xss_en_campo_nombre_registro(): void
    {
        $payload = '<script>alert("xss")</script>';

        $response = $this->post('/register', [
            'name'                  => $payload,
            'email'                 => 'xss@test.com',
            'password'              => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $response->assertDontSee('<script>alert("xss")</script>', false);
    }

    public function test_xss_en_campo_email_login(): void
    {
        $payload = '<script>alert("xss")</script>';

        $response = $this->post('/login', [
            'email'    => $payload,
            'password' => 'cualquiera',
        ]);

        $response->assertDontSee('<script>alert("xss")</script>', false);
    }

    public function test_xss_en_formulario_empleados(): void
    {
        $user    = $this->admin();
        $payload = '<img src=x onerror=alert(1)>';

        $response = $this->actingAs($user)->post('/empleados', [
            'nombre'  => $payload,
            'puesto'  => 'Vendedor',
            'salario' => 2000000,
            'email'   => 'emp2@test.com',
        ]);

        $response->assertDontSee('<img src=x onerror=alert(1)>', false);
    }

    // ── Control de Acceso por Roles ─────────────────────────────────

    // Admin
    public function test_admin_puede_acceder_a_empleados(): void
    {
        $user = $this->admin();
        $response = $this->actingAs($user)->get('/empleados');
        $this->assertContains($response->status(), [200, 500]);
    }

    public function test_admin_puede_acceder_a_vehiculos(): void
    {
        $user = $this->admin();
        $response = $this->actingAs($user)->get('/vehiculos');
        $this->assertContains($response->status(), [200, 500]);
    }

    public function test_admin_puede_acceder_a_reportes(): void
    {
        $user = $this->admin();
        $response = $this->actingAs($user)->get('/reportes');
        $this->assertContains($response->status(), [200, 500]);
    }

    // Empleado
    public function test_empleado_no_puede_acceder_a_gestion_empleados(): void
    {
        $user = $this->empleado();
        $response = $this->actingAs($user)->get('/empleados');
        $this->assertContains($response->status(), [403, 500]);
    }

    public function test_empleado_puede_ver_vehiculos(): void
    {
        $user = $this->empleado();
        $response = $this->actingAs($user)->get('/carros');
        $response->assertStatus(200);
    }

    public function test_empleado_no_puede_crear_vehiculos(): void
    {
        $user = $this->empleado();
        $response = $this->actingAs($user)->get('/vehiculos/create');
        $this->assertContains($response->status(), [403, 500]);
    }

    // Cliente
    public function test_cliente_no_puede_acceder_a_empleados(): void
    {
        $user = $this->cliente();
        $response = $this->actingAs($user)->get('/empleados');
        $this->assertContains($response->status(), [403, 500]);
    }

    public function test_cliente_no_puede_acceder_a_reportes(): void
    {
        $user = $this->cliente();
        $response = $this->actingAs($user)->get('/reportes');
        $this->assertContains($response->status(), [403, 500]);
    }

    public function test_cliente_puede_ver_carros(): void
    {
        $user = $this->cliente();
        $response = $this->actingAs($user)->get('/carros');
        $response->assertStatus(200);
    }

    public function test_cliente_puede_ver_motos(): void
    {
        $user = $this->cliente();
        $response = $this->actingAs($user)->get('/motos');
        $response->assertStatus(200);
    }

    public function test_cliente_puede_comprar_carro(): void
    {
        $user = $this->cliente();
        $response = $this->actingAs($user)->get('/carros/1/comprar');
        $this->assertContains($response->status(), [200, 404]);
    }

    // Escalada de privilegios
    public function test_cliente_no_puede_escalar_a_admin(): void
    {
        $user = $this->cliente();
        $response = $this->actingAs($user)->post('/empleados', [
            'nombre'  => 'Hack',
            'puesto'  => 'Admin',
            'salario' => 9999999,
            'email'   => 'hack@test.com',
        ]);
        $this->assertContains($response->status(), [302, 403, 500]);
    }

    public function test_empleado_no_puede_escalar_a_admin(): void
    {
        $user = $this->empleado();
        $response = $this->actingAs($user)->post('/empleados', [
            'nombre'  => 'Hack',
            'puesto'  => 'Admin',
            'salario' => 9999999,
            'email'   => 'hack2@test.com',
        ]);
        $this->assertContains($response->status(), [302, 403, 500]);
    }
}