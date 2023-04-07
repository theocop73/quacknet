<?php

namespace App\Controller;

use App\Entity\Quack;
use App\Entity\Comment;
use App\Form\QuackType;
use App\Form\CommentType;
use App\Repository\QuackRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/quack')]
class QuackController extends AbstractController
{
    #[Route('/', name: 'app_quack_index', methods: ['GET', 'POST'])]
    public function index(QuackRepository $quackRepository, EntityManagerInterface $entityManager, Request $request): Response
    {
        $quacks = $entityManager->getRepository(Quack::class)->findAll();
        $user = $this->getUser();
    

        $comment = new Comment();
        $commentForm = $this->createForm(CommentType::class, $comment);
    
        // Traitement de la soumission du formulaire de commentaire
        $commentForm->handleRequest($request);
        if ($commentForm->isSubmitted() && $commentForm->isValid()) {
            $comment->setAuthor($this->getUser()); 
            $quackId = $request->request->get('quack_id');
            $quack = $entityManager->getRepository(Quack::class)->find($quackId);
            
           
            $comment->setQuack($quack);

            $entityManager->persist($comment);
            $entityManager->flush();
            $this->addFlash('success', 'Comment added successfully!');
            return $this->redirectToRoute('app_quack_index');
        }

        foreach($quacks as $quack){
            $quack->form  = $commentForm->createView();
        }
    
    
        return $this->render('quack/index.html.twig', [
            'quacks' => $quacks,
        ]);
    }

    #[Route('/comment/create', name: 'comment_create', methods: ['GET', 'POST'])]
    public function createComment(Request $request, EntityManagerInterface $entityManager): Response
    {
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
           
    
            $comment = $form->getData();
            $quackId = $form->get('quack_id')->getData(); // Récupération de l'ID du Quack
    
            $quack = $entityManager->getRepository(Quack::class)->find($quackId); // Récupération du Quack depuis l'ID
    
            if (!$quack) {
                throw $this->createNotFoundException('Aucun Quack trouvé pour cet ID : ' . $quackId);
            }
    
            $comment->setQuack($quack); // Association du Quack au Comment
    
            $entityManager->persist($comment);
            $entityManager->flush();
    
            return $this->redirectToRoute('quack_show', ['id' => $quackId]);
        }
    }

    #[Route('/new', name: 'app_quack_new', methods: ['GET', 'POST'])]
    public function new(Request $request, QuackRepository $quackRepository): Response
    {
        $quack = new Quack();
        $form = $this->createForm(QuackType::class, $quack);
        $form->handleRequest($request);
        

        if ($form->isSubmitted() && $form->isValid()) {
            $quackRepository->save($quack, true);

            return $this->redirectToRoute('app_quack_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('quack/new.html.twig', [
            'quack' => $quack,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_quack_show', methods: ['GET', 'POST'])]
    public function show(Quack $quack, Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setQuack($quack);
            $comment->setAuthor($user);

            $entityManager->persist($comment);
            $entityManager->flush();
    
            return $this->redirectToRoute('app_quack_show', ['id' => $quack->getId()]);
        }

        return $this->render('quack/show.html.twig', [
            'quack' => $quack,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_quack_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Quack $quack, QuackRepository $quackRepository): Response
    {

        $user = $this->getUser();
        $form = $this->createForm(QuackType::class, $quack);
        $form->handleRequest($request);
        if ($user !== $quack->getAuthor()) {
            throw $this->createAccessDeniedException('Vous n\'êtes pas autorisé à modifié ce message.');
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $quackRepository->save($quack, true);

            return $this->redirectToRoute('app_quack_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('quack/edit.html.twig', [
            'quack' => $quack,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_quack_delete', methods: ['POST'])]
    public function delete(Request $request, Quack $quack, QuackRepository $quackRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$quack->getId(), $request->request->get('_token'))) {

            if ($user !== $quack->getAuthor()) {
                throw $this->createAccessDeniedException('Vous n\'êtes pas autorisé à supprimer ce message.');
            }
            $quackRepository->remove($quack, true);
        }

        return $this->redirectToRoute('app_quack_index', [], Response::HTTP_SEE_OTHER);
    }
}
