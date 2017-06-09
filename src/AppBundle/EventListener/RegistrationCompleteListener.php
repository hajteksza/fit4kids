<?php
namespace AppBundle\EventListener;

use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FormEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Doctrine\ORM\EntityManagerInterface;
use AppBundle\Entity\Basket;
use FOS\UserBundle\Event\FilterUserResponseEvent;

/**
 * Listener responsible to change the redirection at the end of the password resetting
 */
class RegistrationCompleteListener implements EventSubscriberInterface
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public static function getSubscribedEvents()
    {
        return array(
            FOSUserEvents::REGISTRATION_COMPLETED => 'onRegistrationComplete',
        );
    }

    public function onRegistrationComplete(FilterUserResponseEvent $event)
    {
        $user = $event->getUser();
        //hack for double event call
        $existingBasket = $this->em->getRepository('AppBundle:Basket')->findOneByUser($user->getId());
        if($existingBasket==null){
            $basket = new Basket;
            $basket->setUser($user);
            $this->em->persist($basket);
            $this->em->flush();
        }
    }
}