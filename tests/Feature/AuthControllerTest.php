<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    private array $credentials = [
        'email' => 'test@mail.com',
        'password' => 'Test1234'
    ];

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'email' => $this->credentials['email'],
            'password' => \Hash::make(
                $this->credentials['password']
            ),
        ]);

        Notification::fake();
    }

    public function test_login(): void
    {
        $response = $this->post(
            '/api/auth/login',
            $this->credentials
        );

        $response->assertStatus(200);
    }

    public function test_login_invalid(): void
    {
        $response = $this->post(
            '/api/auth/login',
            array_replace(
                $this->credentials,
                ['password' => 'Invalid1234']
            )
        );

        $response->assertStatus(401);
    }

    public function test_forgot(): void
    {
        $response = $this->post(
            '/api/auth/forgot',
            ['email' => $this->credentials['email']]
        );

        Notification::assertSentTo(
            [$this->user],
            ResetPasswordNotification::class
        );

        $response->assertStatus(200);
    }

    public function test_forgot_invalid(): void
    {
        $response = $this->post(
            '/api/auth/forgot',
            ['email' => 'invalid@mail.com']
        );

        $response->assertStatus(401);
    }

    public function resetBody(): array
    {
        $this->test_forgot();

        return [
            'token' => $this->getForgotToken(),
            'password' => 'New12345',
            'password_confirmation' => 'New12345',
            'email' => $this->credentials['email'],
        ];
    }

    public function test_reset_valid(): void
    {
        $response = $this->post(
            '/api/auth/reset',
            $this->resetBody()
        );

        $response->assertStatus(200);
    }

    public function test_reset_invalid_fields(): void
    {
        $response = $this->post(
            '/api/auth/reset',
            array_merge($this->resetBody(), [
                'password' => 'DifferentFromConfirmation123',
            ])
        );

        $response->assertStatus(400);
    }

    public function test_reset_invalid_token(): void
    {
        $this->test_reset_valid();

        $response = $this->post(
            '/api/auth/reset',
            $this->resetBody(),
        );

        $response->assertStatus(401);
    }

    public function test_register(): void
    {
        $response = $this->post('/api/auth/register', [
            'name' => 'Teste',
            'email' => 'a@a.a',
            'password' => 'Test@123',
            'password_confirmation' => 'Test@123',
        ]);

        Notification::assertSentTo(
            [User::where('email', 'a@a.a')->first()],
            ResetPasswordNotification::class
        );

        $response->assertStatus(200);
    }


    private function getForgotToken(): string
    {
        $notification = Notification::sent(
            $this->user,
            ResetPasswordNotification::class
        )->first();

        return explode('?token=', $notification->url)[1] ?? '';
    }
}
