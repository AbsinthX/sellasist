<?php declare(strict_types=1);

namespace Tests\Unit;

use App\Enums\PetStatusEnum;
use App\Services\PetstoreService;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PetstoreServiceTest extends TestCase
{
    public function testGetPetsByStatus(): void
    {
        Http::fake([
            'https://petstore.swagger.io/v2/pet/findByStatus*' => Http::response([
                ['id' => 1, 'name' => 'Doggy', 'status' => 'available']
            ], 200),
        ]);

        $service = new PetstoreService();

        $result = $service->getPetsByStatus([PetStatusEnum::AVAILABLE]);

        $this->assertCount(1, $result);
        $this->assertEquals('Doggy', $result[0]['name']);
        $this->assertEquals('available', $result[0]['status']);
    }

    public function testGetPetByIdSuccessful(): void
    {
        Http::fake([
            'https://petstore.swagger.io/v2/pet/1' => Http::response([
                'id' => 1,
                'name' => 'Doggy',
                'status' => 'available'
            ], 200),
        ]);

        $service = new PetstoreService();
        $result = $service->getPetById(1);

        $this->assertEquals(1, $result['id']);
        $this->assertEquals('Doggy', $result['name']);
        $this->assertEquals('available', $result['status']);
    }

    public function testGetPetByIdError(): void
    {
        Http::fake([
            'https://petstore.swagger.io/v2/pet/9999' => Http::response([], 404),
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('API Error [GET https://petstore.swagger.io/v2/pet/9999]: ');

        $service = new PetstoreService();
        $service->getPetById(9999);
    }

    public function testCreatePetSuccessfully(): void
    {
        $petData = [
            'name' => 'Erry',
            'category' => ['id' => 1, 'name' => 'Dogs'],
            'photoUrls' => ['http://example.com/erry.jpg'],
            'tags' => [['id' => 1, 'name' => 'cute']],
            'status' => 'available'
        ];

        Http::fake([
            'https://petstore.swagger.io/v2/pet' => Http::response(array_merge(['id' => 123], $petData), 200),
        ]);

        $service = new PetstoreService();
        $result = $service->createPet($petData);

        $this->assertEquals(123, $result['id']);
        $this->assertEquals('Erry', $result['name']);
        $this->assertEquals('available', $result['status']);
    }

    public function testCreatePetError(): void
    {
        $petData = [
            'name' => 'Erry',
            'category' => ['id' => 1, 'name' => 'Dogs'],
            'photoUrls' => ['http://example.com/erry.jpg'],
            'tags' => [['id' => 1, 'name' => 'cute']],
            'status' => 'available'
        ];

        Http::fake([
            'https://petstore.swagger.io/v2/pet' => Http::response([], 400),
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('API Error [POST https://petstore.swagger.io/v2/pet]: ');

        $service = new PetstoreService();
        $service->createPet($petData);
    }

    public function testUpdatePetByIdSuccessful(): void
    {
        $updatedPetData = [
            'name' => 'Aaaa',
            'category' => ['id' => 1, 'name' => 'Dogs'],
            'photoUrls' => ['http://example.com/Aaaa.jpg'],
            'tags' => [['id' => 2, 'name' => 'cute']],
            'status' => 'pending'
        ];

        Http::fake([
            'https://petstore.swagger.io/v2/pet' => Http::response(array_merge(['id' => 1], $updatedPetData), 200),
        ]);

        $service = new PetstoreService();
        $result = $service->updatePetById(1, $updatedPetData);

        $this->assertEquals(1, $result['id']);
        $this->assertEquals('Aaaa', $result['name']);
        $this->assertEquals('pending', $result['status']);
    }

    public function testUpdatePetByIdError(): void
    {
        $updatedPetData = [
            'name' => 'Aaaa',
            'category' => ['id' => 1, 'name' => 'Dogs'],
            'photoUrls' => ['http://example.com/Aaaa.jpg'],
            'tags' => [['id' => 2, 'name' => 'sweet']],
            'status' => 'pending'
        ];

        Http::fake([
            'https://petstore.swagger.io/v2/pet' => Http::response([], 400),
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('API Error [PUT https://petstore.swagger.io/v2/pet]: ');

        $service = new PetstoreService();
        $service->updatePetById(1, $updatedPetData);
    }


    public function testDeletePetByIdSuccessful(): void
    {
        Http::fake([
            'https://petstore.swagger.io/v2/pet/1' => Http::response([], 200),
        ]);

        $service = new PetstoreService();
        $result = $service->deletePetById(1);

        $this->assertTrue($result);
    }

    public function testDeletePetByIdError(): void
    {
        Http::fake([
            'https://petstore.swagger.io/v2/pet/123' => Http::response([], 404),
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('API Error [DELETE https://petstore.swagger.io/v2/pet/123]: ');

        $service = new PetstoreService();
        $service->deletePetById(123);
    }
}
