<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\User;

class PointsController extends Controller
{
    /**
     * @Route("/addPoints/{value}")
     */
    public function addPointsAction($value)
    {
        $user = $this->getUser();
        $points = $user->getPoints();
        $points += $value;
        $user->setPoints($points);
        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();
        return $this->render('AppBundle:Points:add_points.html.twig', array(
            'value' => $value,
        ));
    }

}
