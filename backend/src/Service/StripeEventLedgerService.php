<?php

namespace App\Service;

use App\Entity\StripeEventLedger;
use App\Enum\StripeEventStatus;
use App\Repository\StripeEventLedgerRepository;
use Doctrine\ORM\EntityManagerInterface;

final class StripeEventLedgerService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly StripeEventLedgerRepository $ledger,
    ) {}

    public function registerIncoming(string $eventId, string $type, array $payload): StripeEventLedger
    {
        $entry = $this->ledger->findOneBy(['stripeEventId' => $eventId]);

        if (!$entry instanceof StripeEventLedger) {
            $entry = (new StripeEventLedger())
                ->setStripeEventId($eventId)
                ->setType($type)
                ->setStatus(StripeEventStatus::RECEIVED)
                ->setPayload($payload);

            $this->entityManager->persist($entry);
            $this->entityManager->flush();

            return $entry;
        }

        if ($entry->isProcessed()) {
            return $entry;
        }

        $entry
            ->setType($type)
            ->setStatus(StripeEventStatus::RECEIVED)
            ->setPayload($payload)
            ->setErrorMessage(null)
            ->setProcessedAt(null);

        $this->entityManager->flush();

        return $entry;
    }

    public function markProcessed(StripeEventLedger $entry): void
    {
        $entry
            ->setStatus(StripeEventStatus::PROCESSED)
            ->setProcessedAt(new \DateTimeImmutable())
            ->setErrorMessage(null);

        $this->entityManager->flush();
    }

    public function markFailed(StripeEventLedger $entry, string $errorMessage): void
    {
        $entry
            ->setStatus(StripeEventStatus::FAILED)
            ->setErrorMessage($errorMessage);

        $this->entityManager->flush();
    }
}
