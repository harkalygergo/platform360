<?php

namespace App\Controller\Platform;

use App\Entity\Platform\User;
use App\Repository\Platform\BlockRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(User::ROLE_USER)]
class BlockController extends AbstractController
{
    #[Route('/admin/v2/blocks', name: 'web_block_index', methods: ['GET'])]
    public function index(BlockRepository $blockRepository): Response
    {
        $blocks = $blockRepository->findAll();

        $data = array_map(function($b) {
            return [
                'id' => $b->getId(),
                'name' => $b->getName(),
                'content' => $b->getContent(),
                'status' => $b->getStatus(),
                'createdAt' => $b->getCreatedAt() ? $b->getCreatedAt()->format(\DateTime::ATOM) : null,
                'updatedAt' => $b->getUpdatedAt() ? $b->getUpdatedAt()->format(\DateTime::ATOM) : null,
                'instanceId' => method_exists($b, 'getInstance') && $b->getInstance() ? $b->getInstance()->getId() : null,
                'createdById' => method_exists($b, 'getCreatedBy') && $b->getCreatedBy() ? $b->getCreatedBy()->getId() : null,
            ];
        }, $blocks);

        return $this->render('platform/dashboard/main.html.twig', [
            'title' => 'blokkok',
            'items'     => $data['0'],
            ]);
    }
}

