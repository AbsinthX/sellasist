<?php declare(strict_types=1);

namespace App\Http\Controllers;

use App\Contracts\PetstoreServiceInterface;
use App\Enums\PetStatusEnum;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class PetstoreController extends Controller
{
    /**
     * @param PetstoreServiceInterface $petstoreService
     */
    public function __construct(private PetstoreServiceInterface $petstoreService)
    {
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getPetsByStatus(Request $request): JsonResponse
    {
        try {
            $statuses = $request->query('status', [PetStatusEnum::AVAILABLE]);

            if (!is_array($statuses)) {
                $statuses = [$statuses];
            }

            $statuses = $this->petstoreService->validateAndMapStatuses($statuses);

            $pets = $this->petstoreService->getPetsByStatus($statuses);

            return response()->json($pets);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'error' => 'Invalid animal status.',
                'message' => $e->getMessage()
            ], 400);
        } catch (\Exception $e) {
            return $this->handleException($e, 'Failed to retrieve the list of animals.');
        }
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    public function getPetById(int $id): JsonResponse
    {
        try {
            $pet = $this->petstoreService->getPetById($id);
            return response()->json($pet, 200);
        } catch (\Exception $e) {
            return $this->handleException($e, 'Failed to retrieve the animal details.');
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function createPet(Request $request): JsonResponse
    {
        try {
            $data = $this->validateRequest($request);
            $pet = $this->petstoreService->createPet($data);

            return response()->json($pet, 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => 'Validation failed.', 'details' => $e->errors()], 422);
        } catch (\Exception $e) {
            return $this->handleException($e, 'Failed to create the animal.');
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function updatePetById(Request $request): JsonResponse
    {
        try {
            $id = (int) $request->route('id');
            $data = $this->validateRequest($request, true);
            $pet = $this->petstoreService->updatePetById($id, $data);

            return response()->json($pet, 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => 'Validation failed.', 'details' => $e->errors()], 422);
        } catch (\Exception $e) {
            return $this->handleException($e, 'Failed to update the animal.');
        }
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    public function deletePetById(int $id): JsonResponse
    {
        try {
            $success = $this->petstoreService->deletePetById($id);

            if ($success) {
                return response()->json(['message' => 'Animal deleted successfully.'], 200);
            }

            return response()->json(['error' => 'Animal could not be deleted.'], 500);
        } catch (\Exception $e) {
            return $this->handleException($e, 'Failed to delete the animal details.');
        }
    }

    /**
     * @param \Throwable $e
     * @param string $message
     * @param int $statusCode
     * @return JsonResponse
     */
    private function handleException(\Throwable $e, string $message, int $statusCode = 500): JsonResponse
    {
        \Log::error($message, ['exception' => $e]);

        return response()->json([
            'error' => $message,
            'message' => $e->getMessage(),
            'code' => $statusCode
        ], $statusCode);
    }

    /**
     * @param bool $isUpdate
     * @return array
     */
    private function getValidationRules(bool $isUpdate = false): array
    {
        $rules = [
            'name' => ['string', 'max:255'],
            'status' => ['string', Rule::in(PetStatusEnum::values())],
            'category.id' => ['integer'],
            'category.name' => ['string', 'max:255'],
            'photoUrls' => ['array'],
            'photoUrls.*' => ['url'],
            'tags' => ['array'],
            'tags.*.name' => ['string', 'max:255'],
        ];

        if ($isUpdate) {
            return array_map(fn($rule) => is_array($rule) ? array_merge(['sometimes'], $rule) : "sometimes|{$rule}", $rules);
        }

        return array_merge(
            ['name' => array_merge(['required'], $rules['name']), 'status' => array_merge(['required'], $rules['status'])],
            $rules
        );
    }

    /**
     * @param Request $request
     * @param bool $isUpdate
     * @return array
     * @throws \Illuminate\Validation\ValidationException
     */
    private function validateRequest(Request $request, bool $isUpdate = false): array
    {
        $validator = Validator::make($request->all(), $this->getValidationRules($isUpdate));

        if ($validator->fails()) {
            throw new \Illuminate\Validation\ValidationException($validator);
        }

        return $validator->validated();
    }
}
