<?php declare(strict_types=1);

namespace App\Services;

use App\Contracts\PetstoreServiceInterface;
use App\Enums\PetStatusEnum;
use Illuminate\Support\Facades\Http;

class PetstoreService implements PetstoreServiceInterface
{
    private const DEFAULT_BASE_URL = 'https://petstore.swagger.io/v2';
    private string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('services.petstore.base_url', self::DEFAULT_BASE_URL);
    }

    /**
     * @param array|PetStatusEnum $statuses
     * @return array
     * @throws \Exception
     */
    public function getPetsByStatus(array|PetStatusEnum $statuses = [PetStatusEnum::AVAILABLE]): array
    {
        $statuses = $this->validateAndMapStatuses($statuses);
        $queryString = implode('&', array_map(fn($status) => "status=" . urlencode($status->value), $statuses));
        $url = "{$this->baseUrl}/pet/findByStatus?$queryString";

        $response = Http::get($url);

        return $this->handleApiResponse($response);
    }

    /**
     * @param int $id
     * @return array
     * @throws \Exception
     */
    public function getPetById(int $id): array
    {
        $response = Http::get("{$this->baseUrl}/pet/{$id}");

        return $this->handleApiResponse($response);
    }

    /**
     * @param array $data
     * @return array
     * @throws \Exception
     */
    public function createPet(array $data): array
    {
        $data['id'] = time();
        $response = Http::post("{$this->baseUrl}/pet", $data);

        return $this->handleApiResponse($response);
    }

    /**
     * @param int $id
     * @param array $data
     * @return array
     * @throws \Exception
     */
    public function updatePetById(int $id, array $data): array
    {
        $data['id'] = $id;
        $response = Http::put("{$this->baseUrl}/pet", $data);

        return $this->handleApiResponse($response);
    }

    /**
     * @param int $id
     * @return bool
     * @throws \Exception
     */
    public function deletePetById(int $id): bool
    {
        $response = Http::delete("{$this->baseUrl}/pet/{$id}");

        $this->handleApiResponse($response);

        return true;
    }

    /**
     * @param array $statuses
     * @return array
     */
    public function validateAndMapStatuses(array $statuses): array
    {
        return array_map(function ($status) {
            if ($status instanceof PetStatusEnum) {
                return $status;
            }

            return PetStatusEnum::tryFrom($status)
                ?? throw new \InvalidArgumentException("Invalid status provided: {$status}");
        }, $statuses);
    }

    /**
     * @param $response
     * @return array
     * @throws \Exception
     */
    private function handleApiResponse($response): array
    {
        if ($response->successful()) {
            return $response->json();
        }

        throw new \Exception(sprintf(
            'API Error [%s %s]: %s',
            $response->transferStats->getRequest()->getMethod(),
            $response->effectiveUri(),
            $response->body()
        ));
    }
}
