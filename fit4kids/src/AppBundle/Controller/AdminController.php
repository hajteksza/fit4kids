<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Carousel;
use AppBundle\Entity\Course;
use AppBundle\Entity\Movie;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Config\Definition\Exception\Exception;

class AdminController extends Controller {

    /**
     * @Route("/superadmin/")
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     */
    public function showSuperAdminPanelAction() {
        return $this->render('AppBundle:Admin:show_super_admin_panel.html.twig');
    }

    /**
     * @Route("/superadmin/admin/")
     * @Method("GET")
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     */
    public function showSuperAdminAdminAction() {
        $repo = $this->getDoctrine()->getRepository('AppBundle:User');
        $qb = $this->getDoctrine()->getManager()->createQueryBuilder();
        $admins = $qb->select('u')
                ->from('AppBundle:User', 'u')
                ->where('u.roles LIKE :roles')
                ->setParameter('roles', '%ADMIN%')
                ->getQuery()
                ->getResult();
        return $this->render('AppBundle:Admin:show_super_admin_admin.html.twig', array(
                    'admins' => $admins
        ));
    }

    /**
     * @Route("/superadmin/user/")
     * @Method("GET")
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     */
    public function showSuperAdminUserAction() {
        $repo = $this->getDoctrine()->getRepository('AppBundle:User');
        $qb = $this->getDoctrine()->getManager()->createQueryBuilder();
        $users = $qb->select('u')
                ->from('AppBundle:User', 'u')
                ->where('u.roles NOT LIKE :roles')
                ->setParameter('roles', '%ADMIN%')
                ->getQuery()
                ->getResult();
        return $this->render('AppBundle:Admin:show_super_admin_user.html.twig', array(
                    'users' => $users
        ));
    }

    /**
     * @Route("/superadmin/promote/{id}/")
     * @Method("GET")
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     */
    public function PromoteUserAction($id) {
        $repo = $this->getDoctrine()->getRepository('AppBundle:User');
        $user = $repo->find($id);
        $user->setRoles(array('ROLE_ADMIN'));
        $userManager = $this->get('fos_user.user_manager');
        $userManager->updateUser($user);
        return $this->render('AppBundle:Admin:show_super_admin_panel.html.twig', array(
        ));
    }

    /**
     * @Route("/superadmin/delete/{id}/")
     * @Method("GET")
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     */
    public function deleteAdminAction($id) {

        $repo = $this->getDoctrine()->getRepository('AppBundle:User');
        $user = $repo->find($id);
        $em = $this->getDoctrine()->getManager();
        $em->remove($user);
        $em->flush();

        return new Response('Usunięto');
    }

    /**
     * @Route("/admin/")
     * @Security("has_role('ROLE_ADMIN')")
     */

    public function showAdminPanelAction()
    {
        try {
            return $this->render('AppBundle:Admin:show_admin_panel.html.twig', array(// ...
            ));
        } catch (\Exception $e) {
            return $this->render('AppBundle:Admin:access_denied.html.twig');
        }
    }

    /**
     * @Route("/admin/carousel/")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function showAdminCarouselAction() {

        $repo = $this->getDoctrine()->getRepository('AppBundle:Carousel');
        $carousels = $repo->findAll();

        return $this->render('AppBundle:Admin:show_admin_carousel.html.twig', array(
                    "carousels" => $carousels,
        ));
    }

    /**
     * @Route("/admin/course/")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function showAdminCourseAction() {
        $repo = $this->getDoctrine()->getRepository('AppBundle:Course');
        $courses = $repo->findAll();
        return $this->render('AppBundle:Admin:show_admin_course.html.twig', array(
                    'courses' => $courses
        ));
    }

    /**
     * @Route("/admin/movie/")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function showAdminMovieAction() {
        $repo = $this->getDoctrine()->getRepository('AppBundle:Movie');
        $movies = $repo->findAll();
        return $this->render('AppBundle:Admin:show_admin_movie.html.twig', array(
                    'movies' => $movies
        ));
    }

    /**
     * @Route("/admin/user/")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function showAdminUserAction() {
        $repo = $this->getDoctrine()->getRepository('AppBundle:User');
                $qb = $this->getDoctrine()->getManager()->createQueryBuilder();
        $users = $qb->select('u')
                ->from('AppBundle:User', 'u')
                ->where('u.roles NOT LIKE :roles')
                ->setParameter('roles', '%ADMIN%')
                ->getQuery()
                ->getResult();
        return $this->render('AppBundle:Admin:show_admin_user.html.twig', array(
                    'users' => $users
        ));
    }

    /**

     * @Route("/admin/basket/")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function showAdminBasketAction() {
        $repoBasket = $this->getDoctrine()->getRepository('AppBundle:Basket');
        $repoUser = $this->getDoctrine()->getRepository('AppBundle:Basket');
        $users = $repoUser->findAll();
        $baskets = $repoBasket->findAll();
        return $this->render('AppBundle:Admin:show_admin_basket.html.twig', array(
                    'baskets' => $baskets,
                    'users' => $users
        ));
    }

    /**

     * @Route("/admin/carousel/add/")
     * @Method("GET")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function showAddCarouselFormAction() {
        $carousel = new Carousel();
        $form = $this->createFormBuilder($carousel)
                ->add('name', TextType::class)
                ->add('course', EntityType::class, array(
                    'class' => 'AppBundle:Course',
                    'choice_label' => 'title'))
                ->add('picture', FileType::class)
                ->add('Dodaj', SubmitType::class)
                ->getForm();

        return $this->render('AppBundle:Admin:basic_form.html.twig', array(
                    'form' => $form->createView()
        ));
    }

    /**
     * @Route("/admin/carousel/add/")
     * @Method("POST")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function addCarouselAction(Request $request) {
        try {
            $carousel = new Carousel();

            $form = $this->createFormBuilder($carousel)
                    ->add('name', TextType::class)
                    ->add('course', EntityType::class, array(
                        'class' => 'AppBundle:Course',
                        'choice_label' => 'title'))
                    ->add('picture', FileType::class)
                    ->add('Dodaj', SubmitType::class)
                    ->getForm();

            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $file = $carousel->getPicture();
                $newPath = $file->move("Assets/images/", $file->getClientOriginalName());
                $carousel->setPicture($newPath);
                $em = $this->getDoctrine()->getManager();

                $repoCarousel = $em->getRepository("AppBundle:Carousel");
                $repoCourse = $em->getRepository('AppBundle:Course');

                $oldCarousel = $repoCarousel->findOneBy(['course' => $carousel->getCourse()]) ?: $carousel;
                $course = $repoCourse->find($oldCarousel->getCourse()->getId());
                $course->setCarousel($oldCarousel);

                $oldCarousel->setName($carousel->getName());
                $oldCarousel->setPicture($carousel->getPicture());
                $oldCarousel->setCourse($carousel->getCourse());

                $em->persist($oldCarousel);
                $em->persist($course);
                $em->flush();
            }
            return $this->render('AppBundle:Admin:added.html.twig');
        } catch (\Exception $e) {
            return $this->render('AppBundle:Admin:error.html.twig');
        }
    }

    /**
     * @Route("/admin/carousel/edit/{id}/")
     * @Method("GET")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function showEditCarouselFormAction($id) {

        $repo = $this->getDoctrine()->getRepository('AppBundle:Carousel');
        $carousel = $repo->find($id);

        $form = $this->createFormBuilder($carousel)
                ->add('name', TextType::class)
                ->add('course', EntityType::class, array(
                    'class' => 'AppBundle:Course',
                    'choice_label' => 'title'))
                ->add('picture', FileType::class, array('data_class' => null))
                ->add('Edytuj', SubmitType::class)
                ->getForm();

        return $this->render('AppBundle:Admin:basic_form.html.twig', array(
                    'form' => $form->createView()
        ));
    }

    /**
     * @Route("/admin/carousel/edit/{id}/")
     * @Method("POST")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function editCarouselAction(Request $request, $id) {
        try {
            $repo = $this->getDoctrine()->getRepository('AppBundle:Carousel');
            $carousel = $repo->find($id);

            $form = $this->createFormBuilder($carousel)
                    ->add('name', TextType::class)
                    ->add('course', EntityType::class, array(
                        'class' => 'AppBundle:Course',
                        'choice_label' => 'title'))
                    ->add('picture', FileType::class, array('data_class' => null))
                    ->add('Dodaj', SubmitType::class)
                    ->getForm();

            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $file = $carousel->getPicture();
                $newPath = $file->move("Assets/images/", $file->getClientOriginalName());
                $carousel->setPicture($newPath);
                $em = $this->getDoctrine()->getManager();

                $repo = $em->getRepository("AppBundle:Carousel");
                $repoCourse = $em->getRepository('AppBundle:Course');

                $oldCarousel = $repoCarousel->findOneBy(['course' => $carousel->getCourse()]) ?: $carousel;
                $course = $repoCourse->find($oldCarousel->getCourse()->getId());
                $course->setCarousel($oldCarousel);

                $oldCarousel->setName($carousel->getName());
                $oldCarousel->setPicture($carousel->getPicture());
                $oldCarousel->setCourse($carousel->getCourse());

                $em->persist($oldCarousel);
                $emp->persist($course);
                $em->flush();
                return $this->render('AppBundle:edited.html.twig');
            }
        } catch (\Exception $e) {
            return $this->render('AppBundle:error.html.twig');
        }
    }

    /**
     * @Route("/admin/carousel/delete/{id}/")
     * @Method("GET")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function showDeleteCarouselFormAction($id) {

        $repo = $this->getDoctrine()->getRepository('AppBundle:Carousel');
        $carousel = $repo->find($id);
        $em = $this->getDoctrine()->getManager();
        $em->remove($carousel);
        $em->flush();

        return new Response('Usunięto');
    }

    /**
     * @Route("/admin/movie/add/")
     * @Method("GET")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function showAddMovieFormAction() {
        $movie = new Movie();
        $form = $this->createFormBuilder($movie)
                ->add('title', TextType::class)
                ->add('description', TextType::class)
                ->add('course', EntityType::class, array(
                    'class' => 'AppBundle:Course',
                    'choice_label' => 'title'))
                ->add('path', FileType::class)
                ->add('Dodaj', SubmitType::class)
                ->getForm();

        return $this->render('AppBundle:Admin:basic_form.html.twig', array(
                    'form' => $form->createView()
        ));
    }

    /**
     * @Route("/admin/movie/add/")
     * @Method("POST")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function addMovieAction(Request $request) {
        try {
            $movie = new Movie();

            $form = $this->createFormBuilder($movie)
                    ->add('title', TextType::class)
                    ->add('description', TextType::class)
                    ->add('course', EntityType::class, array(
                        'class' => 'AppBundle:Course',
                        'choice_label' => 'title'))
                    ->add('path', FileType::class)
                    ->add('Dodaj', SubmitType::class)
                    ->getForm();

            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $file = $movie->getPath();
                $newPath = $file->move("Assets/movies/", $file->getClientOriginalName());
                $movie->setPath("/" . $newPath);
                $em = $this->getDoctrine()->getManager();
                $em->persist($movie);
                $em->flush();
            }
            return $this->render("AppBundle:Admin:added.html.twig");
        } catch (\Exception $e) {
            return $this->render("AppBundle:Admin:error.html.twig");
        }
    }

    /**
     * @Route("/admin/movie/edit/{id}/")
     * @Method("GET")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function showEditMovieFormAction($id) {
        $repo = $this->getDoctrine()->getRepository('AppBundle:Movie');
        $movie = $repo->find($id);
        $form = $this->createFormBuilder($movie)
                ->add('title', TextType::class)
                ->add('description', TextType::class)
                ->add('course', EntityType::class, array(
                    'class' => 'AppBundle:Course',
                    'choice_label' => 'title'))
                ->add('path', FileType::class, array('data_class' => null))
                ->add('Edytuj', SubmitType::class)
                ->getForm();

        return $this->render('AppBundle:Admin:basic_form.html.twig', array(
                    'form' => $form->createView()
        ));
    }

    /**
     * @Route("/admin/movie/edit/{id}/")
     * @Method("POST")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function editMovieAction(Request $request, $id) {
        try {
            $repo = $this->getDoctrine()->getRepository('AppBundle:Movie');
            $movie = $repo->find($id);
            $form = $this->createFormBuilder($movie)
                    ->add('title', TextType::class)
                    ->add('description', TextType::class)
                    ->add('course', EntityType::class, array(
                        'class' => 'AppBundle:Course',
                        'choice_label' => 'title'))
                    ->add('path', FileType::class, array('data_class' => null))
                    ->add('Edytuj', SubmitType::class)
                    ->getForm();

            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $file = $movie->getPath();
                $newPath = $file->move("/Assets/movies/", $file->getClientOriginalName());
                $movie->setPath($newPath);
                $em = $this->getDoctrine()->getManager();
                $em->persist($movie);
                $em->flush();
            }
            return $this->render("AppBundle:Admin:edited.html.twig");
        } catch (\Exception $e) {
            return $this->render("AppBundle:Admin:error.html.twig");
        }
    }

    /**
     * @Route("/admin/movie/delete/{id}/")
     * @Method("GET")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function deleteMovieAction($id) {
        try {
            $repo = $this->getDoctrine()->getRepository('AppBundle:Movie');
            $movie = $repo->find($id);
            $em = $this->getDoctrine()->getManager();
            $em->remove($movie);
            $em->flush();

            return $this->render("AppBundle:Admin:deleted.html.twig");
        } catch (\Exception $e) {
            return $this->render("AppBundle:Admin:delete_error.html.twig");
        }
    }

    /**
     * @Route("/admin/course/add/")
     * @Method("GET")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function showAddCourseFormAction() {
        $course = new Course();
        $form = $this->createFormBuilder($course)
                ->add('title', TextType::class)
                ->add('description', TextType::class)
                ->add('price', IntegerType::class)
                ->add('picture', FileType::class)
                ->add('Dodaj', SubmitType::class)
                ->getForm();

        return $this->render('AppBundle:Admin:basic_form.html.twig', array(
                    'form' => $form->createView()
        ));
    }

    /**
     * @Route("/admin/course/add/")
     * @Method("POST")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function addCourseAction(Request $request) {
        try {
            $course = new Course();

            $form = $this->createFormBuilder($course)
                    ->add('title', TextType::class)
                    ->add('description', TextType::class)
                    ->add('price', IntegerType::class)
                    ->add('picture', FileType::class)
                    ->add('Dodaj', SubmitType::class)
                    ->getForm();

            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $file = $course->getPicture();
                $newPath = $file->move("web/Assets/images/", $file->getClientOriginalName());
                $course->setPicture($newPath);
                $course->setCarousel(null);
                $course->setLikes(0);
                $em = $this->getDoctrine()->getManager();
                $em->persist($course);
                $em->flush();
            }
            return $this->render('AppBundle:Admin:added.html.twig');
        } catch (\Exception $e) {
            return $this->render('AppBundle:Admin:error.html.twig');
        }
    }

    /**
     * @Route("/admin/course/edit/{id}/")
     * @Method("GET")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function showEditCourseFormAction($id) {
        $repo = $this->getDoctrine()->getRepository('AppBundle:Course');
        $course = $repo->find($id);
        $form = $this->createFormBuilder($course)
                ->add('title', TextType::class)
                ->add('description', TextType::class)
                ->add('price', IntegerType::class)
                ->add('picture', FileType::class, array('data_class' => null))
                ->add('Dodaj', SubmitType::class)
                ->getForm();

        return $this->render('AppBundle:Admin:basic_form.html.twig', array(
                    'form' => $form->createView()
        ));
    }

    /**
     * @Route("/admin/course/edit/{id}/")
     * @Method("POST")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function editCourseAction(Request $request, $id) {
        try {
            $repo = $this->getDoctrine()->getRepository('AppBundle:Course');
            $course = $repo->find($id);
            $form = $this->createFormBuilder($course)
                    ->add('title', TextType::class)
                    ->add('description', TextType::class)
                    ->add('price', IntegerType::class)
                    ->add('picture', FileType::class, array('data_class' => null))
                    ->add('Dodaj', SubmitType::class)
                    ->getForm();

            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $file = $course->getPicture();
                $newPath = $file->move("web/Assets/images/", $file->getClientOriginalName());
                $course->setPicture($newPath);
                $em = $this->getDoctrine()->getManager();
                $em->persist($course);
                $em->flush();
            }
            return $this->render("AppBundle:Admin:added.html.twig");
        } catch (\Exception $e) {
            return $this->render("AppBundle:Admin:error.html.twig");
        }
    }

    /**
     * @Route("/admin/course/delete/{id}/")
     * @Method("GET")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function deleteCourseAction($id) {
        try {
            $repo = $this->getDoctrine()->getRepository('AppBundle:Course');
            $course = $repo->find($id);
            $em = $this->getDoctrine()->getManager();
            $em->remove($course);
            $em->flush();

            return new Response('AppBundle:Admin:deleted.html.twig');
        } catch (\Exception $e) {
            return $this->render('AppBundle:Admin:delete_carousel_error.html.twig');
        }
    }

    /**
     * @Route("/admin/user/edit/{id}/")
     * @Method("GET")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function showEditUserFormAction($id) {
        $repo = $this->getDoctrine()->getRepository('AppBundle:User');
        $user = $repo->find($id);
        if ($user->hasRole('ROLE_ADMIN')) {
            throw new Exception();
        }

        $form = $this->createFormBuilder($user)
                ->add('username', TextType::class)
                ->add('points', IntegerType::class)
                ->add('courses', EntityType::class, array(
                    'class' => 'AppBundle:Course',
                    'choice_label' => 'title',
                    'multiple' => 'true',
                    'expanded' => 'true'))
                ->add('Edytuj', SubmitType::class)
                ->getForm();

        return $this->render('AppBundle:Admin:admin_user_form.html.twig', array(
                    'form' => $form->createView()
        ));
    }

    /**
     * @Route("/admin/user/edit/{id}/")
     * @Method("POST")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function editUserAction(Request $request, $id) {
        try {
            $repo = $this->getDoctrine()->getRepository('AppBundle:User');
            $user = $repo->find($id);
            if ($user->hasRole('ROLE_ADMIN')) {
                throw new Exception();
            }
            $form = $this->createFormBuilder($user)
                    ->add('username', TextType::class)
                    ->add('points', IntegerType::class)
                    ->add('courses', EntityType::class, array(
                        'class' => 'AppBundle:Course',
                        'choice_label' => 'title',
                        'multiple' => 'true',
                        'expanded' => 'true'
                    ))
                    ->add('Edytuj', SubmitType::class)
                    ->getForm();

            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();
            }
            return $this->render("AppBundle:Admin:edited.html.twig");
        } catch (\Exception $e) {
            return $this->render("AppBundle:Admin:error.html.twig");
        }
    }

    /**<h1 style="margin-bottom: 30px">Zarejestruj się:</h1>
     * @Route("/admin/user/delete/{id}/")
     * @Method("GET")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function deleteUserAction($id) {
        try {
            $repo = $this->getDoctrine()->getRepository('AppBundle:User');
            $user = $repo->find($id);
            if ($user->hasRole('ROLE_ADMIN')) {
                throw new Exception();
            }
            $em = $this->getDoctrine()->getManager();
            $em->remove($user);
            $em->flush();

            return $this->render("AppBundle:Admin:deleted.html.twig");
        } catch (\Exception $e) {
            return $this->render("AppBundle:Admin:delete_error.html.twig");
        }
    }

}
