<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Course;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Course controller.
 *
 * @Route("course")
 */
class CourseController extends Controller
{
    /**
     * Lists all course entities.
     *
     * @Route("/", name="course_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $allCourses = $em->getRepository('AppBundle:Course')->findAll();
        $userCourses = [];
        foreach ($user->getCourses() as $userCourse){
            $userCourses[] = $userCourse;
        } 
        foreach ($allCourses as $course){
            if (in_array($course, $userCourses)){
                $course->addedByLoggedUser = 'true';
            }
            else{
                $course->addedByLoggedUser = 'false';
            }
        }

        return $this->render('course/index.html.twig', array(
            'courses' => $allCourses,
        ));
    }
    
    /**
     * @Route("/myCourses", name="my_courses")
     * @Method({"GET", "POST"})
     */
    public function myCoursesAction(Request $request)
    {
        $user = $this->getUser();
        $courses = $user->getCourses();
        return $this->render('course/my_courses.html.twig', array(
            'courses' => $courses
        ));
    }
    
        
    /**
    * @Route("/like", name="course_like")
    * 
    */
    public function like(Request $req){
        if($req->isMethod('GET') && !empty($req->query->get('id'))){
            $id = $req->query->get('id');
            $course = $courseRepo = $this->getDoctrine()->getRepository('AppBundle:Course')->find($id);
            $user = $this->getUser();
            $likedBy=[];
            foreach ($course->likedBy as $liker){
                $likedBy[] = $liker;
            }
            if (!in_array($this->getUser(), $likedBy)){
                $course -> addLikedBy($this->getUser());
                $course -> setLikes ($course->likes + 1);
                $user ->addLike($course);
                $this->getDoctrine()->getManager()->flush(); 
                return new \Symfony\Component\HttpFoundation\JsonResponse(["likes"=>$course->likes]); 
            }
            else{
                $course -> removeLikedBy($this->getUser());
                $course -> setLikes ($course->likes - 1);
                $user ->removeLike($course);
                $this->getDoctrine()->getManager()->flush(); 
                return new \Symfony\Component\HttpFoundation\JsonResponse(["likes"=>$course->likes]); 
            }
        }    
    }

    /**
     * Finds and displays a course entity.
     *
     * @Route("/{id}", name="course_show")
     * @Method("GET")
     */
    public function showAction(Course $course)
    {
        $deleteForm = $this->createDeleteForm($course);

        return $this->render('course/show.html.twig', array(
            'course' => $course,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * @Route("/pay/{id}", name="course_pay")
    */
    public function payAction(Request $req, $id)
    {
        try {
            $user = $this->getUser();
            $basket = $user->getBasket();
            $course = $this->getDoctrine()->getRepository('AppBundle:Course')->find($id);
            $pointsAfterPay = intval($user->getPoints()) - intval($course->getPrice());
            if ($pointsAfterPay >= 0) {
                $basket = $user->getBasket();
                $basket->removeCourse($course);
                $course->removeBasket($basket);
                $course->addUser($user);
                $em = $this->getDoctrine()->getManager();
                $em->persist($basket);
                $em->persist($course);
                $em->flush();
                $user->addCourse($course);
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

    /**
     * @Route("/addToBasket/{id}")
     */

    public function addToBasketAction($id)
    {
        try {
            $em = $this->getDoctrine()->getManager();
            $repositoryCourse = $em->getRepository('AppBundle:Course');
            $course = $repositoryCourse->findOneById($id);
            $basket = $this->findLoggedUserBasket();
            $course->addBasket($basket);
            $basket->addCourse($course);
            $em->persist($course);
            $em->persist($basket);
            $em->flush();
            return $this->render('/basket/addedToBasket.html.twig');
        } catch ( \Exception $e) {
            return $this->render('/basket/errorBasket.html.twig');
        }
    }

    public function getTotalPrice($basket)
    {
        $totalPrice = 0;
        if (empty($basket->getCourses)) {
            return null;
        }
        foreach ($basket->getCourses() as $course) {
            $totalPrice += intval($course->getPrice());
        }
        return $totalPrice;
    }
}
