<?php

namespace App\Controller\Admin;

use App\Classe\Mail;
use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;

class OrderCrudController extends AbstractCrudController
{
    private $entityManager;
    private $adminUrlGenerator;

    public function __construct(EntityManagerInterface $entityManager, AdminUrlGenerator $adminUrlGenerator)
    {
        $this->entityManager = $entityManager;
        $this->adminUrlGenerator = $adminUrlGenerator;
    }



    public static function getEntityFqcn(): string
    {
        return Order::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        $updatePreparation = Action::new('updatePreparation','Préparation en cours','fas fa-box-open')->linkToCrudAction('updatePreparation');
        $updateDelivery = Action::new('updateDelivery','Livraison en cours','fas fa-truck')->linkToCrudAction('updateDelivery');

        return $actions
            ->add('index','detail')
            ->add('detail',$updatePreparation)
            ->add('detail',$updateDelivery);
    }

    public function updatePreparation(AdminContext $context)
        {
            $order = $context->getEntity()->getInstance();
            if($order->getState()!=2)
            {
                $order->setState(2);
                $this->entityManager->flush();
                $this->addFlash('notice',"<span style='color:green;'<strong>La commande ".$order->getReference()." est bien <u>en cours de préparation</u>.</strong> </span>");
                $mail = new Mail();
                $mail->send($order->getUser()->getEmail(),$order->getUser()->getFirstname(),"Commande en préparation", "votre commande en cours de préparation" );

            }
             $url = $this->adminUrlGenerator
                ->setController(OrderCrudController::class)
                ->setAction('index')
                ->generateUrl();

            return $this->redirect($url);
        }

    public function updateDelivery(AdminContext $context)
    {
        $order = $context->getEntity()->getInstance();
        if($order->getState() !=3)
        {
            $order->setState(3);
            $this->entityManager->flush();
            $this->addFlash('notice',"<span style='color:orange;'<strong>La commande ".$order->getReference()." est bien <u>en cours de livraison</u>.</strong> </span>");
            $mail = new Mail();
            $mail->send($order->getUser()->getEmail(),$order->getUser()->getFirstname(),'commande expédiée', "votre commande a été expédiée" );
        }
        $url = $this->adminUrlGenerator
            ->setController(OrderCrudController::class)
            ->setAction('index')
            ->generateUrl();

        return $this->redirect($url);
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud->setDefaultSort(['id'=>'DESC']);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            DateTimeField::new('createdAt', 'Passée le'),
            TextField::new('user.fullname','client'),
            TextareaField::new('delivery','adresse de livraison')->renderAsHtml()->hideOnIndex(),
            MoneyField::new('total','Total produit')->setCurrency('EUR'),
            TextField::new('carrierName','Transporteur'),
            MoneyField::new('carrierPrice','Frais de port')->setCurrency('EUR'),
            ChoiceField::new('state','état de la commande')->setChoices([
                'En attente de paiement'=>'0',
                'Paiement validé' =>'1',
                'Préparation en cours' =>2,
                'Expédié'=>3
                ]),
            ArrayField::new('orderDetails', 'Produits achetés')->hideOnIndex()


        ];
    }

}
