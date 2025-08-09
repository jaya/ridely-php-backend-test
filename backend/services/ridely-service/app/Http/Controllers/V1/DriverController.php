<?php

namespace App\Http\Controllers\V1;

use App\Converters\DriverConverter;
use App\Converters\RideConverter;
use App\Exceptions\ApplicationException;
use App\Http\Controllers\Controller;
use App\Http\Criteria\Driver\CreateDriverCriteria;
use App\Http\Criteria\ListCriteria;
use App\Http\Hateos\HateosHelper;
use App\Http\Hateos\HateosMetadata;
use App\Http\Helpers\ResponseHelper;
use App\Models\Driver;
use App\Services\Facades\DriverManagerFacade;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class DriverController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/drivers/{id}",
     *     summary="Buscar um motorista",
     *     tags={"Drivers"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID do motorista",
     *         @OA\Schema(type="string", example="1")
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="Motorista encontrado com sucesso",
     *          @OA\JsonContent(ref="#/components/schemas/DriverShowSuccessResponse")
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
    public function show(string $id, DriverManagerFacade $facade): JsonResponse
    {
        try {
            $path = str_replace("/${id}", "", request()->fullUrl());
            $driver = $facade->find($id);
            $driverResponse = DriverConverter::convertFromArrayToResponse($driver->toArray());
            $driverResponse = HateosHelper::appendHateosLinks($driverResponse, $path, $driver->id);
            return ResponseHelper::success($driverResponse);
        } catch (ApplicationException $e) {
            return ResponseHelper::error($e);
        }

    }
    /**
     * @OA\Get(
     *     path="/api/v1/drivers",
     *     summary="Lista os motoristas com filtros opcionais",
     *     tags={"Drivers"},
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
     *         @OA\JsonContent(ref="#/components/schemas/DriverListSuccessResponse")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Erro de validação",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponseInvalidParams")
     *     ),
     *     @OA\Response(
     *          response=401,
     *          description="Não autorizado",
     *          @OA\JsonContent(ref="#/components/schemas/ErrorResponseUnauthorized")
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
    public function listDrivers(Request $request, DriverManagerFacade $facade)
    {
        try {


            $criteria = new ListCriteria($request->all());
            Log::debug(sprintf("Drivers list - request criteria: %s", json_encode($criteria->toArray())));

            // The request will be validated inside the facade/service
            // $request->validate($criteria->rules());

            $paginator = $facade->list($criteria);
            $metadata = new HateosMetadata($paginator);
            return ResponseHelper::success(DriverConverter::convertListFromArrayToResponse($paginator->items()), metadata: $metadata);

        } catch (ApplicationException $e) {
            return ResponseHelper::error($e);
        }

    }

    /**
     * @OA\Post(
     *     path="/api/v1/drivers",
     *     summary="Cria um novo motorista",
     *     tags={"Drivers"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "car"},
     *             @OA\Property(property="name", type="string", example="Carlos"),
     *             @OA\Property(property="car", ref="#/components/schemas/Car"),
     *             @OA\Property(property="available", type="boolean", example=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Motoristas criado com sucesso",
     *         @OA\JsonContent(ref="#/components/schemas/DriverCreatedSuccessResponse")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Erro de validação",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponseInvalidParams")
     *     ),
     *     @OA\Response(
     *          response=401,
     *          description="Não autorizado",
     *          @OA\JsonContent(ref="#/components/schemas/ErrorResponseUnauthorized")
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
    public function store(Request $request, DriverManagerFacade $facade): JsonResponse
    {

        try {

            $criteria = new CreateDriverCriteria($request->all());
            // The request will be validated inside the facade/service
            // $request->validate($criteria->rules());

            $path = request()->fullUrl();
            $driver = $facade->create($criteria);

            $driverResponse = DriverConverter::convertFromArrayToResponse($driver->toArray());
            $driverResponse = HateosHelper::appendHateosLinks($driverResponse, $path, $driver->id);
            return ResponseHelper::success($driverResponse, Response::HTTP_CREATED);
        } catch (ApplicationException $e) {
            return ResponseHelper::error($e);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/drivers/{id}",
     *     summary="Remove um motorista",
     *     tags={"Drivers"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID do motorista",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Motorista removido com sucesso"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Erro de validação",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponseInvalidParams")
     *     ),
     *     @OA\Response(
     *          response=401,
     *          description="Não autorizado",
     *          @OA\JsonContent(ref="#/components/schemas/ErrorResponseUnauthorized")
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
    public function destroy($id, DriverManagerFacade $facade)
    {
        $facade->delete($id);
        return response()->noContent();
    }

    /**
     * @OA\Get(
     *     path="/api/v1/drivers/{id}/get-rides",
     *     summary="Lista corridas abertas para um motorista",
     *     tags={"Drivers"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID do driver",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de rides abertas",
     *         @OA\JsonContent(ref="#/components/schemas/DriverGetRidesSuccessResponse")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Erro de validação",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponseInvalidParams")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Nenhuma corrida aberta",
     *          @OA\JsonContent(ref="#/components/schemas/ErrorResponseNoRidesWaiting")
     *     ),
     *     @OA\Response(
     *          response=401,
     *          description="Não autorizado",
     *          @OA\JsonContent(ref="#/components/schemas/ErrorResponseUnauthorized")
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
    public function getOpenRides($id, Request $request, DriverManagerFacade $facade): JsonResponse
    {
        try {
            $criteria = new ListCriteria($request->all());
            $paginator = $facade->getOpenRides($id, $criteria);
            $metadata = new HateosMetadata($paginator);
            return ResponseHelper::success(RideConverter::convertListFromArrayToResponse($paginator->items()), metadata: $metadata);
        } catch (ApplicationException $e) {
            return ResponseHelper::error($e);
        }


//        return response()->json($rides->map(function ($ride) {
//            return [
//                'id' => $ride->id,
//                'status' => $ride->status,
//                'drop_off' => $ride->drop_off,
//                'pick_up' => $ride->pick_up,
//                'passenger' => [
//                    'name' => $ride->passenger_name,
//                    'email' => $ride->passenger_email
//                ]
//            ];
//        }));
    }
}
