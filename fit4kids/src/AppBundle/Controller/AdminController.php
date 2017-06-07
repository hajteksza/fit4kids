<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Carousel;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

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
    public function showAdminPanelCarouselAction() {
        
        $repo = $this->getDoctrine()->getRepository('AppBundle:Carousel');
        $carousels = $repo->findAll();
        
        return $this->render('AppBundle:Admin:show_admin_carousel.html.twig', array(
                        "carousels" => $carousels
        ));
    }

    /**
     * @Route("/admin/course/")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function showAdminCourseAction() {
        return $this->render('AppBundle:Admin:show_admin_course.html.twig', array(
                        // ...
        ));
    }

    /**
     * @Route("/admin/movie/")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function showAdminMovieAction() {
        return $this->render('AppBundle:Admin:show_admin_movie.html.twig', array(
                        // ...
        ));
    }

    /**
     * @Route("/admin/carousel/add/")
     * @Method("GET")
     */
    public function showAddCarouselFormAction() {
        $carousel = new Carousel();
        $form = $this->createFormBuilder()
                ->add('name', TextType::class)
                ->add('course', EntityType::class, array(
                    'class' => 'AppBundle:Course',
                    'choice_label' => 'title'))
                ->add('picture', FileType::class)
                ->add('Dodaj', SubmitType::class)
                ->getForm();

        return $this->render('AppBundle:Admin:add_carousel_form.html.twig', array(
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

                $repo = $em->getRepository("AppBundle:Carousel");
                $oldCarousel = $repo->findOneBy(['course' => $carousel->getCourse()]) ?: $carousel;

                $oldCarousel->setName($carousel->getName());
                $oldCarousel->setPicture($carousel->getPicture());
                $oldCarousel->setCourse($carousel->getCourse());

                $em->persist($oldCarousel);
                $em->flush();
            }
        }
        return $this->render('AppBundle:Admin:add_carousel.html.twig', array(
        ));
    }
}