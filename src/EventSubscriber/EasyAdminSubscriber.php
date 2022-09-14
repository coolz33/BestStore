<?php
namespace App\EventSubscriber;

use App\Entity\Product;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class EasyAdminSubscriber implements EventSubscriberInterface
{


    public static function getSubscribedEvents()
    {
        return [
           // BeforeEntityUpdatedEvent::class => ['setIllustration'],
        ];
    }

    public function setIllustrationDefault(BeforeEntityUpdatedEvent $event)
    {
        $entity = $event->getEntityInstance();

        if (!($entity instanceof Product)) {
            return;
        }
        if (!$entity->getIllustration()) {
            $entity->setIllustration('default.png');
        }
    }
}