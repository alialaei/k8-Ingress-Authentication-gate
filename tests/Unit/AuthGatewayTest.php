<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;
use Laravel\Passport\Client;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Support\Str;

class AuthGatewayTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Generate Passport keys before tests
        $this->artisan('passport:keys --force');
    }

    #[Test]
    public function a_user_can_get_an_access_token_via_password_grant()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        // Create password grant client using Eloquent
        $client = Client::create([
            'id' => (string) Str::uuid(),
            'name' => 'Password Grant Client',
            'secret' => 'testing_secret', // Do not hash it
            'redirect_uris' => ["http://localhost"],
            'grant_types' => ['password', 'refresh_token'],
            'revoked' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->assertNotNull($client, 'Password grant client not created.');

        $response = $this->postJson('/oauth/token', [
            'grant_type' => 'password',
            'client_id' => $client->id,
            'client_secret' => 'testing_secret',
            'username' => $user->email,
            'password' => 'password',
            'scope' => '*',
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure(['access_token', 'refresh_token', 'expires_in', 'token_type']);
    }

    #[Test]
    public function an_authenticated_user_can_access_protected_routes()
    {
        $user = User::factory()->create();

        Passport::actingAs($user);

        $response = $this->getJson('/api/user');

        $response->assertStatus(200)
                 ->assertJson([
                     'id' => $user->id,
                     'email' => $user->email,
                     'name' => $user->name,
                 ]);
    }

    #[Test]
    public function unauthenticated_users_cannot_access_protected_routes()
    {
        $response = $this->getJson('/api/user');

        $response->assertStatus(401)
                 ->assertJson(['message' => 'Unauthenticated.']);
    }

    #[Test]
    public function the_verify_token_endpoint_works_for_valid_tokens()
    {
        $user = User::factory()->create();
        Passport::actingAs($user);

        $response = $this->getJson('/api/verify-token');

        $response->assertStatus(200)
                 ->assertJson([
                     'message' => 'Token is valid',
                     'user_id' => $user->id,
                     'user_email' => $user->email,
                 ])
                 ->assertHeader('X-User-ID', (string)$user->id)
                 ->assertHeader('X-User-Email', $user->email);
    }

    #[Test]
    public function the_verify_token_endpoint_returns_unauthorized_for_invalid_tokens()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer invalid_token_string'
        ])->getJson('/api/verify-token');

        $response->assertStatus(401)
                 ->assertJson(['message' => 'Unauthenticated.']);
    }
}
