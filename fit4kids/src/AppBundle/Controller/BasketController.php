<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Basket;
use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Basket controller.
 *
 * @Route("basket")
 */
class BasketController extends Controller
{
    /**
     * Finds and displays a basket entity.
     *
     * @Route("/", name="basket_show")
     * @Method("GET")
     */
    public function showAction()
    {
        $basket = $this->findLoggedUserBasket();
        $courses = $basket->getCourses();
        
        return $this->render('basket/show.html.twig', array(
            'basket' => $basket,
            'courses' => $courses
        ));
    }
    
    public function findLoggedUserBasket(){
        $user = $this->getUser();
        return $user->getBasket();
    }

}
