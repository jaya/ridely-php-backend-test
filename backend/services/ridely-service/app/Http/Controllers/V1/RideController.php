<?php

namespace App\Http\Controllers\V1;

use App\Converters\RideConverter;
use App\Converters\RideEstimateConverter;
use App\Exceptions\ApplicationException;
use App\Http\Controllers\Controller;
use App\Http\Criteria\ListCriteria;
use App\Http\Criteria\Ride\CreateRideCriteria;
use App\Http\Hateos\HateosHelper;
use App\Http\Hateos\HateosMetadata;
use App\Http\Helpers\ResponseHelper;
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
     *          response=200,
     *          description="Corrida encontrada",
     *          @OA\JsonContent(ref="#/components/schemas/RideShowSuccessResponse")
     *      ),
     *     @OA\Response(
     *          response=401,
     *          description="Não autorizado",
     *          @OA\JsonContent(ref="#/components/schemas/ErrorResponseUnauthorized")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Corrida não encontrada",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponseRideNotFound")
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erro interno no serviço",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponseInternalError")
     *     ),
     *     @OA\Response(
     *         response=503,
     *         description="Serviço indisponivel",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponseServiceUnavailable")
     *     ),
     * )
     */
    public function show(string $id, RideManagerFacade $facade): JsonResponse
    {
        try {
            $path = str_replace("/${id}", "", request()->fullUrl());
            $ride = $facade->find($id);
            $rideResponse = RideConverter::convertFromArrayToResponse($ride->toArray());
            $rideResponse = HateosHelper::appendRideHateosLinks($rideResponse, $path, $ride->id);
            return ResponseHelper::success($rideResponse);
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
     *          response=401,
     *          description="Não autorizado",
     *          @OA\JsonContent(ref="#/components/schemas/ErrorResponseUnauthorized")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Corrida não encontrada",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponseRideNotFound")
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erro interno no serviço",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponseInternalError")
     *     ),
     *     @OA\Response(
     *         response=503,
     *         description="Serviço indisponivel",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponseServiceUnavailable")
     *     ),
     * )
     */
    public function destroy(string $id, RideManagerFacade $facade)
    {
        $facade->delete($id);
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
     *         @OA\JsonContent(ref="#/components/schemas/RideResponseSuccess")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Não há motoristas disponíveis",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponseRideUnavailable")
     *     ),
     *     @OA\Response(
     *          response=401,
     *          description="Não autorizado",
     *          @OA\JsonContent(ref="#/components/schemas/ErrorResponseUnauthorized")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Corrida não encontrada",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponseRideNotFound")
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erro interno no serviço",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponseInternalError")
     *     ),
     *     @OA\Response(
     *         response=503,
     *         description="Serviço indisponivel",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponseServiceUnavailable")
     *     ),
     * )
     * OK
     */
    public function requestDriver(Request $request, RideManagerFacade $facade): JsonResponse
    {
        try {
            Log::info('Requesting a driver...');
            $criteria = new CreateRideCriteria($request->all());

            // The request will be validated inside the facade/service
            //$request->validate($criteria->rules());

//            Log::info('Request validated successfully');

            $path = str_replace("/request-driver", "", $request->fullUrl());
            $ride = $facade->create($criteria);
            $rideResponse = RideConverter::convertFromArrayToResponse($ride->toArray());
            $rideResponse = HateosHelper::appendRideHateosLinks($rideResponse, $path, $ride->id);
            return ResponseHelper::success($rideResponse, Response::HTTP_CREATED);
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
     *         @OA\JsonContent(ref="#/components/schemas/RideCancelledResponseSuccess")
     *     ),
     *     @OA\Response(
     *          response=400,
     *          description="Corrida com status diferente de 'requested'",
     *          @OA\JsonContent(ref="#/components/schemas/ErrorResponseRideWithInvalidState")
     *    ),
     *     @OA\Response(
     *          response=401,
     *          description="Não autorizado",
     *          @OA\JsonContent(ref="#/components/schemas/ErrorResponseUnauthorized")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Corrida não encontrada",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponseRideNotFound")
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erro interno no serviço",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponseInternalError")
     *     ),
     *     @OA\Response(
     *         response=503,
     *         description="Serviço indisponivel",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponseServiceUnavailable")
     *     ),
     * )
     * OK
     */
    public function cancelRide($id, RideManagerFacade $facade): JsonResponse
    {

        try {

            Log::debug("Cancel ride request received with ID: " . $id);


            $path = str_replace("/${id}/cancel-ride", "", request()->fullUrl());
            $ride = $facade->cancelRide($id);

            $rideResponse = RideConverter::convertFromArrayToResponse($ride->toArray());
            // Removing data from response as it is not needed in this context
            unset($rideResponse['driver']);
            unset($rideResponse['passenger']);

            $rideResponse = HateosHelper::appendRideHateosLinks($rideResponse, $path, $ride->id);
            return ResponseHelper::success($rideResponse);

        } catch (ApplicationException $e) {
            return ResponseHelper::error($e);
        }

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
     *         description="Corrida criada com sucesso",
     *         @OA\JsonContent(ref="#/components/schemas/AcceptRequestSuccess")
     *     ),
     *     @OA\Response(
     *          response=401,
     *          description="Não autorizado",
     *          @OA\JsonContent(ref="#/components/schemas/ErrorResponseUnauthorized")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Corrida invalida,sem motorista ou não encontrada",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponseRideNotFound")
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erro interno no serviço",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponseInternalError")
     *     ),
     *     @OA\Response(
     *         response=503,
     *         description="Serviço indisponivel",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponseServiceUnavailable")
     *     ),
     * )
     * OK
     */
    public function acceptRide($id, RideManagerFacade $facade): JsonResponse
    {
        try {
            $ride = $facade->acceptRide($id);
            $path = str_replace("/{$id}/accept-ride", "", request()->fullUrl());
            $rideResponse = RideConverter::convertFromArrayToResponse($ride->toArray());
            $rideResponse = HateosHelper::appendRideHateosLinks($rideResponse, $path, $ride->id);
            return ResponseHelper::success($rideResponse);

        } catch (ApplicationException $e) {
            return ResponseHelper::error($e);
        }

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
     *          response=200,
     *          description="Corrida recusada",
     *          @OA\JsonContent(ref="#/components/schemas/RideRefusedSuccess")
     *      ),
     *     @OA\Response(
     *          response=401,
     *          description="Não autorizado",
     *          @OA\JsonContent(ref="#/components/schemas/ErrorResponseUnauthorized")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Corrida não encontrada",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponseRideNotFound")
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erro interno no serviço",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponseInternalError")
     *     ),
     *     @OA\Response(
     *         response=503,
     *         description="Serviço indisponivel",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponseServiceUnavailable")
     *     ),
     * )
     */
    public function refuseRide($id, RideManagerFacade $facade): JsonResponse
    {
        try {

            Log::debug("Cancel ride request received with ID: " . $id);


            $path = str_replace("/${id}/refuse-ride", "", request()->fullUrl());
            $ride = $facade->refuseRide($id);

            $rideResponse = RideConverter::convertFromArrayToResponse($ride->toArray());
            // Removing data from response as it is not needed in this context
            unset($rideResponse['driver']);
//            unset($rideResponse['passenger']);

            $rideResponse = HateosHelper::appendRideHateosLinks($rideResponse, $path, $ride->id);
            return ResponseHelper::success($rideResponse);

        } catch (ApplicationException $e) {
            return ResponseHelper::error($e);
        }
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
     *         @OA\JsonContent(ref="#/components/schemas/RideFinishedResponseSuccess")
     *     ),
     *     @OA\Response(
     *          response=400,
     *          description="Corrida com status diferente de 'accepted'",
     *          @OA\JsonContent(ref="#/components/schemas/ErrorResponseRideWithInvalidState")
     *    ),
     *     @OA\Response(
     *          response=401,
     *          description="Não autorizado",
     *          @OA\JsonContent(ref="#/components/schemas/ErrorResponseUnauthorized")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Corrida não encontrada",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponseRideNotFound")
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erro interno no serviço",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponseInternalError")
     *     ),
     *     @OA\Response(
     *         response=503,
     *         description="Serviço indisponivel",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponseServiceUnavailable")
     *     ),
     * )
     * )
     */
    public function finishRide($id, RideManagerFacade $facade): JsonResponse
    {
        try {

            Log::debug("Finish ride request received with ID: " . $id);


            $path = str_replace("/${id}/finish-ride", "", request()->fullUrl());
            $ride = $facade->finishRide($id);

            $rideResponse = RideConverter::convertFromArrayToResponse($ride->toArray());
            // Removing data from response as it is not needed in this context
            unset($rideResponse['driver']);
//            unset($rideResponse['passenger']);

            $rideResponse = HateosHelper::appendRideHateosLinks($rideResponse, $path, $ride->id);
            return ResponseHelper::success($rideResponse);

        } catch (ApplicationException $e) {
            return ResponseHelper::error($e);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/v1/rides/without-driver",
     *     summary="Lista as corridas sem motorista",
     *     tags={"Rides"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Referente a qual página dos itens da listagem deseja buscar",
     *         required=false,
     *         @OA\Schema(type="integer", minimum=0)
     *     ),
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         description="Quantidade de resultados por página (máx: 100)",
     *         required=false,
     *         @OA\Schema(type="integer", minimum=1, maximum=100)
     *     ),
     *     @OA\Parameter(
     *         name="order_by",
     *         in="query",
     *         description="Campo para ordenação (ex: name, created_at)",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="sort_by",
     *         in="query",
     *         description="Direção da ordenação: asc ou desc",
     *         required=false,
     *         @OA\Schema(type="string", enum={"asc", "desc"})
     *     ),
     *     @OA\Parameter(
     *         name="fields",
     *         in="query",
     *         description="Campos específicos a retornar (ex: name,available)",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de motoristas retornada com sucesso",
     *         @OA\JsonContent(ref="#/components/schemas/RideListResponse")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Erro de validação dos critérios",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     ),
     *     @OA\Response(
     *          response=401,
     *          description="Não autorizado",
     *          @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erro interno no serviço",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponseInternalError")
     *     ),
     *     @OA\Response(
     *          response=503,
     *          description="Serviço indisponível",
     *          @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     ),
     * )
     * // TODO revisar os exemplos dos erros, pois estão todos com a mesma mensagem, qualquer coisa criar outros schemas ou ver como passar os valores para o schema
     */
    public function listRidesWithoutDriver(Request $request, RideManagerFacade $facade): JsonResponse
    {
        try {
            $criteria = new ListCriteria($request->all());
            $paginator = $facade->listRidesWithoutDriver($criteria);
            $metadata = new HateosMetadata($paginator);
            return ResponseHelper::success(RideConverter::convertListFromArrayToResponse($paginator->items()), metadata: $metadata);
        } catch (ApplicationException $e) {
            return ResponseHelper::error($e);
        }

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
     *     @OA\Response(
     *          response=401,
     *          description="Não autorizado",
     *          @OA\JsonContent(ref="#/components/schemas/ErrorResponseUnauthorized")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Corrida não encontrada",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponseRideNotFound")
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erro interno no serviço",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponseInternalError")
     *     ),
     *     @OA\Response(
     *         response=503,
     *         description="Serviço indisponivel",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponseServiceUnavailable")
     *     ),
     * )
     * OK
     */
    public function estimateRide($id, Request $request, RideManagerFacade $facade): JsonResponse
    {
        Log::debug("Estimate ride for ride ID: $id with data: " . json_encode($request->all()));
        try {

            $estimate = $facade->estimateRide($id);
            Log::debug("Ride estimate created successfully");
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
     *     @OA\Response(
     *          response=401,
     *          description="Não autorizado",
     *          @OA\JsonContent(ref="#/components/schemas/ErrorResponseUnauthorized")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Corrida não encontrada",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponseRideNotFound")
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erro interno no serviço",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponseInternalError")
     *     ),
     *     @OA\Response(
     *         response=503,
     *         description="Serviço indisponivel",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponseServiceUnavailable")
     *     ),
     * )
     * OK
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
