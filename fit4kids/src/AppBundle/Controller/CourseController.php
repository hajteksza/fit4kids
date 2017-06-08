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

        $courses = $em->getRepository('AppBundle:Course')->findAll();

        return $this->render('course/index.html.twig', array(
            'courses' => $courses,
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
        $user = $this->getUser();
        $basket = $user->getBasket();
        $courseRepo = $this->getDoctrine()->getRepository('AppBundle:Course');
        $course = $courseRepo->find($id);
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
        } catch (\Exception $e) {
            return $this->render('/basket/errorBasket.html.twig');
        }
    }
    
}
