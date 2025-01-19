<?php declare(strict_types=1);

namespace App\Http\Controllers;

use App\Contracts\PetstoreServiceInterface;
use App\Enums\PetStatusEnum;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PetstoreController extends Controller
{
    public function __construct(private PetstoreServiceInterface $petstoreService)
    {
        $this->petstoreService = $petstoreService;
    }

    public function getPetsByStatus(Request $request): JsonResponse
    {
        try {
            $statuses = $request->query('status', [PetStatusEnum::AVAILABLE]);

            if (!is_array($statuses)) {
                $statuses = [$statuses];
            }

            $statuses = array_map(function ($status) {
                return PetStatusEnum::tryFrom($status) ?? throw new \InvalidArgumentException("Invalid status: $status");
            }, $statuses);

            $pets = $this->petstoreService->getPetsByStatus($statuses);

            return response()->json($pets);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'error' => 'Niepoprawny status zwierząt.',
                'message' => $e->getMessage()
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Nie udało się pobrać listy zwierząt.',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getPetById(int $id)
    {
        try {
            $pet = $this->petstoreService->getPetById($id);
            return response()->json($pet, 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Nie udało się pobrać szczegółów zwierzęcia.',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function createPet(Request $request): array
    {
        try {
            $data = $request->all();
            return $this->petstoreService->createPet($data);
        } catch (\Exception $e) {
            throw new \Exception('Nie udało się stworzyć zwierzęcia: ' . $e->getMessage());
        }
    }

    public function updatePetById(Request $request): array
    {
        try {
            $id = $request->route('id');
            $data = $request->all();
            return $this->petstoreService->updatePetById($id, $data);
        } catch (\Exception $e) {
            throw new \Exception('Nie udało się zaktualizować zwierzęcia: ' . $e->getMessage());
        }
    }

    public function deletePetById(int $id)
    {
        try {
            $pet = $this->petstoreService->deletePetById($id);
            return response()->json($pet, 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Nie udało się usunąć szczegółów zwierzęcia.',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
