<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Driver;
use App\Models\Ride;
use App\Exceptions\DriverException;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;

class DriverController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/drivers",
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
    public function store(Request $request): JsonResponse
    {
        $data = $request->all();
        $car = $data['car'] ?? [];

        $driver = Driver::create([
            'name' => $data['name'],
            'car_license_plate' => $car['license_plate'] ?? null,
            'car_model' => $car['model'] ?? null,
            'car_color' => $car['color'] ?? null,
            'available' => $data['available'] ?? true,
        ]);

        return response()->json([
            'id' => $driver->id,
            'name' => $driver->name,
            'car' => [
                'license_plate' => $driver->car_license_plate,
                'model' => $driver->car_model,
                'color' => $driver->car_color
            ],
            'available' => $driver->available,
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/drivers/{id}",
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
     *     path="/api/drivers/{id}/open-rides",
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
