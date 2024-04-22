<?php

namespace App\Controller\Api;

use App\Entity\WorkTime;
use App\Service\WorkTimeService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use OpenApi\Annotations as OA;

#[Route('/api/v1/work-time')]
class WorkTimeController extends AbstractController
{
    private WorkTimeService $workTimeService;
    private SerializerInterface $serializer;

    public function __construct(
        WorkTimeService $workTimeService,
        SerializerInterface $serializer
    ) {
        $this->workTimeService = $workTimeService;
        $this->serializer = $serializer;
    }

    /**
     * @OA\Get(
     *     summary="Get a list of WorkTime",
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/WorkTime")
     *         )
     *     )
     * )
     * @OA\Tag(name="WorkTime")
     *
     * Retrieves a list of WorkTime.
     *
     * @param Request $request The HTTP request object.
     * @return JsonResponse A JSON response containing the list of WorkTime or an error message.
     * @throws \JsonException
     */
    #[Route('/', name: 'app_work_time_index', methods: ['GET'])]
    public function index(Request $request): JsonResponse
    {

        $queryBuilder = $request->attributes->get('query_builder');
        $results = $queryBuilder->getQuery()->getResult();

        $data = $this->serializer->serialize($results, 'json', ['groups' => 'worktime:read']);
        $decodedData = json_decode($data, true, 512, JSON_THROW_ON_ERROR);

        return new JsonResponse($decodedData, Response::HTTP_OK);
    }

    /**
     * @OA\Get(
     *     summary="Get WorkTime details",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the task",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/WorkTime")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="WorkTime not found"
     *     )
     * )
     * @OA\Tag(name="WorkTime")
     *
     * Retrieves the details of a specific task.
     *
     * @param WorkTime $workTime The WorkTime entity to retrieve details for.
     * @return JsonResponse
     */
    public function show(WorkTime $workTime): Response
    {
        return $this->json($workTime);
    }

    /**
     * @OA\Put(
     *     summary="Update a WorkTime",
     *     tags={"WorkTime"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the task to update",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(ref="#/components/schemas/WorkTime")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/WorkTime")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid data or database error",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="WorkTime not found"
     *     )
     * )
     *
     * Updates a WorkTime based on the provided data.
     *
     * @param Request $request The HTTP request object containing the updated task data.
     * @param WorkTime $workTime The WorkTime entity to be updated.
     * @return JsonResponse A JSON response containing the updated task data or an error message.
     */
    public function update(Request $request, WorkTime $workTime): Response
    {
        try {
            $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);

            $updatedWorkTime = $this->workTimeService->updateWorkTime($workTime, $data);
            $serializedWorkTime = $this->serializer->serialize($updatedWorkTime, 'json');

            return new JsonResponse($serializedWorkTime, Response::HTTP_OK);
        } catch (\Throwable $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Route("/{id}", name="app_work_time_delete", methods={"DELETE"})
     *
     * @OA\Delete(
     *     summary="Delete a WorkTime",
     *     tags={"WorkTime"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the WorkTime to delete",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="WorkTime deleted successfully",
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Failed to delete WorkTime",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="WorkTime not found"
     *     )
     * )
     *
     * Deletes a WorkTime based on its identifier.
     *
     * @param WorkTime $workTime
     * @return JsonResponse A JSON response containing a success message or an error message.
     */
    #[Route('/{id}', name: 'app_work_time_delete', methods: ['DELETE'])]
    public function delete(WorkTime $workTime): JsonResponse
    {
        try {
            $this->workTimeService->deleteWorkTime($workTime);
            return new JsonResponse(['message' => 'WorkTime deleted successfully'], Response::HTTP_NO_CONTENT);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Failed to delete WorkTime'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
