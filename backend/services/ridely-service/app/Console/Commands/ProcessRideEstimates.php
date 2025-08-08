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
    protected $signature = 'queue:process-ride-estimates';

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
        $maxItems = 10;
        $redis = Redis::connection('streams');
        $streamName = RedisStreamsEnum::RIDE_ESTIMATES_STREAM->value;

        Log::info('Starting ProcessRideEstimates command');
        // Check if the Redis stream exists
        try {
            $redis->xgroup('CREATE', $streamName, 'estimate_group', '$', 'MKSTREAM');
        } catch (\Exception $e) {
            Log::warning($e->getMessage());
        }

        /**
         * @var EstimateRideServiceInterface $estimateRideService
         */
        $estimateRideService = app(EstimateRideServiceInterface::class);

        while (true) {


            $entries = $redis->xreadgroup('estimate_group', 'consumer-1', [
                $streamName => '>'
            ], $maxItems, 5000);

            if (!empty($entries)) {
                $count = count($entries[$streamName] ?? []);
                Log::info("======================================================================");
                Log::info("Received entries from Redis stream ($count)");
                Log::info("======================================================================");
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
                        $estimateRideService->estimateRide($estimateId, $criteria);
                        $redis->xack(RedisStreamsEnum::RIDE_ESTIMATES_STREAM->value, 'estimate_group', [$id]);
                    } catch (ServerException $e) {
                        Log::error("Error processing estimate for ride ID: $rideId, estimate ID: $estimateId. Error: " . $e->getMessage());
                        $estimateRideService->updateStatus($estimateId, RideEstimateStatusEnum::FAILED);
                    }


                }
            }

            // avoid busy loop
            sleep(1);
        }

    }
}
