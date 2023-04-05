<?php

namespace App\EventSubscriber;

use App\Entity\Quack;
use Doctrine\ORM\Events;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Doctrine\Common\EventSubscriber;

class QuackSubscriber implements EventSubscriber
{


    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }


   

    public function getSubscribedEvents()
    {
        return [
            Events::prePersist,
        ];
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        if (!$entity instanceof Quack) {
            return;
        }

        $user = $this->security->getUser();
        $entity->setAuthor($user);
    }
}
