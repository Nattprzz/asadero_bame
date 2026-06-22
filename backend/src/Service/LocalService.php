<?php

// ─────────────────────────────────────────────────────────────────────────────
// LocalService.php — gestión de locales.
//
// Este servicio centraliza las operaciones de creación, actualización y
// eliminación de locales. También se encarga de aplicar los datos recibidos
// a la entidad antes de guardarla en la base de datos.
// ─────────────────────────────────────────────────────────────────────────────

namespace App\Service;

use App\Entity\Local;
use Doctrine\ORM\EntityManagerInterface;

final class LocalService
{
    public function __construct(private readonly EntityManagerInterface $em) {}

    // Crea un nuevo local y lo almacena en la base de datos.
    public function create(array $payload): Local
    {
        $local = new Local();

        $this->apply($local, $payload);

        $this->em->persist($local);
        $this->em->flush();

        return $local;
    }

    // Actualiza los datos de un local existente.
    public function update(Local $local, array $payload): Local
    {
        $this->apply($local, $payload);

        $this->em->flush();

        return $local;
    }

    // Elimina un local de la base de datos.
    public function delete(Local $local): void
    {
        $this->em->remove($local);
        $this->em->flush();
    }

    // Aplica los datos recibidos a la entidad.
    private function apply(Local $local, array $payload): void
    {
        if (isset($payload['name'])) {
            $local->setName((string) $payload['name']);
        }

        if (isset($payload['address'])) {
            $local->setAddress((string) $payload['address']);
        }

        if (array_key_exists('phone', $payload)) {
            $local->setPhone($payload['phone']);
        }

        if (array_key_exists('openingHours', $payload)) {
            $local->setOpeningHours($payload['openingHours']);
        }
    }
}