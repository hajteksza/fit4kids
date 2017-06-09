<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\User;
use AppBundle\Entity\Rating;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class RatingController extends Controller
{
    /**
     * @Route("/addRating", name ="addRating")
     * @Method({"GET", "POST"})
     */
    public function addRatingAction(Request $req)
    {
        if ($req ->isMethod('POST')){
            $movieId = $req->request->get('movieId');
            $value = $req->request->get('value');
            $user = $this->getUser();
            $movie = $this->getDoctrine()->getRepository('AppBundle:Movie')->find($movieId);
            
            $repo = $this->getDoctrine()->getRepository('AppBundle:Rating');
            if(!$repo->checkIfUserRatedMovie($user, $movie)){
                $rating = new Rating;
                $rating->setRating($value);
                $rating->setMovie($movie);
                $rating->setUser($user);
                $user->addRating($rating);
                $movie->addRating($rating);
                $em = $this->getDoctrine()->getManager();
                $em->persist($rating);
                $em->flush();
                $this->addFlashbag($req, 'success', 'rating dodany!');
                return new RedirectResponse($this->generateUrl('course_movies', array('id'=> $movie->getCourse()->getId())));
            }        
            else{
                $this->addFlashbag($req, 'danger', 'już oceniłeś ten film!');
                return new RedirectResponse($this->generateUrl('course_movies', array('id'=> $movie->getCourse()->getId())));
            }
        }

    }
    
    public function addFlashbag($req, $type, $message){
        $req->getSession()
        ->getFlashBag()
        ->add($type, $message);
    }
}