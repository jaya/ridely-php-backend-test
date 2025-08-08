<?php

namespace App\Http\Controllers\V1;

use App\Converters\RideEstimateConverter;
use App\Converters\RideConverter;
use App\Enums\RideStatusEnum;
use App\Exceptions\ApplicationException;
use App\Exceptions\RideException;
use App\Http\Controllers\Controller;
use App\Http\Criteria\EstimateRideCriteria;
use App\Http\Criteria\Ride\CreateRideCriteria;
use App\Http\Helpers\ResponseHelper;
use App\Models\Driver;
use App\Models\Ride;
use App\Services\Facades\RideManagerFacade;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class RideController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/rides/{id}",
     *     summary="Buscar detalhes de uma corrida",
     *     tags={"Rides"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID da corrida",
     *         @OA\Schema(type="string", example="1")
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
     * TODO revisar por causa do HATEOS
     */
    public function show(string $id, RideManagerFacade $facade): JsonResponse
    {
        try {
            $ride = $facade->find($id);
            return ResponseHelper::success(($ride) ? RideConverter::convertFromModelToResponse($ride): null);
        } catch (ApplicationException $e) {
            return ResponseHelper::error($e);
        }

    }

    /**
     * @OA\Delete(
     *     path="/api/v1/rides/{id}",
     *     summary="Remover uma corrida",
     *     tags={"Rides"},
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
     *     tags={"Rides"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"passenger", "pick_up", "drop_off"},
     *             @OA\Property(property="passenger", ref="#/components/schemas/Passenger"),
     *             @OA\Property(property="pick_up", type="string", example="Avenida Beira Mar, 25"),
     *             @OA\Property(property="drop_off", type="string", example="Avenida Euclides Figueiredo, 65")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Corrida criada com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="label", type="string", example="success"),
     *             @OA\Property(property="code", type="integer", example=0),
     *             @OA\Property(property="message", type="string", example="Success"),
     *             @OA\Property(property="data", ref="#/components/schemas/RideFull")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Não há motoristas disponíveis",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="label", type="string", example="common.error.ride_drivers_available"),
     *             @OA\Property(property="code", type="integer", example=63),
     *             @OA\Property(property="message", type="string", example="We do not have drivers available"),
     *             @OA\Property(
     *               property="params",
     *               type="array",
     *               description="Parametros da requisição",
     *               @OA\Items(type="string", example="id")
     *            ),
     *             @OA\Property(
     *               property="detail",
     *               type="array",
     *               description="Stack trace (visível apenas em ambiente de desenvolvimento)",
     *               @OA\Items(type="object")
     *            )
     *         )
     *     )
     * )
     */
    public function requestDriver(Request $request, RideManagerFacade $facade): JsonResponse
    {
        try {
            $criteria = new CreateRideCriteria($request->all());
            $request->validate($criteria->rules());

            $ride = $facade->create($criteria);
            return ResponseHelper::success(RideConverter::convertFromArrayToResponse($ride->toArray()), Response::HTTP_CREATED);
        } catch (ApplicationException $e) {
            return ResponseHelper::error($e);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/v1/rides/{id}/cancel-ride",
     *     summary="Cancelar corrida",
     *     tags={"Rides"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID da corrida",
     *         @OA\Schema(type="integer", example=1)
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
    public function cancelRide($id): JsonResponse
    {
        Log::debug("Cancel ride request received with ID: " . $id);
        $ride = Ride::findOrFail($id);
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
     *     path="/api/v1/rides/{id}/accept-ride",
     *     summary="Aceitar corrida",
     *     tags={"Rides"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID da corrida",
     *         @OA\Schema(type="integer", example=1)
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
    public function acceptRide($id): JsonResponse
    {
        //$id = $request->id;
        $ride = Ride::findOrFail($id);
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
     *     path="/api/v1/rides/{id}/refuse-ride",
     *     summary="Recusar corrida",
     *     tags={"Rides"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID da corrida",
     *         @OA\Schema(type="integer", example=1)
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
    public function refuseRide($id): JsonResponse
    {
        $ride = Ride::findOrFail($id);
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
     *     path="/api/v1/rides/{id}/finish-ride",
     *     summary="Finalizar corrida",
     *     tags={"Rides"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID da corrida",
     *         @OA\Schema(type="integer", example=1)
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

    public function getOpenRides($driverId): JsonResponse
    {
        // TODO preciso revisar, parece duplicado de alguma forma
        $driver = Driver::findOrFail($driverId);

        if (!$driver->available) {
            return response()->json([
                'message' => 'Driver is not available',
            ], 400);
        }

        $rides = Ride::where('status', RideStatusEnum::REQUESTED)
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

    /**
     * @OA\Post(
     *     path="/api/v1/rides/{id}/estimate-ride",
     *     summary="Estimar corrida manualmente",
     *     description="Retorna a estimativa de distância, duração e preço de uma corrida.",
     *     operationId="estimateRide",
     *     tags={"Rides"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID da corrida",
     *         @OA\Schema(type="number", example="1")
     *     ),
     *
     *     @OA\Response(
     *         response=201,
     *         description="Estimativa gerada com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="label", type="string", example="success"),
     *             @OA\Property(property="code", type="integer", example=0),
     *             @OA\Property(property="message", type="string", example="Success"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="distance_km", type="number", format="float", example=12.5),
     *                 @OA\Property(property="duration_min", type="number", format="float", example=18.3),
     *                 @OA\Property(property="price_estimate", type="number", format="float", example=23.75)
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=400,
     *         description="Erro de validação ou corrida não encontrada",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="label", type="string", example="common.error.invalid_request_param"),
     *             @OA\Property(property="code", type="integer", example=61),
     *             @OA\Property(property="message", type="string", example="Ride not found"),
     *             @OA\Property(
     *               property="params",
     *               type="array",
     *               description="Parametros da requisição",
     *               @OA\Items(type="string", example="id")
     *            ),
     *             @OA\Property(
     *               property="detail",
     *               type="array",
     *               description="Stack trace (visível apenas em ambiente de desenvolvimento)",
     *               @OA\Items(type="object")
     *            )
     *         )
     *     )
     * )
     *
     *
     */
    public function estimateRide($id, Request $request, RideManagerFacade $facade): JsonResponse
    {
        Log::debug("Estimate ride for ride ID: $id with data: " . json_encode($request->all()));
        try {

            $estimate = $facade->estimateRide($id);
            return ResponseHelper::success(RideEstimateConverter::convertFromModelToResponse($estimate), Response::HTTP_CREATED);

        } catch (ApplicationException $e) {
            return ResponseHelper::error($e);
        }

    }

    /**
     * @OA\Get(
     *     path="/api/v1/rides/{id}/estimate-ride",
     *     summary="Consultar uma estimativa de corrida",
     *     description="Retorna a estimativa de distância, duração e preço de uma corrida.",
     *     operationId="getRideEstimate",
     *     tags={"Rides"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID da corrida",
     *         @OA\Schema(type="number", example="1")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="A estimativa consultada",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="label", type="string", example="success"),
     *             @OA\Property(property="code", type="integer", example=0),
     *             @OA\Property(property="message", type="string", example="Success"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="distance_km", type="number", format="float", example=12.5),
     *                 @OA\Property(property="duration_min", type="number", format="float", example=18.3),
     *                 @OA\Property(property="price_estimate", type="number", format="float", example=23.75)
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=400,
     *         description="Erro de validação ou corrida não encontrada",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="label", type="string", example="common.error.invalid_request_param"),
     *             @OA\Property(property="code", type="integer", example=61),
     *             @OA\Property(property="message", type="string", example="Ride not found"),
     *             @OA\Property(
     *               property="params",
     *               type="array",
     *               description="Parametros da requisição",
     *               @OA\Items(type="string", example="id")
     *            ),
     *             @OA\Property(
     *               property="detail",
     *               type="array",
     *               description="Stack trace (visível apenas em ambiente de desenvolvimento)",
     *               @OA\Items(type="object")
     *            )
     *         )
     *     )
     * )
     *
     *
     */
    public function getRideEstimate($id, Request $request, RideManagerFacade $facade)
    {
        Log::debug("Estimate ride for ride ID: $id with data: " . json_encode($request->all()));
        try {

            $estimate = $facade->findEstimateRideByRideId($id);
            return ResponseHelper::success(RideEstimateConverter::convertFromModelToResponse($estimate), Response::HTTP_OK);

        } catch (ApplicationException $e) {
            return ResponseHelper::error($e);
        }
    }
}
