<?php

namespace App\Http\Controllers\V1;

use App\Converters\DriverConverter;
use App\Enums\ErrorMessagesEnum;
use App\Exceptions\ApplicationException;
use App\Exceptions\RepositoryException;
use App\Exceptions\ServiceException;
use App\Http\Controllers\Controller;
use App\Http\Criteria\Criteria;
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
     *     path="/api/v1/drivers",
     *     summary="Lista os motoristas com filtros opcionais",
     *     tags={"Driver"},
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
     *         @OA\JsonContent(ref="#/components/schemas/DriverListResponse")
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
     *          response=500,
     *          description="Outros erros",
     *          @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     ),
     *     @OA\Response(
     *          response=503,
     *          description="Serviço indisponível",
     *          @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     ),
     * )
     * // TODO revisar os exemplos dos erros, pois estão todos com a mesma mensagem, qualquer coisa criar outros schemas ou ver como passar os valores para o schema
     */
    public function index(Request $request, DriverManagerFacade $manager)
    {
        $criteria = new Criteria($request->all());

        $user = $request->attributes->get('user') ?? null;
        Log::debug("Request user: $user");
        Log::debug(sprintf("Drivers list - request criteria: %s", json_encode($criteria->toArray())));

        try {
            $paginator = $manager->list($criteria);
            $metadata = new HateosMetadata($paginator);
            return ResponseHelper::success(DriverConverter::convertListFromArrayToResponse($paginator->items()), metadata: $metadata);
        } catch (ServiceException $e) {
            return ResponseHelper::error($e);
        } catch (RepositoryException $e) {
            return ResponseHelper::error($e);
        } catch (\Throwable $e) {
            return ResponseHelper::error(new ApplicationException(ErrorMessagesEnum::UNABLE_TO_LIST_DRIVERS, Response::HTTP_BAD_REQUEST, previous: $e));
        }

    }
    /**
     * @OA\Post(
     *     path="/api/v1/drivers",
     *     summary="Cria um novo motorista",
     *     tags={"Driver"},
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
     *         description="Driver criado com sucesso",
     *         @OA\JsonContent(ref="#/components/schemas/Driver")
     *     )
     * )
     */
    public function store(Request $request, DriverManagerFacade $manager): JsonResponse
    {

        $data = $request->all();

        try {
            $driver = $manager->create($data);
            return ResponseHelper::success(DriverConverter::convertFromArrayToResponse($driver), Response::HTTP_CREATED);
        } catch (ServiceException $e) {
            return ResponseHelper::error($e);
        } catch (\Throwable $e) {
            return ResponseHelper::error(new ApplicationException(ErrorMessagesEnum::UNABLE_TO_CREATE_DRIVER, 500, previous: $e));
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/drivers/{id}",
     *     summary="Remove um motorista",
     *     tags={"Driver"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID do driver",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Driver removido com sucesso"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Driver não encontrado"
     *     )
     * )
     */
    public function destroy($id): JsonResponse
    {
        // TODO incluir coluna active para deletar logicamente
        $driver = Driver::findOrFail($id);
        $driver->delete();

        return response()->json(null, 204);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/drivers/{id}/open-rides",
     *     summary="Lista corridas abertas para um motorista",
     *     tags={"Driver"},
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
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Ride")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Nenhuma corrida aberta",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="No rides waiting to be accepted")
     *         )
     *     )
     * )
     */
    public function getOpenRides($id): JsonResponse
    {
        // TODO I need to review
        $driver = Driver::findOrFail($id);
        $rides = $driver->getOpenRides();

        if ($rides->isEmpty()) {
            return response()->json([
                'message' => 'No rides waiting to be accepted',
            ], 404);
        }

        return response()->json($rides->map(function ($ride) {
            return [
                'id' => $ride->id,
                'status' => $ride->status,
                'drop_off' => $ride->drop_off,
                'pick_up' => $ride->pick_up,
                'passenger' => [
                    'name' => $ride->passenger_name,
                    'email' => $ride->passenger_email
                ]
            ];
        }));
    }
}
