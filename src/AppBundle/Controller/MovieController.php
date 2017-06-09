<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Movie;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Movie controller.
 *
 * @Route("movie")
 */
class MovieController extends Controller
{

     /**
     * @Route("/courseMovies/{id}", name="course_movies")
     */
    public function courseMoviesAction(Request $req, $id)
    {   
        $em = $this->getDoctrine()->getManager();
        $course = $em->getRepository('AppBundle:Course')->find($id);
        if($course == null){
            return $this->render('/course/course_not_found.html.twig');
        }
        $courseTitle = $course->getTitle();
        $user = $this->getUser();
        $userCourses = [];
        foreach ($user->getCourses() as $userCourse){
            $userCourses[] = $userCourse;
        }
        if (in_array ($course , $userCourses)){
            $movies=[];
            $i=0;
                foreach($course->getMovies() as $movie){
                    $movies[$i]['id'] = $movie->getId();
                    $movies[$i]['title'] = $movie->getTitle();
                    $movies[$i]['description'] = $movie->getTitle();
                    $movies[$i]['avgRating'] = $movie->getAverageRating();
                    $movies[$i]['ratingCount'] = count($movie->getRatings());
                    $movies[$i]['path'] = $movie->getPath();
                    $i++;
                }
            if(empty($movies)){
                $this->addFlashbag($req, 'danger', 'w tym kursie nie ma żadnych filmów!');
            }
            return $this->render('movie/course_movies.html.twig', array(
                'movies' => $movies,
                'courseTitle' => $courseTitle
            ));
        }
        else{
            $this->addFlashbag($req, 'danger', 'ten kurs nie jest zakupiony!');
            return new RedirectResponse($this->generateUrl('course_index', array()));
        }
    }
    
     /**
     * @Route("/getMoviePath", name="movie_path")
     */
    public function getMoviePathAction(Request $req)
    {
        if(null !== $req->query->get('id')){
            $id = $req->query->get('id');
            $movieRepo = $this->getDoctrine()->getRepository('AppBundle:Movie');
            $moviePath = $movieRepo->find($id)->getPath();
            return new \Symfony\Component\HttpFoundation\JsonResponse(["path"=>$moviePath]);
        }        
    }
    public function addFlashbag($req, $type, $message){
        $req->getSession()
        ->getFlashBag()
        ->add($type, $message);
    }

}
