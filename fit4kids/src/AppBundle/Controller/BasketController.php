<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Basket;
use AppBundle\Entity\User;
use AppBundle\Entity\Course;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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
        $empty=0;
        if (count($courses)==0){
            $empty = 1;
        }
        
        return $this->render('basket/show.html.twig', array(
            'basket' => $basket,
            'courses' => $courses,
            'empty' => $empty
        ));
    }
    
     /**
     * @Route("/removeCourse/{id}", name="removeFromBasket")
     */
    public function removeFromBasketAction($id)
    {
        $basket = $this->findLoggedUserBasket();
        $courseRepo = $this->getDoctrine()->getRepository('AppBundle:Course');
        $course = $courseRepo->find($id);
        try {
            $basket->removeCourse($course);
            $course->removeBasket($basket);
            $em = $this->getDoctrine()->getManager();
            $em -> flush();
            return $this->render('/basket/deleteSuccess.html.twig');
        } catch (\Exception $e) {
            return $this->render('/basket/deleteFail.html.twig');
        }
    }
    
     /**
     * @Route("/payAll", name="payAllBasket")
     */
    public function payAllInBasketAction(Request $req)
    {
        try {
            $user = $this->getUser();
            $basket = $user->getBasket();
            $totalPrice = $this->getTotalPrice($basket);
            $pointsAfterPay = intval($user->getPoints()) - $totalPrice;
            if ($pointsAfterPay >= 0 && $totalPrice != null) {
                $em = $this->getDoctrine()->getManager();
                foreach ($basket->getCourses() as $course) {
                    $basket->removeCourse($course);
                    $course->removeBasket($basket);
                    $course->addUser($user);
                    $em->persist($basket);
                    $em->persist($course);
                    $user->addCourse($course);
                }
                $em->flush();
                $user->setPoints($pointsAfterPay);
                $userManager = $this->get('fos_user.user_manager');
                $userManager->updateUser($user);
                return $this->render('course/buy.html.twig');
            } else {
                return $this->render('course/buy_error.html.twig');
            }
        } catch (\Exception $e) {
            return $this->render('course/buy_database_error.html.twig');
        }
    }

    public function findLoggedUserBasket()
    {
        $user = $this->getUser();
        return $user->getBasket();
    }
    
    public function getTotalPrice($basket){
        $totalPrice=0;
        foreach ($basket->getCourses() as $course){
            $totalPrice += intval($course->getPrice());
        }
        return $totalPrice;
    }
    
}
