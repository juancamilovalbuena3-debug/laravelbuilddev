<?php
namespace Tests\Feature;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
class MotrixTest extends TestCase
{
    use RefreshDatabase;
    public function test_pagina_principal_carga(): void
    {
        $response = $this->get("/");
        $response->assertStatus(200);
    }
    public function test_login_con_credenciales_invalidas(): void
    {
        $response = $this->post("/login", [
            "email" => "noexiste@test.com",
            "password" => "wrongpassword",
        ]);
        $response->assertSessionHasErrors();
    }
    public function test_usuario_autenticado_accede_al_dashboard(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get("/dashboard");
        $response->assertStatus(200);
    }
    public function test_usuario_no_autenticado_redirige_al_login(): void
    {
        $response = $this->get("/dashboard");
        $response->assertRedirect("/login");
    }
    public function test_registro_con_datos_validos(): void
    {
        $response = $this->post("/register", [
            "name" => "Test User",
            "email" => "test@motrix.com",
            "password" => "password123",
            "password_confirmation" => "password123",
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas("users", ["email" => "test@motrix.com"]);
    }
    public function test_registro_con_email_invalido(): void
    {
        $response = $this->post("/register", [
            "name" => "Test",
            "email" => "no-es-email",
            "password" => "password123",
            "password_confirmation" => "password123",
        ]);
        $response->assertSessionHasErrors(["email"]);
    }
}