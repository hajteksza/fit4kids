<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Movie;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Movie controller.
 *
 * @Route("movie")
 */
class MovieController extends Controller
{
    /**
     * Lists all movie entities.
     *
     * @Route("/", name="movie_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $movies = $em->getRepository('AppBundle:Movie')->findAll();

        return $this->render('movie/index.html.twig', array(
            'movies' => $movies,
        ));
    }

    /**
     * Creates a new movie entity.
     *
     * @Route("/new", name="movie_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $movie = new Movie();
        $form = $this->createForm('AppBundle\Form\MovieType', $movie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($movie);
            $em->flush();

            return $this->redirectToRoute('movie_show', array('id' => $movie->getId()));
        }

        return $this->render('movie/new.html.twig', array(
            'movie' => $movie,
            'form' => $form->createView(),
        ));
    }
    
     /**
     * @Route("/courseMovies/{id}", name="course_movies")
     */
    public function courseMoviesAction(Request $req, $id)
    {   
        $em = $this->getDoctrine()->getManager();
        $course = $em->getRepository('AppBundle:Course')->find($id);
        $courseTitle = $course->getTitle();
        $user = $this->getUser();
        $userCourses = [];
        foreach ($user->getCourses() as $userCourse){
            $userCourses[] = $userCourse;
        }
        if (in_array ($course , $userCourses)){
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
            return $this->render('movie/course_movies.html.twig', array(
                'movies' => $movies,
                'courseTitle' => $courseTitle
            ));
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
    
    /**
     * Finds and displays a movie entity.
     *
     * @Route("/{id}", name="movie_show")
     * @Method("GET")
     */
    public function showAction(Movie $movie)
    {
        $deleteForm = $this->createDeleteForm($movie);

        return $this->render('movie/show.html.twig', array(
            'movie' => $movie,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing movie entity.
     *
     * @Route("/{id}/edit", name="movie_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Movie $movie)
    {
        $deleteForm = $this->createDeleteForm($movie);
        $editForm = $this->createForm('AppBundle\Form\MovieType', $movie);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('movie_edit', array('id' => $movie->getId()));
        }

        return $this->render('movie/edit.html.twig', array(
            'movie' => $movie,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a movie entity.
     *
     * @Route("/{id}", name="movie_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Movie $movie)
    {
        $form = $this->createDeleteForm($movie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($movie);
            $em->flush();
        }

        return $this->redirectToRoute('movie_index');
    }
    

    /**
     * Creates a form to delete a movie entity.
     *
     * @param Movie $movie The movie entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Movie $movie)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('movie_delete', array('id' => $movie->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
