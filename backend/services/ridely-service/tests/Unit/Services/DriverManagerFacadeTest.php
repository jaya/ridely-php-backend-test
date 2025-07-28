<?php

namespace Tests\Unit\Services;

use App\Converters\DriverConverter;
use App\Http\Criteria\Criteria;
use App\Services\DriverManagerFacade;
use Illuminate\Support\Facades\Log;
use Mocks\Services\DriverManagerFacadeMock;
use Tests\Unit\UnitTestCase;

// TODO revisar os nomes dos testes
class DriverManagerFacadeTest extends UnitTestCase
{
    protected Criteria $criteria;

    protected DriverManagerFacade $facade;

    protected DriverManagerFacadeMock $mock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mock = new DriverManagerFacadeMock();
    }
    public function testCreateSuccess()
    {
        Log::info(
            sprintf("Testing the method %s with parameters: %s", __METHOD__, json_encode(func_get_args()))
        );

        // TODO migrar para um Helper ou Util
        $data = [
            'name' => 'John Doe',
            'car' => [
                'license_plate' => 'XYZ1234',
                'model' => 'Tesla Model S',
                'color' => 'Black',
            ],
            'available' => true,
        ];

        $expectedDriver = DriverConverter::convertFromArrayToModel($data);
        $expectedDriver->id = 1;

        $this->mock->repository
            ->expects($this->once())
            ->method('create')
            ->with($data)
            ->willReturn($expectedDriver);


        $this->facade = $this->mock->getObjectWithMockDependencies();
        $driver = $this->facade->create($data);

        $this->assertEquals(1, $driver->id);
        $this->assertEquals($data['name'], $driver->name);
        $this->assertEquals($data['car']['license_plate'], $driver->car_license_plate);

    }

    public function testUpdateSuccess()
    {
        Log::info(
            sprintf("Testing the method %s with parameters: %s", __METHOD__, json_encode(func_get_args()))
        );

        $this->markTestSkipped('Not implemented yet');
    }

    public function testListSuccess()
    {
        Log::info(
            sprintf("Testing the method %s with parameters: %s", __METHOD__, json_encode(func_get_args()))
        );



        // TODO migrar para um Helper ou Util
        $drivers = [
            ['id' => 1, 'name' => 'John Doe'],
            ['id' => 2, 'name' => 'Jane Smith']
        ];

        $criteria = new Criteria([]);

        $this->mock->repository
            ->expects($this->once())
            ->method('all')
            ->with($criteria)
            ->willReturn($drivers);


        $this->facade = $this->mock->getObjectWithMockDependencies();
        $result = $this->facade->list($criteria);

        $this->assertEquals($drivers, $result);
    }



    public function testReadSuccess()
    {
        Log::info(
            sprintf("Testing the method %s with parameters: %s", __METHOD__, json_encode(func_get_args()))
        );

        $this->markTestSkipped('Not implemented yet');
    }

    public function testDeleteSuccess()
    {
        Log::info(
            sprintf("Testing the method %s with parameters: %s", __METHOD__, json_encode(func_get_args()))
        );

        $this->markTestSkipped('Not implemented yet');
    }
}
