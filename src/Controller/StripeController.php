<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Entity\Order;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Stripe\Checkout\Session;

class StripeController extends AbstractController
{
    #[Route('/commande/create-session/{reference}', name: 'app_stripe_create_session')]
    public function index(EntityManagerInterface $entityManager, Cart $cart, $reference)
    {

        Stripe::SetApiKey('sk_test_51Kw5xEHVhNhyzu5kl6LyXzvC2lWIK84nkrmvZpGwNgqNRn4bh7Zh0133RQX3ZcDpC3yDB36Ob09D1HKHLwLvtgzn00ccjJSc9L');
        header('Content-Type: application/json');
        $product_for_stripe = [];
        $YOUR_DOMAIN = 'http://127.0.0.1:8000';

        $order = $entityManager->getRepository(Order::class)->findOneByReference($reference);

        if(!$order)
        {
            return $this->redirectToRoute('app_order');
        }

        foreach ($order->getOrderDetails()->getValues() as $product) {
            $product_object= $entityManager->getRepository(Product::class)->findOneByname($product->getProduct());
            $product_for_stripe[] =
                [
                    'price_data' =>
                        [
                            'currency' => 'eur',
                            'unit_amount' => $product->getPrice(),
                            'product_data' =>
                                [
                                    'name' => $product->getProduct(),
                                    'images' => [$YOUR_DOMAIN . "/uploads/" . $product_object->getIllustration()],
                                ],
                        ],
                    'quantity' => $product->getQuantity() ,
                ];
        }

        $product_for_stripe[] =
            [
                'price_data' =>
                    [
                        'currency' => 'eur',
                        'unit_amount' => $order->getCarrierPrice(),
                        'product_data' =>
                            [
                                'name' => $order->getCarrierName(),
                                'images' => [$YOUR_DOMAIN],
                            ],
                    ],
                'quantity' => 1 ,
            ];
        Stripe::setApiKey('sk_test_51Kw5xEHVhNhyzu5kl6LyXzvC2lWIK84nkrmvZpGwNgqNRn4bh7Zh0133RQX3ZcDpC3yDB36Ob09D1HKHLwLvtgzn00ccjJSc9L');

        $checkout_session = Session::create([
            'customer_email' => $this->getUser()->getEmail(),
            'payment_method_types' =>['card'],
            'line_items' => [ $product_for_stripe],
            'mode' => 'payment',
            'success_url' => $YOUR_DOMAIN . '/commande/merci/{CHECKOUT_SESSION_ID}',
            'cancel_url' => $YOUR_DOMAIN . '/commande/erreur/{CHECKOUT_SESSION_ID}',
        ]);

        $order->setStripeSessionId($checkout_session->id);
        $entityManager->flush();
        return $this->redirect($checkout_session->url);
    }
}
