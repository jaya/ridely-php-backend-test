<?php

namespace App\Http\Controllers\V1;

use App\Converters\DriverConverter;
use App\Exceptions\ServiceException;
use App\Http\Controllers\Controller;
use App\Http\Criteria\Criteria;
use App\Models\Driver;
use App\Services\DriverManagerFacade;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DriverController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/drivers",
     *     summary="Lista os motoristas com filtros opcionais",
     *     tags={"Driver"},
     *     @OA\Parameter(
     *         name="offset",
     *         in="query",
     *         description="Deslocamento (ponto de início da listagem)",
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
     *         description="Lista de motoristas",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Driver")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Erro de validação dos critérios"
     *     ),
     *     @OA\Response(
     *          response=422,
     *          description="Outros erros"
     *      ),
     *     @OA\Response(
     *          response=401,
     *          description="Não autorizado"
     *       )
     * )
     */
    public function index(Request $request, DriverManagerFacade $manager)
    {
        $criteria = new Criteria($request->all());

        Log::info(sprintf("Request criteria: %s", json_encode($criteria->toArray())));

        try {
            $drivers = $manager->list($criteria);

            return response()->json([
                'success' => true,
                'label' => 'success',
                'code' => 0,
                'message' => 'Success',
                'data' => $drivers,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'label' => 'error',
                'code' => 1,
                'message' => $e->getMessage(),
                'detail' => config('app.debug') ? $e->getTrace() : null,
            ], $e instanceof ServiceException ? 400 : 422);
        }

    }
    /**
     * @OA\Post(
     *     path="/api/v1/drivers",
     *     summary="Cria um novo motorista",
     *     tags={"Driver"},
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
        $driver = $manager->create($data);

        $converter = new DriverConverter();
        return response()->json($converter->convertFromModelToArray($driver), 201);

        # TODO implemetar VO e converter
//        return response()->json([
//            'id' => $driver->id,
//            'name' => $driver->name,
//            'car' => [
//                'license_plate' => $driver->car_license_plate,
//                'model' => $driver->car_model,
//                'color' => $driver->car_color
//            ],
//            'available' => $driver->available,
//        ], 201);

//        $car = $data['car'] ?? [];
//
//        $driver = Driver::create([
//            'name' => $data['name'],
//            'car_license_plate' => $car['license_plate'] ?? null,
//            'car_model' => $car['model'] ?? null,
//            'car_color' => $car['color'] ?? null,
//            'available' => $data['available'] ?? true,
//        ]);
//
//        return response()->json([
//            'id' => $driver->id,
//            'name' => $driver->name,
//            'car' => [
//                'license_plate' => $driver->car_license_plate,
//                'model' => $driver->car_model,
//                'color' => $driver->car_color
//            ],
//            'available' => $driver->available,
//        ]);
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
