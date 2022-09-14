<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Classe\Mail;
use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderSuccessController extends AbstractController
{
    private $entityManager;
    public function __Construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager=$entityManager;
    }

    #[Route('/commande/merci/{stripeSessionId}', name: 'app_order_success')]
    public function index(Cart $cart, $stripeSessionId): Response
    {
        $order = $this->entityManager->getRepository(Order::class)->findOneByStripeSessionId($stripeSessionId);
        if(!$order || $order->getUser() != $this->getUser())
        {
            return $this->redirectToRoute('app_home');
        }

        if ($order->getState()==0)
        {
            //vider la session cart
            $cart->remove();
            //Modifier le statut de notre commande en mettant 1
            $order->setState(1);
            $this->entityManager->flush();

            //envoyer un email pour confirmer la commande
            $mail= new Mail();
            $content = "Bonjour ".$order->getUser()->getFirstname()."<br>Merci pour votre commande. <br><br>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Corporis, hic quidem! Architecto deleniti dolorem doloremque hic laboriosam laborum minima, molestias nisi omnis provident! Dignissimos doloremque iure laborum molestias nobis qui.";
            $mail->send($order->getUser()->getEmail(),$order->getUser()->getFirstname(),'Votre commande BestStore est bien validé', $content);
        }

        return $this->render('order_success/index.html.twig', [
        'order' => $order
        ]);
    }
}
