<?php

namespace Tests\Unit\Repositories\V1;

use App\Enums\ErrorMessagesEnum;
use App\Exceptions\RepositoryException;
use App\Http\Criteria\ListCriteria;
use App\Models\Driver;
use App\Repositories\V1\DriverRepository;
use Tests\Unit\UnitTestCase;

// TODO revisar os nomes dos testes
class DriverRepositoryTest extends UnitTestCase
{
    protected DriverRepository $repository;

    public function setUp(): void {
        parent::setUp();
        $this->repository = new DriverRepository();
    }

    public function testCreateSuccess()
    {
        $data = [
            'name' => 'John Doe',
            'car' => [
                'license_plate' => 'XYZ1234',
                'model' => 'Tesla Model S',
                'color' => 'Black',
            ],
            'available' => true,
        ];

        $driver = $this->repository->create($data);

        $this->assertDatabaseHas('drivers', [
            'name' => 'John Doe',
            'car_license_plate' => 'XYZ1234'
        ]);
        $this->assertEquals('John Doe', $driver->name);
    }

    public function testUpdateDriverNotFound()
    {

        $this->expectException(RepositoryException::class);
        $this->expectExceptionMessage(ErrorMessagesEnum::DRIVER_NOT_FOUND->message());

        $this->repository->update(999, ['name' => 'Test']);
    }

    public function testDeleteDriverNotFound()
    {

        $this->expectException(RepositoryException::class);
        $this->expectExceptionMessage(ErrorMessagesEnum::DRIVER_NOT_FOUND->message());

        $this->repository->delete(999);
    }

    public function testFindSuccess()
    {
        $driver = Driver::factory()->create();

        $result = $this->repository->find($driver->id);

        $this->assertEquals($driver->id, $result->id);
    }

    public function testFindNotFound()
    {
        $this->expectException(RepositoryException::class);
        $this->expectExceptionMessage(ErrorMessagesEnum::DRIVER_NOT_FOUND->message());

        $this->repository->find(999);
    }

    public function testAllWithCriteria()
    {
        Driver::factory()->count(3)->create();

        $criteria = new ListCriteria([
            'fields' => ['id', 'name'],
            'order_by' => 'id',
            'sort_by' => 'asc',
            'offset' => 0,
            'limit' => 2,
        ]);

        $results = $this->repository->all($criteria);

        $this->assertCount(2, $results);
        $this->assertArrayHasKey('name', $results[0]);
    }
}
