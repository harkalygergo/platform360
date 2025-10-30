<?php

namespace App\Controller\Platform;

use App\Entity\Platform\Task\Task;
use App\Entity\Platform\User;
use App\Entity\Platform\Instance;
use App\Form\Platform\TaskType;
use App\Repository\Platform\Task\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/v2/erp/task1', name: 'erp_task_')]
#[IsGranted(User::ROLE_USER)]
class TaskController2 extends AbstractController
{
    private EntityManagerInterface $em;
    private TaskRepository $repo;

    public function __construct(EntityManagerInterface $em, TaskRepository $repo)
    {
        $this->em = $em;
        $this->repo = $repo;
    }

    #[Route('/', name: 'list', methods: ['GET'])]
    public function list(): Response
    {
        $tasks = $this->repo->findAll();

        //$data = array_map([$this, 'serializeTask'], $tasks);
        $data = array_map(function($b) {
            return [
                'id' => $b->getId(),
                'status' => $b->getStatus(),
                'title' => $b->getTitle(),
                'description' => $b->getDescription(),
                'createdAt' => $b->getCreatedAt() ? $b->getCreatedAt()->format(\DateTime::ATOM) : null,
                'updatedAt' => $b->getUpdatedAt() ? $b->getUpdatedAt()->format(\DateTime::ATOM) : null,
                'instanceId' => method_exists($b, 'getInstance') && $b->getInstance() ? $b->getInstance()->getId() : null,
                'createdById' => method_exists($b, 'getCreatedBy') && $b->getCreatedBy() ? $b->getCreatedBy()->getId() : null,
            ];
        }, $tasks);

        return $this->render('platform/dashboard/main.html.twig', [
            'title' => 'feladatok',
            'items'     => $data,
        ]);
    }

    #[Route('/new', name: 'admin_v1_block_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $task = new Task();
        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $task->setInstance($this->currentInstance);
            $task->setCreatedBy($this->getUser());
            $this->em->persist($task);
            $this->em->flush();

            return $this->redirectToRoute('erp_task_list', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('platform/backend/v1/form.html.twig', [
            'sidebarMenu' => $this->getSidebarController()->getSidebarMenu(),
            'title' => 'Új feladat létrehozása',
            'form' => $form->createView(),
        ]);
    }


    #[Route('/{id}', name: 'get', methods: ['GET'])]
    public function get(int $id): JsonResponse
    {
        $task = $this->repo->find($id);
        if (!$task) {
            return new JsonResponse(['error' => 'Task not found'], Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse($this->serializeTask($task));
    }

    #[Route('/create/', name: 'add', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $payload = json_decode($request->getContent(), true);
        if (!is_array($payload)) {
            return new JsonResponse(['error' => 'Invalid JSON'], Response::HTTP_BAD_REQUEST);
        }

        // required: instance_id and created_by_id
        $instanceId = $payload['instance_id'] ?? null;
        $createdById = $payload['created_by_id'] ?? null;
        if (!$instanceId || !$createdById) {
            return new JsonResponse(['error' => 'instance_id and created_by_id are required'], Response::HTTP_BAD_REQUEST);
        }

        $instance = $this->em->getRepository(Instance::class)->find($instanceId);
        $createdBy = $this->em->getRepository(User::class)->find($createdById);

        if (!$instance || !$createdBy) {
            return new JsonResponse(['error' => 'Instance or createdBy user not found'], Response::HTTP_BAD_REQUEST);
        }

        $task = new Task();
        $task->setInstance($instance);
        $task->setCreatedBy($createdBy);
        $task->setTitle($payload['title'] ?? null);
        $task->setDescription($payload['description'] ?? null);
        $task->setStatus(isset($payload['status']) ? (bool)$payload['status'] : false);
        if (!empty($payload['assignee_id'])) {
            $assignee = $this->em->getRepository(User::class)->find($payload['assignee_id']);
            if ($assignee) {
                $task->setAssignee($assignee);
            }
        }
        if (!empty($payload['deadline'])) {
            try {
                $task->setDeadline(new \DateTimeImmutable($payload['deadline']));
            } catch (\Exception $e) {
                // ignore invalid date format
            }
        }
        if (isset($payload['priority'])) {
            $task->setPriority((int)$payload['priority']);
        }

        $this->em->persist($task);
        $this->em->flush();

        return new JsonResponse($this->serializeTask($task), Response::HTTP_CREATED);
    }

    #[Route('/edit/{id}/', name: 'update', methods: ['GET', 'PUT', 'PATCH'])]
    public function update(Request $request, Task $task): Response
    {
        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($task);
            $this->em->flush();

            $this->addFlash('success', 'Block updated');

            return $this->redirectToRoute('erp_task_list');
        }

        return $this->render('platform/block/edit.html.twig', [
            'form' => $form->createView(),
            'block' => $task,
        ]);



        /*
        $task = $this->repo->find($id);
        if (!$task) {
            return new JsonResponse(['error' => 'Task not found'], Response::HTTP_NOT_FOUND);
        }

        $payload = json_decode($request->getContent(), true);
        if (!is_array($payload)) {
            return new JsonResponse(['error' => 'Invalid JSON'], Response::HTTP_BAD_REQUEST);
        }

        if (array_key_exists('title', $payload)) {
            $task->setTitle($payload['title']);
        }
        if (array_key_exists('description', $payload)) {
            $task->setDescription($payload['description']);
        }
        if (array_key_exists('status', $payload)) {
            $task->setStatus((bool)$payload['status']);
        }
        if (array_key_exists('instance_id', $payload) && $payload['instance_id']) {
            $instance = $this->em->getRepository(Instance::class)->find($payload['instance_id']);
            if ($instance) {
                $task->setInstance($instance);
            }
        }
        if (array_key_exists('assignee_id', $payload)) {
            $assignee = $this->em->getRepository(User::class)->find($payload['assignee_id']);
            $task->setAssignee($assignee);
        }
        if (array_key_exists('updated_by_id', $payload) && $payload['updated_by_id']) {
            $updatedBy = $this->em->getRepository(User::class)->find($payload['updated_by_id']);
            if ($updatedBy) {
                $task->setUpdatedBy($updatedBy);
            }
        }
        if (array_key_exists('deadline', $payload)) {
            if ($payload['deadline']) {
                try {
                    $task->setDeadline(new \DateTimeImmutable($payload['deadline']));
                } catch (\Exception $e) {
                    // ignore invalid date
                }
            } else {
                $task->setDeadline(null);
            }
        }
        if (array_key_exists('priority', $payload)) {
            $task->setPriority($payload['priority'] !== null ? (int)$payload['priority'] : null);
        }

        $this->em->flush();

        return new JsonResponse($this->serializeTask($task));
        */
    }

    #[Route('/delete/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $task = $this->repo->find($id);
        if (!$task) {
            return new JsonResponse(['error' => 'Task not found'], Response::HTTP_NOT_FOUND);
        }

        $this->em->remove($task);
        $this->em->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    private function serializeTask(Task $t): array
    {
        return [
            'id' => $t->getId(),
            'title' => $t->getTitle(),
            'description' => $t->getDescription(),
            'status' => $t->getStatus(),
            'instance_id' => $t->getInstance() ? $t->getInstance()->getId() : null,
            'created_by_id' => $t->getCreatedBy() ? $t->getCreatedBy()->getId() : null,
            'updated_by_id' => $t->getUpdatedBy() ? $t->getUpdatedBy()->getId() : null,
            'assignee_id' => $t->getAssignee() ? $t->getAssignee()->getId() : null,
            'deadline' => $t->getDeadline() ? $t->getDeadline()->format(\DateTime::ATOM) : null,
            'priority' => $t->getPriority(),
            'created_at' => $t->getCreatedAt() ? $t->getCreatedAt()->format(\DateTime::ATOM) : null,
            'updated_at' => $t->getUpdatedAt() ? $t->getUpdatedAt()->format(\DateTime::ATOM) : null,
        ];
    }
}

