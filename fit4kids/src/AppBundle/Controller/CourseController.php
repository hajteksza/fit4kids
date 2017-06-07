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
     * Creates a new course entity.
     *
     * @Route("/new", name="course_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $course = new Course();
        $form = $this->createForm('AppBundle\Form\CourseType', $course);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($course);
            $em->flush();

            return $this->redirectToRoute('course_show', array('id' => $course->getId()));
        }

        return $this->render('course/new.html.twig', array(
            'course' => $course,
            'form' => $form->createView(),
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
     * Displays a form to edit an existing course entity.
     *
     * @Route("/{id}/edit", name="course_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Course $course)
    {
        $deleteForm = $this->createDeleteForm($course);
        $editForm = $this->createForm('AppBundle\Form\CourseType', $course);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('course_edit', array('id' => $course->getId()));
        }

        return $this->render('course/edit.html.twig', array(
            'course' => $course,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }


    /**
     * Deletes a course entity.
     *
     * @Route("/{id}", name="course_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Course $course)
    {
        $form = $this->createDeleteForm($course);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($course);
            $em->flush();
        }

        return $this->redirectToRoute('course_index');
    }

    /**
     * @Route("/{id}/pay", name="course_pay")
     */
    public function payAction(Request $req, $id)
    {
        $user = $this->getUser();
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

    /**
     * Creates a form to delete a course entity.
     *
     * @param Course $course The course entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Course $course)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('course_delete', array('id' => $course->getId())))
            ->setMethod('DELETE')
            ->getForm();
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
            return $this->render('/course/addedToBasket.html.twig');
        } catch (\Exception $e) {
            return $this->render('/course/errorBasket.html.twig');
        }
    }
}
