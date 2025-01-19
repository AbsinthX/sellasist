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

    public function getPetsByStatus(array $statuses = [PetStatusEnum::AVAILABLE]): array
    {
        $queryString = implode('&', array_map(fn($status) => "status=" . urlencode($status->value), $statuses));
        $url = "{$this->baseUrl}/pet/findByStatus?$queryString";

        $response = Http::get($url);

        if ($response->successful()) {
            return $response->json();
        }

        throw new \Exception('Error fetching pets: ' . $response->body());
    }

    public function getPetById(int $id): array
    {
        $response = Http::get("{$this->baseUrl}/pet/{$id}");

        if ($response->successful()) {
            return $response->json();
        }

        throw new \Exception('Error fetching pet: ' . $response->body());
    }

    public function createPet(array $data): array
    {
        $response = Http::post("{$this->baseUrl}/pet", $data);

        if ($response->successful()) {
            return $response->json();
        }

        throw new \Exception('Error creating pet: ' . $response->body());
    }

    public function updatePetById(int $id, array $data): array
    {
        $data['id'] = $id;
        $response = Http::put("{$this->baseUrl}/pet", $data);

        if ($response->successful()) {
            return $response->json();
        }

        throw new \Exception('Error updating pet: ' . $response->body());
    }

    public function deletePetById(int $id): bool
    {
        $response = Http::delete("{$this->baseUrl}/pet/{$id}");

        if ($response->successful()) {
            return true;
        }

        throw new \Exception('Error deleting pet: ' . $response->body());
    }
}
