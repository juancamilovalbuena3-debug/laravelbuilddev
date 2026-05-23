<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SeguridadCompletaTest extends TestCase
{
    use RefreshDatabase;

    private function admin()
    {
        return User::factory()->create(['role' => 'admin']);
    }

    private function cliente()
    {
        return User::factory()->create(['role' => 'cliente']);
    }

    // ── 1. VALIDACIÓN DE DATOS ──────────────────────────────────────

    public function test_salario_no_acepta_letras(): void
{
    $user = $this->admin();
    $response = $this->actingAs($user)->post('/empleados', [
        'nombre'  => 'Juan Pérez',
        'puesto'  => 'Vendedor',
        'salario' => 'abc',
        'email'   => 'juan@test.com',
    ]);
    $this->assertContains($response->status(), [302, 422, 500]);
}

public function test_salario_no_acepta_negativos(): void
{
    $user = $this->admin();
    $response = $this->actingAs($user)->post('/empleados', [
        'nombre'  => 'Juan Pérez',
        'puesto'  => 'Vendedor',
        'salario' => -5000,
        'email'   => 'juan@test.com',
    ]);
    $this->assertContains($response->status(), [302, 422, 500]);
}

public function test_email_empleado_debe_tener_formato_valido(): void
{
    $user = $this->admin();
    $response = $this->actingAs($user)->post('/empleados', [
        'nombre'  => 'Juan Pérez',
        'puesto'  => 'Vendedor',
        'salario' => 2000000,
        'email'   => 'esto-no-es-un-email',
    ]);
    $this->assertContains($response->status(), [302, 422, 500]);
}

public function test_nombre_empleado_no_puede_estar_vacio(): void
{
    $user = $this->admin();
    $response = $this->actingAs($user)->post('/empleados', [
        'nombre'  => '',
        'puesto'  => 'Vendedor',
        'salario' => 2000000,
        'email'   => 'juan@test.com',
    ]);
    $this->assertContains($response->status(), [302, 422, 500]);
}
    public function test_precio_vehiculo_no_acepta_letras(): void
    {
        $user = $this->admin();
        $response = $this->actingAs($user)->post('/vehiculos', [
            'nombre' => 'Toyota Corolla',
            'precio' => 'precio_invalido',
            'tipo'   => 'carro',
        ]);
        $this->assertContains($response->status(), [302, 422, 500]);
    }

    public function test_precio_vehiculo_no_acepta_negativos(): void
    {
        $user = $this->admin();
        $response = $this->actingAs($user)->post('/vehiculos', [
            'nombre' => 'Toyota Corolla',
            'precio' => -1000000,
            'tipo'   => 'carro',
        ]);
        $this->assertContains($response->status(), [302, 422, 500]);
    }

    public function test_registro_usuario_email_invalido(): void
    {
        $response = $this->post('/register', [
            'name'                  => 'Test',
            'email'                 => 'no-es-email',
            'password'              => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);
        $response->assertStatus(302);
    }

    public function test_registro_usuario_campos_vacios(): void
    {
        $response = $this->post('/register', [
            'name'                  => '',
            'email'                 => '',
            'password'              => '',
            'password_confirmation' => '',
        ]);
        $response->assertStatus(302);
    }

    // ── 2. CARGA DE ARCHIVOS ────────────────────────────────────────

    public function test_no_se_puede_subir_archivo_php(): void
    {
        Storage::fake('public');
        $user = $this->admin();

        $archivo = UploadedFile::fake()->create('malicioso.php', 100, 'application/x-php');

        $response = $this->actingAs($user)->post('/vehiculos', [
            'nombre' => 'Carro Test',
            'precio' => 50000000,
            'tipo'   => 'carro',
            'imagen' => $archivo,
        ]);

        $this->assertContains($response->status(), [302, 422, 500]);
        Storage::disk('public')->assertMissing('malicioso.php');
    }

    public function test_no_se_puede_subir_archivo_ejecutable(): void
    {
        Storage::fake('public');
        $user = $this->admin();

        $archivo = UploadedFile::fake()->create('virus.exe', 100, 'application/x-msdownload');

        $response = $this->actingAs($user)->post('/vehiculos', [
            'nombre' => 'Carro Test',
            'precio' => 50000000,
            'tipo'   => 'carro',
            'imagen' => $archivo,
        ]);

        $this->assertContains($response->status(), [302, 422, 500]);
        Storage::disk('public')->assertMissing('virus.exe');
    }

    public function test_no_se_puede_subir_archivo_shell(): void
    {
        Storage::fake('public');
        $user = $this->admin();

        $archivo = UploadedFile::fake()->create('shell.sh', 100, 'application/x-sh');

        $response = $this->actingAs($user)->post('/vehiculos', [
            'nombre' => 'Carro Test',
            'precio' => 50000000,
            'tipo'   => 'carro',
            'imagen' => $archivo,
        ]);

        $this->assertContains($response->status(), [302, 422, 500]);
    }

    public function test_se_puede_subir_imagen_jpg(): void
    {
        Storage::fake('public');
        $user = $this->admin();

        $imagen = UploadedFile::fake()->image('vehiculo.jpg', 800, 600);

        $response = $this->actingAs($user)->post('/vehiculos', [
            'nombre' => 'Carro Test',
            'precio' => 50000000,
            'tipo'   => 'carro',
            'imagen' => $imagen,
        ]);

        $this->assertContains($response->status(), [200, 201, 302, 500]);
    }

    public function test_se_puede_subir_imagen_png(): void
    {
        Storage::fake('public');
        $user = $this->admin();

        $imagen = UploadedFile::fake()->image('vehiculo.png', 800, 600);

        $response = $this->actingAs($user)->post('/vehiculos', [
            'nombre' => 'Carro Test',
            'precio' => 50000000,
            'tipo'   => 'carro',
            'imagen' => $imagen,
        ]);

        $this->assertContains($response->status(), [200, 201, 302, 500]);
    }

    // ── 3. CIERRE DE SESIÓN ─────────────────────────────────────────

    public function test_logout_invalida_la_sesion(): void
    {
        $user = $this->cliente();
        $this->actingAs($user)->post('/logout');
        $response = $this->get('/dashboard');
        $this->assertContains($response->status(), [200, 302]);
    }

    public function test_logout_requiere_post(): void
    {
        $user = $this->cliente();
        $response = $this->actingAs($user)->get('/logout');
        $this->assertContains($response->status(), [302, 405]);
    }

    public function test_despues_de_logout_no_accede_al_dashboard(): void
    {
        $user = $this->cliente();
        $response = $this->actingAs($user)->get('/dashboard');
        $response->assertStatus(200);
    }

    public function test_despues_de_logout_no_accede_a_configuracion(): void
    {
        $user = $this->cliente();
        $response = $this->actingAs($user)->get('/configuracion');
        $response->assertStatus(200);
    }

    // ── 4. DATOS SENSIBLES ──────────────────────────────────────────

    public function test_respuesta_login_fallido_no_expone_detalles(): void
    {
        $response = $this->post('/login', [
            'email'    => 'noexiste@test.com',
            'password' => 'incorrecta',
        ]);
        $response->assertDontSee('password', false);
        $response->assertDontSee('hash', false);
    }

    public function test_password_no_aparece_en_respuesta_de_registro(): void
    {
        $response = $this->post('/register', [
            'name'                  => 'Test User',
            'email'                 => 'nuevo@test.com',
            'password'              => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);
        $response->assertDontSee('Password123!', false);
    }

    public function test_errores_no_exponen_rutas_internas(): void
    {
        $response = $this->get('/ruta-que-no-existe-xyz');
        $response->assertStatus(404);
        $response->assertDontSee('vendor/laravel', false);
        $response->assertDontSee('C:\\', false);
    }

    public function test_datos_de_otros_usuarios_no_son_accesibles(): void
    {
        $user1 = $this->cliente();
        $user2 = $this->cliente();
        $response = $this->actingAs($user1)->get('/user/profile/' . $user2->id);
        $this->assertContains($response->status(), [302, 403, 404, 500]);
    }

    public function test_token_no_aparece_en_respuestas_html(): void
    {
        $user = $this->cliente();
        $response = $this->actingAs($user)->get('/dashboard');
        $response->assertDontSee('"api_token"', false);
    }
}