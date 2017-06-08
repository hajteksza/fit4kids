<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\User;
use AppBundle\Entity\Rating;
use Symfony\Component\HttpFoundation\RedirectResponse;

class RatingController extends Controller
{
    /**
     * @Route("/addRating/{movieId}/{value}")
     */
//    public function addRatingAction(Request $req, $movieId, $value)
//    {
//        if ($req ->isMethod('POST')){
//            $request->request->get('value');
//            $user = $this->getUser();
//            $movie = $em->getRepository('AppBundle:Movie')->find($id);
//            $rating = new Rating;
//            $rating->setRating($value);
//            $rating->setMovie($movie);
//            $rating->setUser($user);
//            $user->addRating($rating);
//            $movie->addRating($rating);
//            $em = $this->getDoctrine()->getManager();
//            $em->persist($rating);
//            $em->flush();
//            return new RedirectResponse($this->generateUrl('course_movies', array('id'=> $movie->getCourse()->getId())));
//        }
//    }

}