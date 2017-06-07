<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Entity\Carousel;
use AppBundle\Entity\Movie;
use AppBundle\Entity\Course;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class AdminController extends Controller {

    /**
     * @Route("/admin/")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function showAdminPanelAction() {
        return $this->render('AppBundle:Admin:show_admin_panel.html.twig', array(
                        // ...
        ));
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
     * @Route("/admin/carousel/add/")
     * @Method("GET")
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
     */
    public function addCarouselAction(Request $request) { {
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
        }
        return new Response('Dodano');
    }

    /**
     * @Route("/admin/carousel/edit/{id}/")
     * @Method("GET")
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
     */
    public function editCarouselAction(Request $request, $id) { {
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
            }
        }
        return new Response('Edytowano');
    }

    /**
     * @Route("/admin/carousel/delete/{id}/")
     * @Method("GET")
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
     */
    public function addMovieAction(Request $request) { {
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
                $newPath = $file->move("web/Assets/movies/", $file->getClientOriginalName());
                $movie->setPath($newPath);
                $em = $this->getDoctrine()->getManager();
                $em->persist($movie);
                $em->flush();
            }
        }
        return new Response('Dodano');
    }

    /**
     * @Route("/admin/movie/edit/{id}/")
     * @Method("GET")
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
     */
    public function editMovieAction(Request $request, $id) { {
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
                $newPath = $file->move("web/Assets/movies/", $file->getClientOriginalName());
                $movie->setPath($newPath);
                $em = $this->getDoctrine()->getManager();
                $em->persist($movie);
                $em->flush();
            }
        }
        return new Response('Edytowano');
    }

    /**
     * @Route("/admin/movie/delete/{id}/")
     * @Method("GET")
     */
    public function deleteMovieAction($id) {

        $repo = $this->getDoctrine()->getRepository('AppBundle:Movie');
        $movie = $repo->find($id);
        $em = $this->getDoctrine()->getManager();
        $em->remove($movie);
        $em->flush();

        return new Response('Usunięto');
    }

    /**
     * @Route("/admin/course/add/")
     * @Method("GET")
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
     */
    public function addCourseAction(Request $request) { {
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
        }
        return new Response('Dodano');
    }

    /**
     * @Route("/admin/course/edit/{id}/")
     * @Method("GET")
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
     */
    public function editCourseAction(Request $request, $id) { {
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
                $file = $course->getPath();
                $newPath = $file->move("web/Assets/images/", $file->getClientOriginalName());
                $course->setPath($newPath);
                $em = $this->getDoctrine()->getManager();
                $em->persist($course);
                $em->flush();
            }
        }
        return new Response('Edytowano');
    }

    /**
     * @Route("/admin/course/delete/{id}/")
     * @Method("GET")
     */
    public function deleteCourseAction($id) {

        $repo = $this->getDoctrine()->getRepository('AppBundle:Course');
        $course = $repo->find($id);
        $em = $this->getDoctrine()->getManager();
        $em->remove($course);
        $em->flush();

        return new Response('Usunięto');
    }

}
