<?php

namespace Tests\Mocks;

use App\Http\Criteria\ListCriteria;
use App\Models\Driver;
use Illuminate\Pagination\LengthAwarePaginator;
use Tests\Helpers\DriverHelper;

trait DriverMocks
{
    /**
     * @return LengthAwarePaginator
     */
    public function mockPaginator(): LengthAwarePaginator
    {
        $sampleList = DriverHelper::getDriversModelListSample();
        $paginator = new LengthAwarePaginator(
            $sampleList,
            count($sampleList),
            ListCriteria::LIMIT,
            ListCriteria::PAGE,
            [
                'path' => request()->url(),
                'query' => request()->query(),
            ]
        );
        return $paginator;
    }

    /**
     * @return array
     */
    public function mockDriverWithData(): array
    {
        $data = DriverHelper::getDriverSample();
        $fakeDriver = $this->createModelMockWithData(Driver::class, $data);
        return array($data, $fakeDriver);
    }

    /**
     * @return Driver
     */
    public function mockDriver(): Driver
    {
        $data = DriverHelper::getDriverSample();
        return $this->createModelMockWithData(Driver::class, $data);
    }

    /**
     * @return Driver
     */
    public function mockDriverModelCreate(): Driver
    {
        $fakeDriver = $this->mockDriver();

        $this->driverModelMock->shouldReceive('create')
            ->once()
            ->andReturn($fakeDriver);
        return $fakeDriver;
    }

    /**
     * @return void
     */
    public function mockDriverModelAllDrivers(): void
    {
        $paginator = $this->mockPaginator();
        $this->driverModelMock->shouldReceive('allDrivers')
            ->withAnyArgs()
            ->once()
            ->andReturn($paginator);
    }
}