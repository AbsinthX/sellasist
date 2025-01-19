<?php declare(strict_types=1);

namespace App\Contracts;

interface PetstoreServiceInterface
{
    public function getPetsByStatus(array $status): array;

    public function getPetById(int $id): array;

    public function createPet(array $data): array;

    public function updatePetById(int $id, array $data): array;

    public function deletePetById(int $id): bool;
}
