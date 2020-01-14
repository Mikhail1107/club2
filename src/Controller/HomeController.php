<?php

namespace App\Controller;

use App\Entity\Message;
use App\Form\MessageType;
use App\Repository\MessageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     *
     */
    public function home(MessageRepository $messageRepository)
    {
        $messages = $messageRepository->findAll();
        return $this->render('homepage.html.twig', [
            'messages' => $messages
        ]);
    }

    /**
     * @Route("/admin", name="admin")
     * Méthode pour rediriger sur la page de connexion via le chemin /admin si l'admin n'est pas connecté
     */
    public function admin()
    {
        if ($this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('home');
        } else {
            return $this->redirectToRoute('fos_user_security_login');
        }
    }

    /**
     * @Route("/contact", name="contact")
     */
    public function contact(EntityManagerInterface $entityManager, Request $request, ValidatorInterface $validator)
    {
        $message = new Message();
        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            // On envoie le message en base de données grâce aux méthodes persist(objet) + flush
            // persist + flush est l'équivalent de commit + push de Git.
            $entityManager->persist($message);
            $entityManager->flush();
            $this->addFlash('Success', 'Salut '. $message->getFirstname() . '! Votre message a bien été envoyé.');
        }
        //si le formulaire est soumis (submit)
            //utilisation de la fonctoin mail()
        return $this->render('contact.html.twig', [
            'messageForm' => $form->createView(),
            'message' => $message
        ]);
    }


    /**
     * @Route("/admin/messages", name="messages")
     */
     public function messages(MessageRepository $messageRepository)
     {
         $messages = $messageRepository->findAll();
         return $this->render('messages.html.twig', [
             'messages' => $messages
         ]);
     }


}
