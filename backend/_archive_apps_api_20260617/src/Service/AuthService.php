<?php

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

    public function register(array $data): array
    {
        $this->validator->requireFields($data, ['name', 'email', 'password']);
        $this->validator->email((string) $data['email']);
        $this->validator->password((string) $data['password']);

        if ($this->users->findOneBy(['email' => mb_strtolower((string) $data['email'])]) !== null) {
            throw new BadRequestHttpException('Ya existe un usuario con ese email.');
        }

        $username = (string) ($data['username'] ?? $this->usernameFromEmail((string) $data['email']));
        if ($username !== '' && $this->users->findOneBy(['username' => mb_strtolower($username)]) !== null) {
            throw new BadRequestHttpException('Ya existe un usuario con ese username.');
        }

        $user = (new User())
            ->setName((string) $data['name'])
            ->setSurname((string) ($data['surname'] ?? ''))
            ->setUsername($username)
            ->setEmail((string) $data['email'])
            ->setPhone($data['phone'] ?? null)
            ->setRoles([Roles::USER]);
        $user->setPassword($this->passwordHasher->hashPassword($user, (string) $data['password']));

        $this->entityManager->persist($user);
        $plainToken = $this->issueToken($user);
        $this->entityManager->flush();

        return [$plainToken, $user];
    }

    public function login(string $email, string $password): array
    {
        $user = $this->findByIdentifier($email);
        if ($user === null || !$this->passwordHasher->isPasswordValid($user, $password)) {
            throw new UnauthorizedHttpException('Bearer', 'Credenciales no validas.');
        }

        $plainToken = $this->issueToken($user);
        $this->entityManager->flush();

        return [$plainToken, $user];
    }

    public function logout(string $authorization): void
    {
        $plainToken = trim(substr($authorization, 7));
        $token = $this->tokens->findOneBy(['tokenHash' => hash('sha256', $plainToken)]);
        if ($token !== null) {
            $token->revoke();
            $this->entityManager->flush();
        }
    }

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

    public function resetPassword(string $token, string $password): void
    {
        $this->validator->password($password);
        $reset = $this->passwordResetTokens->findOneBy(['tokenHash' => hash('sha256', $token)]);
        if ($reset === null || !$reset->isValid() || $reset->getUser() === null) {
            throw new BadRequestHttpException('Token de recuperacion no valido o caducado.');
        }

        $user = $reset->getUser();
        $user->setPassword($this->passwordHasher->hashPassword($user, $password));
        $reset->markUsed();
        $this->entityManager->flush();
    }

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

    private function usernameFromEmail(string $email): string
    {
        return preg_replace('/[^a-z0-9_.-]/', '', mb_strtolower(strstr($email, '@', true) ?: $email)) ?: '';
    }
}
