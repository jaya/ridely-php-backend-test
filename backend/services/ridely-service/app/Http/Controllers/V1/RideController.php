<?php

namespace App\Http\Controllers\V1;

use App\Converters\DriverConverter;
use App\Enums\ErrorMessagesEnum;
use App\Exceptions\ApplicationException;
use App\Exceptions\RideException;
use App\Exceptions\ServiceException;
use App\Http\Controllers\Controller;
use App\Http\Criteria\EstimateRideCriteria;
use App\Http\Helpers\ResponseHelper;
use App\Models\Driver;
use App\Models\Ride;
use App\Services\Facades\RideManagerFacade;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class RideController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/rides/{id}",
     *     summary="Buscar detalhes de uma corrida",
     *     tags={"Ride"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID da corrida",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Detalhes da corrida",
     *         @OA\JsonContent(ref="#/components/schemas/RideFull")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Corrida não encontrada"
     *     )
     * )
     */
    public function show(string $id)
    {
        $ride = Ride::with('driver')->findOrFail($id);

        return response()->json([
            'id' => $ride->id,
            'status' => $ride->status,
            'pick_up' => $ride->pick_up,
            'drop_off' => $ride->drop_off,
            'driver' => $ride->driver ? [
                'name' => $ride->driver->name,
                'car' => [
                    'license_plate' => $ride->driver->car_license_plate,
                    'model' => $ride->driver->car_model,
                    'color' => $ride->driver->car_color
                ]
            ] : null
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/rides/{id}",
     *     summary="Remover uma corrida",
     *     tags={"Ride"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID da corrida",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Corrida removida"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Corrida não encontrada"
     *     )
     * )
     */
    public function destroy(string $id)
    {
        $ride = Ride::findOrFail($id);
        $ride->delete();
        return response()->noContent();
    }

    /**
     * @OA\Post(
     *     path="/api/v1/rides/request-driver",
     *     summary="Solicitar motorista",
     *     tags={"Ride"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"passenger", "pick_up", "drop_off"},
     *             @OA\Property(property="passenger", ref="#/components/schemas/Passenger"),
     *             @OA\Property(property="pick_up", type="string", example="Rua X, 200"),
     *             @OA\Property(property="drop_off", type="string", example="Av. Brasil, 1000")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Corrida criada",
     *         @OA\JsonContent(ref="#/components/schemas/RideFull")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Não há motoristas disponíveis",
     *         @OA\JsonContent(@OA\Property(property="error", type="string", example="We do not have drivers available"))
     *     )
     * )
     */
    public function requestDriver(Request $request): JsonResponse
    {
        $fields = $request->all();

        $driver = Driver::where('available', true)
            ->orderBy('activation_date', 'asc')
            ->first();

        if (!$driver) {
            return response()->json(['error' => 'We do not have drivers available'], 400);
        }

        $ride = Ride::create([
            'passenger_name' => $fields['passenger']['name'],
            'passenger_email' => $fields['passenger']['email'],
            'pick_up' => $fields['pick_up'],
            'drop_off' => $fields['drop_off'],
            'driver_id' => $driver->id
        ]);

        $ride->request();

        return response()->json([
            'id' => $ride->id,
            'status' => $ride->status,
            'drop_off' => $ride->drop_off,
            'pick_up' => $ride->pick_up,
            'driver' => [
                'name' => $driver->name,
                'car' => [
                    'color' => $driver->car_color,
                    'license_plate' => $driver->car_license_plate,
                    'model' => $driver->car_model
                ]
            ]
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/rides/cancel",
     *     summary="Cancelar corrida",
     *     tags={"Ride"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"id"},
     *             @OA\Property(property="id", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Corrida cancelada",
     *         @OA\JsonContent(ref="#/components/schemas/RideSimple")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Corrida não encontrada"
     *     )
     * )
     */
    public function cancelRide(Request $request): JsonResponse
    {
        $ride = Ride::findOrFail($request->id);
        $ride->cancel();

        return response()->json([
            'id' => $ride->id,
            'status' => $ride->status,
            'drop_off' => $ride->drop_off,
            'pick_up' => $ride->pick_up
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/rides/accept",
     *     summary="Aceitar corrida",
     *     tags={"Ride"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"id"},
     *             @OA\Property(property="id", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Corrida aceita",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="status", type="string", example="accepted"),
     *             @OA\Property(property="drop_off", type="string", example="Av. Brasil, 1000"),
     *             @OA\Property(property="pick_up", type="string", example="Rua X, 200"),
     *             @OA\Property(property="passenger", ref="#/components/schemas/Passenger")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Corrida ou motorista não encontrado"
     *     )
     * )
     */
    public function acceptRide(Request $request): JsonResponse
    {
        $ride = Ride::findOrFail($request->id);
        $driver = Driver::findOrFail($ride->driver_id);
        $ride->accept($driver);

        return response()->json([
            'id' => $ride->id,
            'status' => $ride->status,
            'drop_off' => $ride->drop_off,
            'pick_up' => $ride->pick_up,
            'passenger' => [
                'name' => $ride->passenger_name,
                'email' => $ride->passenger_email
            ]
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/rides/refuse",
     *     summary="Recusar corrida",
     *     tags={"Ride"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"id"},
     *             @OA\Property(property="id", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Corrida recusada",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="status", type="string", example="refused"),
     *             @OA\Property(property="drop_off", type="string", example="Av. Brasil, 1000"),
     *             @OA\Property(property="pick_up", type="string", example="Rua X, 200"),
     *             @OA\Property(property="passenger", ref="#/components/schemas/Passenger")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Corrida não encontrada"
     *     )
     * )
     */
    public function refuseRide(Request $request): JsonResponse
    {
        $ride = Ride::findOrFail($request->id);
        $ride->refuse();

        return response()->json([
            'id' => $ride->id,
            'status' => $ride->status,
            'drop_off' => $ride->drop_off,
            'pick_up' => $ride->pick_up,
            'passenger' => [
                'name' => $ride->passenger_name,
                'email' => $ride->passenger_email
            ]
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/rides/finish",
     *     summary="Finalizar corrida",
     *     tags={"Ride"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"id", "price"},
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="price", type="number", format="float", example=25.00)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Corrida finalizada",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="status", type="string", example="finished"),
     *             @OA\Property(property="drop_off", type="string", example="Av. Brasil, 1000"),
     *             @OA\Property(property="price", type="number", format="float", example=25.00),
     *             @OA\Property(property="passenger", ref="#/components/schemas/Passenger")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Corrida não encontrada"
     *     )
     * )
     */
    public function finishRide(Request $request): JsonResponse
    {
        $ride = Ride::findOrFail($request->id);
        $ride->finish();
        $ride->price = $request->price;
        $ride->save();

        return response()->json([
            'id' => $ride->id,
            'status' => $ride->status,
            'drop_off' => $ride->drop_off,
            'price' => $ride->price,
            'passenger' => [
                'name' => $ride->passenger_name,
                'email' => $ride->passenger_email
            ]
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/drivers/{driverId}/open-rides",
     *     summary="Listar corridas abertas para motorista",
     *     tags={"Ride"},
     *     @OA\Parameter(
     *         name="driverId",
     *         in="path",
     *         required=true,
     *         description="ID do motorista",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de corridas abertas",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/RideSimple")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Motorista indisponível",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Driver is not available"))
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Nenhuma corrida aberta",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="No rides waiting to be accepted"))
     *     )
     * )
     */
    public function getOpenRides($driverId): JsonResponse
    {
        $driver = Driver::findOrFail($driverId);
        
        if (!$driver->available) {
            return response()->json([
                'message' => 'Driver is not available',
            ], 400);
        }

        $rides = Ride::where('status', Ride::STATUS_REQUESTED)
            ->whereNull('driver_id')
            ->get();

        if ($rides->isEmpty()) {
            return response()->json([
                'message' => 'No rides waiting to be accepted',
            ], 404);
        }

        return response()->json($rides->map(function ($ride) {
            return [
                'id' => $ride->id,
                'passenger_name' => $ride->passenger_name,
                'passenger_email' => $ride->passenger_email,
                'pick_up' => $ride->pick_up,
                'drop_off' => $ride->drop_off,
                'status' => $ride->status,
            ];
        }));
    }

    public function estimateRide(Request $request, RideManagerFacade $facade): JsonResponse
    {
        $estimateRideData = new EstimateRideCriteria($request->all());
        try {

            $request->validate($estimateRideData->rules());

            $rideData = $facade->estimateRide($estimateRideData);
            return ResponseHelper::success($rideData, Response::HTTP_OK);

        } catch (ServiceException $e) {
            return ResponseHelper::error($e);
        } catch (RideException $e) {
            return ResponseHelper::error($e);
        } catch (ValidationException $e) {
            return ResponseHelper::error(ServiceException::invalidRequestParam($e->getMessage(), [], $e));
        } catch (\Throwable $e) {
            return ResponseHelper::error(new ApplicationException(ErrorMessagesEnum::UNABLE_TO_ESTIMATE_RIDE, Response::HTTP_INTERNAL_SERVER_ERROR, previous: $e));
        }

    }
}
