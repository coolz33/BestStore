<?php

namespace App\Controller;

use App\Classe\Mail;
use App\Entity\User;
use App\Form\RegisterType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegisterController extends AbstractController
{
    private $entityManager;
    public function __construct(EntityManagerInterface $entityManager){
        $this->entityManager = $entityManager;
    }

    #[Route('/inscription', name: 'app_register')]
    public function index(Request $request, UserPasswordHasherInterface $hasher): Response
    {
        $notification = null;
        $user = new User();
        $form = $this->createForm(RegisterType::class,$user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $user=$form->getData();
            $search_email = $this->entityManager->getRepository(User::class)->findOneByEmail($user->getEmail());
            if (!$search_email)
            {
                $password = $hasher->hashPassword($user,$user->getPassword());
                $user->setPassword($password);
                $this->entityManager->persist($user);
                $this->entityManager->flush();
                $mail= new Mail();
                $content = "Bonjour ".$user->getFirstname()."<br>Bienvenue sur Beststore. <br><br>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Corporis, hic quidem! Architecto deleniti dolorem doloremque hic laboriosam laborum minima, molestias nisi omnis provident! Dignissimos doloremque iure laborum molestias nobis qui.";
                $mail->send($user->getEmail(),$user->getFirstname(),'Bienvenue sur BestStore', $content);

                $notification ="Votre inscription s'est correctement déroulé. Vous pouvez dès à présent vous connecter à votre compte.";
            }
            else
            {
                $notification ="L'email que vous avez renseigné existe déjà.";
            }



        }

        return $this->render('register/index.html.twig', [
            'form' => $form->createView(),
                'notification' => $notification
            ]

        );
    }
}
