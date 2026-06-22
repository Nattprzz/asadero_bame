<?php

// ─────────────────────────────────────────────────────────────────────────────
// AuthTest.php — pruebas funcionales de autenticación.
//
// Este conjunto de pruebas verifica el funcionamiento de los procesos de
// inicio de sesión y registro, comprobando tanto los casos correctos como
// las validaciones y errores más habituales.
// ─────────────────────────────────────────────────────────────────────────────

namespace App\Tests\Functional;

use App\Entity\PersonalAccessToken;
use App\Enum\Roles;
use App\Service\AuthService;

final class AuthTest extends ApiTestCase
{
    // Comprueba que el login devuelve un token y los datos del usuario.
    public function testLoginReturnsToken(): void
    {
        $client = static::createClient();
        $user = $this->createUser($client, $this->uniqueEmail('login'), 'Cliente1234!');

        $response = $this->jsonRequest('POST', '/api/v1/auth/login', [
            'email' => $user->getEmail(),
            'password' => 'Cliente1234!',
        ], null, $client);

        self::assertSame(200, $response['status']);
        self::assertArrayHasKey('token', $response['json']['data']);
        self::assertArrayHasKey('user', $response['json']['data']);
        self::assertSame($user->getEmail(), $response['json']['data']['user']['email']);
        self::assertContains('ROLE_USER', $response['json']['data']['user']['roles']);
    }

    public function testResponsibleLoginReturnsLocalId(): void
    {
        $client = static::createClient();
        $local = $this->createLocal($client);
        $user = $this->createUser(
            $client,
            $this->uniqueEmail('responsable-login'),
            'Cliente1234!',
            [Roles::RESPONSABLE],
            $local
        );

        $response = $this->jsonRequest('POST', '/api/v1/auth/login', [
            'email' => $user->getEmail(),
            'password' => 'Cliente1234!',
        ], null, $client);

        self::assertSame(200, $response['status']);
        self::assertSame($local->getId(), $response['json']['data']['user']['localId']);
    }

    public function testMeReturnsLocalId(): void
    {
        $client = static::createClient();
        $local = $this->createLocal($client);
        $user = $this->createUser(
            $client,
            $this->uniqueEmail('responsable-me'),
            'Cliente1234!',
            [Roles::RESPONSABLE],
            $local
        );
        $token = $this->loginUser($client, $user);

        $response = $this->jsonRequest('GET', '/api/v1/auth/me', [], $token, $client);

        self::assertSame(200, $response['status']);
        self::assertSame($local->getId(), $response['json']['data']['localId']);
    }

    // Comprueba que el login valida los campos obligatorios.
    public function testLoginValidatesRequiredFields(): void
    {
        $response = $this->jsonRequest('POST', '/api/v1/auth/login', []);

        self::assertSame(422, $response['status']);
        self::assertFalse($response['json']['success']);
        self::assertSame('Revisa los datos del formulario.', $response['json']['error']['message']);
        self::assertSame('VALIDATION_ERROR', $response['json']['error']['code']);
        self::assertArrayHasKey('email', $response['json']['error']['details']);
        self::assertArrayHasKey('password', $response['json']['error']['details']);
    }

    // Comprueba que unas credenciales incorrectas devuelven error 401.
    public function testLoginWithInvalidCredentialsReturnsUnauthorized(): void
    {
        $client = static::createClient();
        $user = $this->createUser($client, $this->uniqueEmail('invalid-login'), 'Cliente1234!');

        $response = $this->jsonRequest('POST', '/api/v1/auth/login', [
            'email' => $user->getEmail(),
            'password' => 'PasswordIncorrecta123!',
        ], null, $client);

        self::assertSame(401, $response['status']);
        self::assertFalse($response['json']['success']);
    }

    // Comprueba las reglas mínimas de validación de contraseña.
    public function testRegisterValidatesPassword(): void
    {
        $response = $this->jsonRequest('POST', '/api/v1/auth/register', [
            'email' => 'nuevo@example.test',
            'name' => 'Nuevo Cliente',
            'password' => 'short',
        ]);

        self::assertSame(422, $response['status']);
    }

    // Comprueba que no es posible asignarse permisos de administrador al registrarse.
    public function testRegisterIgnoresRolePayload(): void
    {
        $response = $this->jsonRequest('POST', '/api/v1/auth/register', [
            'email' => 'nuevo-role-'.bin2hex(random_bytes(4)).'@example.test',
            'name' => 'Nuevo Cliente',
            'password' => 'Cliente1234!',
            'roles' => ['ROLE_ADMIN'],
        ]);

        self::assertSame(201, $response['status']);
        self::assertSame(['ROLE_USER'], $response['json']['data']['user']['roles']);
    }

    // Comprueba la compatibilidad con alias de campos utilizados por el frontend.
    public function testRegisterAcceptsFrontendAliasesAndSpanishPhone(): void
    {
        $response = $this->jsonRequest('POST', '/api/v1/auth/register', [
            'nombre' => 'Cliente',
            'apellidos' => 'Nuevo',
            'email' => 'nuevo-phone-'.bin2hex(random_bytes(4)).'@example.test',
            'telefono' => '+34604265251',
            'plainPassword' => 'Cliente1234!',
            'privacyPolicy' => true,
        ]);

        self::assertSame(201, $response['status']);
        self::assertSame('+34604265251', $response['json']['data']['user']['phone']);
        self::assertSame(['ROLE_USER'], $response['json']['data']['user']['roles']);
    }

    // Comprueba que un teléfono inválido genera un error de validación.
    public function testRegisterReturnsFieldErrorForInvalidPhone(): void
    {
        $response = $this->jsonRequest('POST', '/api/v1/auth/register', [
            'name' => 'Cliente',
            'email' => 'nuevo-invalid-phone-'.bin2hex(random_bytes(4)).'@example.test',
            'phone' => '123',
            'password' => 'Cliente1234!',
        ]);

        self::assertSame(422, $response['status']);
        self::assertFalse($response['json']['success']);
        self::assertSame('VALIDATION_ERROR', $response['json']['error']['code']);
        self::assertArrayHasKey('phone', $response['json']['error']['details']);
    }

    // Comprueba que no se permite registrar un email ya existente.
    public function testRegisterReturnsFieldErrorForDuplicateEmail(): void
    {
        $client = static::createClient();
        $email = $this->uniqueEmail('duplicate-register');

        $first = $this->jsonRequest('POST', '/api/v1/auth/register', [
            'name' => 'Cliente',
            'email' => $email,
            'phone' => '604265251',
            'password' => 'Cliente1234!',
        ], null, $client);

        self::assertSame(201, $first['status']);

        $response = $this->jsonRequest('POST', '/api/v1/auth/register', [
            'name' => 'Cliente',
            'email' => $email,
            'phone' => '604265251',
            'password' => 'Cliente1234!',
        ], null, $client);

        self::assertSame(422, $response['status']);
        self::assertSame('VALIDATION_ERROR', $response['json']['error']['code']);
        self::assertArrayHasKey('email', $response['json']['error']['details']);
    }

    public function testForgotPasswordNeverExposesTokenOrEnumeratesUsers(): void
    {
        $client = static::createClient();
        $user = $this->createUser($client, $this->uniqueEmail('forgot-password'));

        $existingUserResponse = $this->jsonRequest('POST', '/api/v1/auth/forgot-password', [
            'email' => $user->getEmail(),
        ], null, $client);

        $unknownUserResponse = $this->jsonRequest('POST', '/api/v1/auth/forgot-password', [
            'email' => $this->uniqueEmail('unknown-forgot-password'),
        ], null, $client);

        $expectedResponse = [
            'success' => true,
            'data' => [
                'message' => 'Si el email existe, recibirás instrucciones para restablecer la contraseña.',
            ],
        ];

        self::assertSame(200, $existingUserResponse['status']);
        self::assertSame(200, $unknownUserResponse['status']);
        self::assertSame($expectedResponse, $existingUserResponse['json']);
        self::assertSame($expectedResponse, $unknownUserResponse['json']);
        self::assertStringNotContainsString('resetToken', $existingUserResponse['content']);
        self::assertStringNotContainsString('resetToken', $unknownUserResponse['content']);
    }

    public function testResetPasswordRevokesExistingTokensAndBlocksOldSession(): void
    {
        $client = static::createClient();
        $user = $this->createUser($client, $this->uniqueEmail('reset-password'));
        $oldToken = $this->loginUser($client, $user);
        $resetToken = static::getContainer()
            ->get(AuthService::class)
            ->createPasswordResetToken($user->getEmail());

        self::assertNotNull($resetToken);

        $response = $this->jsonRequest('POST', '/api/v1/auth/reset-password', [
            'token' => $resetToken,
            'password' => 'NuevaClave123!',
        ], null, $client);

        self::assertSame(200, $response['status']);
        self::assertSame(['passwordReset' => true], $response['json']['data']);

        $tokenEntity = $this->entityManager()
            ->getRepository(PersonalAccessToken::class)
            ->findOneBy(['tokenHash' => hash('sha256', $oldToken)]);

        self::assertInstanceOf(PersonalAccessToken::class, $tokenEntity);
        self::assertFalse($tokenEntity->isValid());

        $protectedResponse = $this->jsonRequest('GET', '/api/v1/auth/me', [], $oldToken, $client);

        self::assertSame(401, $protectedResponse['status']);
    }

    public function testFailedResetPasswordDoesNotRevokeExistingTokens(): void
    {
        $client = static::createClient();
        $user = $this->createUser($client, $this->uniqueEmail('reset-password-failed'));
        $oldToken = $this->loginUser($client, $user);
        $tokenEntityBefore = $this->entityManager()
            ->getRepository(PersonalAccessToken::class)
            ->findOneBy(['tokenHash' => hash('sha256', $oldToken)]);

        self::assertInstanceOf(PersonalAccessToken::class, $tokenEntityBefore);
        self::assertTrue($tokenEntityBefore->isValid());

        $response = $this->jsonRequest('POST', '/api/v1/auth/reset-password', [
            'token' => 'invalid-reset-token',
            'password' => 'NuevaClave123!',
        ], null, $client);

        self::assertSame(400, $response['status']);

        $tokenEntityAfter = $this->entityManager()
            ->getRepository(PersonalAccessToken::class)
            ->findOneBy(['tokenHash' => hash('sha256', $oldToken)]);

        self::assertInstanceOf(PersonalAccessToken::class, $tokenEntityAfter);
        self::assertTrue($tokenEntityAfter->isValid());

        $protectedResponse = $this->jsonRequest('GET', '/api/v1/auth/me', [], $oldToken, $client);

        self::assertSame(200, $protectedResponse['status']);
    }
}
