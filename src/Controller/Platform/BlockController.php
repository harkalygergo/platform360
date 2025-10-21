<?php

namespace App\Controller\Platform;

use App\Entity\Platform\User;
use App\Entity\Platform\Block;
use App\Form\Platform\BlockType;
use App\Repository\Platform\BlockRepository;
use App\Repository\Platform\InstanceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/v2/web/block')]
#[IsGranted(User::ROLE_USER)]
class BlockController extends AbstractController
{
    #[Route('/', name: 'web_block_index', methods: ['GET'])]
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
            'items'     => $data,
            ]);
    }

    #[Route('/add/', name: 'web_block_add', methods: ['GET', 'POST'])]
    public function add(Request $request, EntityManagerInterface $em, InstanceRepository $instanceRepository): Response
    {
        $block = new Block();

        $form = $this->createForm(BlockType::class, $block);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Ensure relations required by Block are set: instance and createdBy
            $user = $this->getUser();
            if ($user instanceof User) {
                $block->setCreatedBy($user);
                // try to get user's default instance, fallback to first instance in DB
                $instance = $user->getDefaultInstance();
                if (!$instance) {
                    $instances = $instanceRepository->findAll();
                    $instance = $instances[0] ?? null;
                }
                if ($instance) {
                    $block->setInstance($instance);
                }
            }

            $em->persist($block);
            $em->flush();

            $this->addFlash('success', 'Block created');

            return $this->redirectToRoute('web_block_index');
        }

        return $this->render('platform/block/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/edit/{id}', name: 'web_block_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, EntityManagerInterface $em, Block $block): Response
    {
        $form = $this->createForm(BlockType::class, $block);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($block);
            $em->flush();

            $this->addFlash('success', 'Block updated');

            return $this->redirectToRoute('web_block_index');
        }

        return $this->render('platform/block/edit.html.twig', [
            'form' => $form->createView(),
            'block' => $block,
        ]);
    }

    #[Route('/delete/{id}', name: 'web_block_delete', methods: ['POST'])]
    public function delete(Request $request, EntityManagerInterface $em, Block $block): Response
    {
        if ($this->isCsrfTokenValid('delete' . $block->getId(), $request->request->get('_token'))) {
            $em->remove($block);
            $em->flush();
            $this->addFlash('success', 'Block deleted');
        }

        return $this->redirectToRoute('web_block_index');
    }
}
