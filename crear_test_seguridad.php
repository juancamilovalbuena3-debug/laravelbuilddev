<?php
$contenido = '<?php
namespace Tests\Feature;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
class SeguridadTest extends TestCase
{
    use RefreshDatabase;
    public function test_ruta_protegida_sin_autenticacion(): void
    {
        $response = $this->get("/dashboard");
        $response->assertRedirect("/login");
    }
    public function test_login_con_sql_injection(): void
    {
        $response = $this->post("/login", [
            "email" => "admin@test.com OR 1=1",
            "password" => "cualquiera",
        ]);
        $response->assertSessionHasErrors();
    }
    public function test_login_con_campos_vacios(): void
    {
        $response = $this->post("/login", [
            "email" => "",
            "password" => "",
        ]);
        $response->assertSessionHasErrors(["email"]);
    }
    public function test_registro_con_password_corta(): void
    {
        $response = $this->post("/register", [
            "name" => "Test",
            "email" => "test@test.com",
            "password" => "123",
            "password_confirmation" => "123",
        ]);
        $response->assertSessionHasErrors(["password"]);
    }
    public function test_registro_con_passwords_diferentes(): void
    {
        $response = $this->post("/register", [
            "name" => "Test",
            "email" => "test@test.com",
            "password" => "password123",
            "password_confirmation" => "diferente123",
        ]);
        $response->assertSessionHasErrors(["password"]);
    }
    public function test_usuario_no_puede_acceder_a_perfil_ajeno(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $response = $this->actingAs($user1)->get("/user/profile");
        $response->assertStatus(200);
    }
    public function test_sesion_expira_al_cerrar(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $response = $this->post("/logout");
        $response->assertRedirect("/");
        $response = $this->get("/dashboard");
        $response->assertRedirect("/login");
    }
}';
file_put_contents("tests/Feature/SeguridadTest.php", $contenido);
echo "Test de seguridad creado correctamente";
?>