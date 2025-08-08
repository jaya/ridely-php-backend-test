<?php

namespace App\Console\Commands;

use App\Enums\RedisStreamsEnum;
use App\Enums\RideEstimateStatusEnum;
use App\Http\Criteria\EstimateRideCriteria;
use App\Services\Interfaces\EstimateRideServiceInterface;
use GuzzleHttp\Exception\ServerException;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
class ProcessRideEstimates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:process-ride-estimates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';


    /**
     * Execute the console command.
     */
    public function handle()
    {
        $redis = Redis::connection('streams');
        $streamName = RedisStreamsEnum::RIDE_ESTIMATES_STREAM->value;

        Log::info('Starting ProcessRideEstimates command');
        // Check if the Redis stream exists
        try {
            $redis->xgroup('CREATE', $streamName, 'estimate_group', '$', 'MKSTREAM');
        } catch (\Exception $e) {
            // Grupo já existe, ignorar
            Log::warning($e->getMessage());
        }

        Log::info('...');

        /**
         * @var EstimateRideServiceInterface $estimateRideService
         */
        $estimateRideService = app(EstimateRideServiceInterface::class);

        Log::info('...');

        while (true) {
//            Log::info('While loop happening');

            $entries = $redis->xreadgroup('estimate_group', 'consumer-1', [
                $streamName => '>'
            ], 1, 5000);

            Log::info('Received entries from Redis stream', ['entries' => $entries]);

            if (!empty($entries)) {
                foreach ($entries[$streamName] ?? [] as $id => $data) {
                    Log::info('Found entry in Redis stream', ['id' => $id, 'data' => $data]);

                    $criteria = new EstimateRideCriteria([
                        'pick_up' => $data['pick_up'] ?? null,
                        'drop_off' => $data['drop_off'] ?? null,
                    ]);
                    $rideId = $data['ride_id'];
                    $estimateId = $data['estimate_id'];

                    Log::info("Processing estimate for ride ID: $rideId, estimate ID: $estimateId");


                    $estimateRideService->checkDatabase();

                    try {
                        $estimateRideService->estimateRide($criteria, $estimateId);
                        $redis->xack(RedisStreamsEnum::RIDE_ESTIMATES_STREAM->value, 'estimate_group', [$id]);
                    } catch (ServerException $e) {
                        Log::error("Error processing estimate for ride ID: $rideId, estimate ID: $estimateId. Error: " . $e->getMessage());
                        $estimateRideService->updateEstimateRide($estimateId, RideEstimateStatusEnum::FAILED);
                    }


                }
            }

            sleep(1); // evitar busy loop
        }

    }
}
