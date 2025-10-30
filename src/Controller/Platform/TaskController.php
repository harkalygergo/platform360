<?php

namespace App\Controller\Platform;

use App\Entity\Platform\User;
use App\Entity\Platform\Task\Task;
use App\Form\Platform\BlockType;
use App\Repository\Platform\BlockRepository;
use App\Repository\Platform\InstanceRepository;
use App\Repository\Platform\Task\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/v2/erp/task', name: 'erp_task_')]
#[IsGranted(User::ROLE_USER)]
class TaskController extends AbstractController
{
    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(TaskRepository $taskRepository): Response
    {
        $tasks = $taskRepository->findAll();

        $data = array_map(function($b) {
            return [
                'id' => $b->getId(),
                'title' => $b->getTitle(),
                'description' => $b->getDescription(),
                'status' => $b->getStatus(),
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

}
