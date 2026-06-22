<?php

// ─────────────────────────────────────────────────────────────────────────────
// AuthService.php — gestión de autenticación de usuarios.
//
// Este servicio centraliza el registro, inicio de sesión, cierre de sesión y
// recuperación de contraseña. También se encarga de validar los datos recibidos,
// generar tokens de acceso y proteger las contraseñas mediante hash.
// ─────────────────────────────────────────────────────────────────────────────

namespace App\Service;

use App\Entity\PasswordResetToken;
use App\Entity\PersonalAccessToken;
use App\Entity\User;
use App\Enum\Roles;
use App\Repository\PasswordResetTokenRepository;
use App\Repository\PersonalAccessTokenRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class AuthService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserRepository $users,
        private readonly PersonalAccessTokenRepository $tokens,
        private readonly PasswordResetTokenRepository $passwordResetTokens,
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly InputValidator $validator,
        private readonly int $tokenTtlDays,
    ) {}

    // Registra un nuevo usuario y devuelve su token de acceso.
    public function register(array $data): array
    {
        $data = $this->normalizeRegisterPayload($data);
        $this->validateRegisterPayload($data);

        if ($this->users->findOneBy(['email' => mb_strtolower((string) $data['email'])]) !== null) {
            $this->throwValidationError(['email' => ['Ya existe un usuario con ese email.']]);
        }

        $username = (string) ($data['username'] ?? $this->usernameFromEmail((string) $data['email']));
        if ($username !== '' && $this->users->findOneBy(['username' => mb_strtolower($username)]) !== null) {
            $this->throwValidationError(['username' => ['Ya existe un usuario con ese username.']]);
        }

        $user = (new User())
            ->setName((string) $data['name'])
            ->setSurname((string) ($data['surname'] ?? ''))
            ->setUsername($username)
            ->setEmail((string) $data['email'])
            ->setPhone($data['phone'] ?? null)
            ->setRoles([Roles::USER]);

        // La contraseña nunca se guarda en texto plano.
        $user->setPassword($this->passwordHasher->hashPassword($user, (string) $data['password']));

        $this->entityManager->persist($user);

        // Se genera un token inicial para iniciar sesión justo después del registro.
        $plainToken = $this->issueToken($user);

        $this->entityManager->flush();

        return [$plainToken, $user];
    }

    // Inicia sesión usando email o username junto con la contraseña.
    public function login(string $email, string $password): array
    {
        $email = mb_strtolower(trim($email));
        $user = $this->findByIdentifier($email);

        if ($user === null || !$this->passwordHasher->isPasswordValid($user, $password)) {
            throw new UnauthorizedHttpException('Bearer', 'Credenciales no validas.');
        }

        $plainToken = $this->issueToken($user);
        $this->entityManager->flush();

        return [$plainToken, $user];
    }

    // Normaliza y valida los datos antes de iniciar sesión.
    public function loginFromPayload(array $data): array
    {
        $data = $this->normalizeLoginPayload($data);
        $this->validateLoginPayload($data);

        return $this->login((string) $data['identifier'], (string) $data['password']);
    }

    // Cierra sesión revocando el token enviado en la cabecera Authorization.
    public function logout(string $authorization): void
    {
        $plainToken = trim(substr($authorization, 7));
        $token = $this->tokens->findOneBy(['tokenHash' => hash('sha256', $plainToken)]);

        if ($token !== null) {
            $token->revoke();
            $this->entityManager->flush();
        }
    }

    // Crea un token para recuperar la contraseña.
    public function createPasswordResetToken(string $email): ?string
    {
        $user = $this->users->findOneBy(['email' => mb_strtolower($email)]);

        if ($user === null) {
            return null;
        }

        $plainToken = bin2hex(random_bytes(32));

        $reset = (new PasswordResetToken())
            ->setUser($user)
            ->setTokenHash(hash('sha256', $plainToken));

        $this->entityManager->persist($reset);
        $this->entityManager->flush();

        return $plainToken;
    }

    // Cambia la contraseña usando un token de recuperación válido.
    public function resetPassword(string $token, string $password): void
    {
        $this->validator->password($password);

        $reset = $this->passwordResetTokens->findOneBy(['tokenHash' => hash('sha256', $token)]);

        if ($reset === null || !$reset->isValid() || $reset->getUser() === null) {
            throw new BadRequestHttpException('Token de recuperacion no valido o caducado.');
        }

        $user = $reset->getUser();
        $connection = $this->entityManager->getConnection();
        $connection->beginTransaction();

        try {
            $user->setPassword($this->passwordHasher->hashPassword($user, $password));

            // Se marca como usado para que no pueda reutilizarse.
            $reset->markUsed();

            // El cambio de contraseña invalida todas las sesiones activas.
            $this->tokens->revokeAllForUser($user);

            $this->entityManager->flush();
            $connection->commit();
        } catch (\Throwable $exception) {
            if ($connection->isTransactionActive()) {
                $connection->rollBack();
            }

            $this->entityManager->clear();

            throw $exception;
        }
    }

    // Genera y guarda un token personal de acceso para el usuario.
    private function issueToken(User $user): string
    {
        $plainToken = bin2hex(random_bytes(32));

        $token = (new PersonalAccessToken())
            ->setUser($user)
            ->setTokenHash(hash('sha256', $plainToken))
            ->setExpiresAt(new \DateTimeImmutable(sprintf('+%d days', $this->tokenTtlDays)));

        $this->entityManager->persist($token);

        return $plainToken;
    }

    // Adapta distintos nombres de campos para aceptar datos del frontend.
    private function normalizeRegisterPayload(array $data): array
    {
        $data['name'] ??= $data['nombre'] ?? $data['firstName'] ?? null;
        $data['surname'] ??= $data['apellidos'] ?? $data['lastName'] ?? '';
        $data['password'] ??= $data['plainPassword'] ?? null;
        $data['phone'] ??= $data['phoneNumber'] ?? $data['telefono'] ?? $data['mobile'] ?? null;

        if (is_string($data['email'] ?? null)) {
            $data['email'] = mb_strtolower(trim($data['email']));
        }

        if (is_string($data['name'] ?? null)) {
            $data['name'] = trim($data['name']);
        }

        if (is_string($data['surname'] ?? null)) {
            $data['surname'] = trim($data['surname']);
        }

        if (is_string($data['username'] ?? null)) {
            $data['username'] = trim($data['username']);
        }

        if (is_string($data['phone'] ?? null)) {
            $data['phone'] = $this->normalizeSpanishPhone($data['phone']);
        }

        return $data;
    }

    // Prepara los datos de login para permitir email o username.
    private function normalizeLoginPayload(array $data): array
    {
        $data['identifier'] ??= $data['email'] ?? $data['username'] ?? null;
        $data['password'] ??= $data['plainPassword'] ?? null;

        if (is_string($data['identifier'] ?? null)) {
            $data['identifier'] = mb_strtolower(trim($data['identifier']));
        }

        return $data;
    }

    // Valida que los datos mínimos para iniciar sesión estén presentes.
    private function validateLoginPayload(array $data): void
    {
        $errors = [];

        if (($data['identifier'] ?? '') === '') {
            $errors['email'][] = 'El email es obligatorio.';
        }

        if (($data['password'] ?? '') === '') {
            $errors['password'][] = 'La password es obligatoria.';
        }

        if ($errors !== []) {
            $this->throwValidationError($errors);
        }
    }

    // Valida los datos necesarios para registrar un usuario.
    private function validateRegisterPayload(array $data): void
    {
        $errors = [];

        if (($data['name'] ?? '') === '') {
            $errors['name'][] = 'El nombre es obligatorio.';
        }

        $email = (string) ($data['email'] ?? '');
        if ($email === '') {
            $errors['email'][] = 'El email es obligatorio.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'][] = 'El email no es valido.';
        }

        $password = (string) ($data['password'] ?? '');
        if ($password === '') {
            $errors['password'][] = 'La password es obligatoria.';
        } elseif (mb_strlen($password) < 8) {
            $errors['password'][] = 'La password debe tener al menos 8 caracteres.';
        }

        if (($data['phone'] ?? null) !== null && !$this->isValidSpanishPhone((string) $data['phone'])) {
            $errors['phone'][] = 'El telefono debe tener 9 digitos o formato internacional espanol.';
        }

        $privacy = $data['privacyPolicy'] ?? $data['privacyAccepted'] ?? $data['acceptPrivacy'] ?? $data['termsAccepted'] ?? null;
        if ($privacy !== null && filter_var($privacy, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) !== true) {
            $errors['privacyPolicy'][] = 'Debes aceptar la politica de privacidad.';
        }

        if ($errors !== []) {
            $this->throwValidationError($errors);
        }
    }

    // Limpia el teléfono para poder validarlo de forma uniforme.
    private function normalizeSpanishPhone(string $phone): ?string
    {
        $phone = preg_replace('/[\s().-]+/', '', trim($phone));

        if ($phone === '') {
            return null;
        }

        if (str_starts_with($phone, '0034')) {
            $phone = '+34'.substr($phone, 4);
        }

        return $phone;
    }

    // Comprueba teléfonos españoles nacionales o con prefijo +34.
    private function isValidSpanishPhone(string $phone): bool
    {
        return preg_match('/^(?:[6789]\d{8}|\+34[6789]\d{8})$/', $phone) === 1;
    }

    // Lanza los errores de validación en formato JSON.
    private function throwValidationError(array $errors): never
    {
        throw new BadRequestHttpException(json_encode($errors, JSON_THROW_ON_ERROR));
    }

    // Busca un usuario por email o username.
    private function findByIdentifier(string $identifier): ?User
    {
        $identifier = mb_strtolower(trim($identifier));

        return $this->users->createQueryBuilder('u')
            ->andWhere('u.email = :identifier OR u.username = :identifier')
            ->setParameter('identifier', $identifier)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    // Genera un username básico a partir del email.
    private function usernameFromEmail(string $email): string
    {
        return preg_replace('/[^a-z0-9_.-]/', '', mb_strtolower(strstr($email, '@', true) ?: $email)) ?: '';
    }
}
