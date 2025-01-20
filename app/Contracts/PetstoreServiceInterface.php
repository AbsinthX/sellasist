<?php declare(strict_types=1);

namespace App\Contracts;

use App\Enums\PetStatusEnum;

/**
 *  @TODO Implementing DTOs for method parameters and return values, e.g. PetData, Category, Tag
 */
interface PetstoreServiceInterface
{
    /**
     * @param array|PetStatusEnum $status
     * @return array
     */
    public function getPetsByStatus(array|PetStatusEnum $status): array;

    /**
     * @param int $id
     * @return array
     */
    public function getPetById(int $id): array;

    /**
     * @param array $data
     * @return array
     */
    public function createPet(array $data): array;

    /**
     * @param int $id
     * @param array $data
     * @return array
     */
    public function updatePetById(int $id, array $data): array;

    /**
     * @param int $id
     * @return bool
     */
    public function deletePetById(int $id): bool;
}
