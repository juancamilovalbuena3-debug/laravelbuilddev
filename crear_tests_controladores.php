<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ControladoresTest extends TestCase
{
    use RefreshDatabase;

    private function usuario()
    {
        return User::factory()->create();
    }

    // ── VehiculoController ──────────────────────────────────────────

    public function test_carros_requiere_autenticacion(): void
    {
        $response = $this->get('/carros');
        $response->assertRedirect('/login');
    }

    public function test_motos_requiere_autenticacion(): void
    {
        $response = $this->get('/motos');
        $response->assertRedirect('/login');
    }

    public function test_carros_autenticado_accesible(): void
    {
        $user = $this->usuario();
        $response = $this->actingAs($user)->get('/carros');
        $response->assertStatus(200);
    }

    public function test_motos_autenticado_accesible(): void
    {
        $user = $this->usuario();
        $response = $this->actingAs($user)->get('/motos');
        $response->assertStatus(200);
    }

    public function test_vehiculos_requiere_autenticacion(): void
    {
        $response = $this->get('/vehiculos');
        $response->assertRedirect('/login');
    }

    public function test_vehiculos_create_requiere_autenticacion(): void
    {
        $response = $this->get('/vehiculos/create');
        $response->assertRedirect('/login');
    }

    public function test_reportes_requiere_autenticacion(): void
    {
        $response = $this->get('/reportes');
        $response->assertRedirect('/login');
    }

    public function test_detalle_carro_requiere_autenticacion(): void
    {
        $response = $this->get('/carros/1/detalle');
        $response->assertRedirect('/login');
    }

    public function test_detalle_moto_requiere_autenticacion(): void
    {
        $response = $this->get('/motos/1/detalle');
        $response->assertRedirect('/login');
    }

    public function test_comprar_carro_requiere_autenticacion(): void
    {
        $response = $this->get('/carros/1/comprar');
        $response->assertRedirect('/login');
    }

    public function test_comprar_moto_requiere_autenticacion(): void
    {
        $response = $this->get('/motos/1/comprar');
        $response->assertRedirect('/login');
    }

    // ── EmpleadoController ──────────────────────────────────────────

    public function test_empleados_requiere_autenticacion(): void
    {
        $response = $this->get('/empleados');
        $response->assertRedirect('/login');
    }

    public function test_empleados_create_requiere_autenticacion(): void
    {
        $response = $this->get('/empleados/create');
        $response->assertRedirect('/login');
    }

    public function test_empleados_store_requiere_autenticacion(): void
    {
        $response = $this->post('/empleados', [
            'nombre'  => 'Test',
            'puesto'  => 'Vendedor',
            'salario' => 2000000,
            'email'   => 'test@test.com',
        ]);
        $response->assertRedirect('/login');
    }

    // ── ConfiguracionController ─────────────────────────────────────

    public function test_configuracion_requiere_autenticacion(): void
    {
        $response = $this->get('/configuracion');
        $response->assertRedirect('/login');
    }

    public function test_configuracion_autenticado(): void
    {
        $user = $this->usuario();
        $response = $this->actingAs($user)->get('/configuracion');
        $response->assertStatus(200);
    }

    // ── PaymentController ───────────────────────────────────────────

    public function test_pagar_requiere_autenticacion(): void
    {
        $response = $this->get('/pagar');
        $response->assertRedirect('/login');
    }

    public function test_pagar_sin_stripe_retorna_error(): void
    {
        $user = $this->usuario();
        $response = $this->actingAs($user)->get('/pagar');
        $response->assertStatus(500);
    }

    // ── VenderController ────────────────────────────────────────────

    public function test_vender_requiere_autenticacion(): void
    {
        $response = $this->get('/vender');
        $response->assertRedirect('/login');
    }

    public function test_vender_autenticado_retorna_error_sin_microservicio(): void
    {
        $user = $this->usuario();
        $response = $this->actingAs($user)->get('/vender');
        $response->assertStatus(500);
    }

    // ── Dashboard ───────────────────────────────────────────────────

    public function test_dashboard_requiere_autenticacion(): void
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
    }

    public function test_dashboard_autenticado(): void
    {
        $user = $this->usuario();
        $response = $this->actingAs($user)->get('/dashboard');
        $response->assertStatus(200);
    }
}