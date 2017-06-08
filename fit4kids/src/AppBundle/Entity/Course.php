<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Course
 *
 * @ORM\Table(name="course")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CourseRepository")
 */
class Course {

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text")
     */
    private $description;

    /**
     * @var int
     *
     * @ORM\Column(name="price", type="integer")
     */
    private $price;

    /**
     * @var int
     *
     * @ORM\Column(name="likes", type="integer")
     */
    private $likes;

    /**
     * @ORM\Column(name="picture", type="string", length=255)
     */
    private $picture;

    /**
     * @ORM\ManyToMany(targetEntity="User", mappedBy="courses")
     */
    protected $users;

    /**
     * @ORM\ManyToMany(targetEntity="User", mappedBy="likes")
     */
    protected $likedBy;

    /**
     * @ORM\ManyToMany(targetEntity="Basket", inversedBy="courses")
     */
    protected $baskets;

    /**
     * @ORM\OneToMany(targetEntity="Movie", mappedBy="course")
     */
    protected $movies;
    
    /**
     * @ORM\OneToOne(targetEntity="Carousel", inversedBy="course")
     * @ORM\JoinColumn(name="carousel_id", referencedColumnName="id")
     */
    private $carousel;
    
    public $addedByLoggedUser;

    public function __construct() {
        $this->users = new ArrayCollection();
        $this->movies = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return Course
     */
    public function setTitle($title) {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle() {
        return $this->title;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Course
     */
    public function setDescription($description) {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription() {
        return $this->description;
    }

    /**
     * Set price
     *
     * @param integer $price
     *
     * @return Course
     */
    public function setPrice($price) {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return int
     */
    public function getPrice() {
        return $this->price;
    }

    /**
     * Set likes
     *
     * @param integer $likes
     *
     * @return Course
     */
    public function setLikes($likes) {
        $this->likes = $likes;

        return $this;
    }

    /**
     * Get likes
     *
     * @return int
     */
    public function getLikes() {
        return $this->likes;
    }

    /**
     * Add user
     *
     * @param \AppBundle\Entity\User $user
     *
     * @return Course
     */
    public function addUser(\AppBundle\Entity\User $user) {
        $this->users[] = $user;

        return $this;
    }

    /**
     * Remove user
     *
     * @param \AppBundle\Entity\User $user
     */
    public function removeUser(\AppBundle\Entity\User $user) {
        $this->users->removeElement($user);
    }

    /**
     * Get users
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUsers() {
        return $this->users;
    }

    /**
     * Add likedBy
     *
     * @param \AppBundle\Entity\User $likedBy
     *
     * @return Course
     */
    public function addLikedBy(\AppBundle\Entity\User $likedBy) {
        $this->likedBy[] = $likedBy;

        return $this;
    }

    /**
     * Remove likedBy
     *
     * @param \AppBundle\Entity\User $likedBy
     */
    public function removeLikedBy(\AppBundle\Entity\User $likedBy) {
        $this->likedBy->removeElement($likedBy);
    }

    /**
     * Get likedBy
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getLikedBy() {
        return $this->likedBy;
    }

    /**
     * Add basket
     *
     * @param \AppBundle\Entity\Basket $basket
     *
     * @return Course
     */
    public function addBasket(\AppBundle\Entity\Basket $basket) {
        $this->baskets[] = $basket;

        return $this;
    }

    /**
     * Remove basket
     *
     * @param \AppBundle\Entity\Basket $basket
     */
    public function removeBasket(\AppBundle\Entity\Basket $basket) {
        $this->baskets->removeElement($basket);
    }

    /**
     * Get baskets
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBaskets() {
        return $this->baskets;
    }

    /**
     * Add movie
     *
     * @param \AppBundle\Entity\Movie $movie
     *
     * @return Course
     */
    public function addMovie(\AppBundle\Entity\Movie $movie) {
        $this->movies[] = $movie;

        return $this;
    }

    /**
     * Remove movie
     *
     * @param \AppBundle\Entity\Movie $movie
     */
    public function removeMovie(\AppBundle\Entity\Movie $movie) {
        $this->movies->removeElement($movie);
    }

    /**
     * Get movies
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMovies() {
        return $this->movies;
    }

    /**
     * Set picture
     *
     * @param string $picture
     *
     * @return Course
     */
    public function setPicture($picture) {
        $this->picture = $picture;

        return $this;
    }

    /**
     * Get picture
     *
     * @return string
     */
    public function getPicture() {
        return $this->picture;
    }


    /**
     * Set carousel
     *
     * @param \AppBundle\Entity\Carousel $carousel
     *
     * @return Course
     */
    public function setCarousel(\AppBundle\Entity\Carousel $carousel = null)
    {
        $this->carousel = $carousel;

        return $this;
    }

    /**
     * Get carousel
     *
     * @return \AppBundle\Entity\Carousel
     */
    public function getCarousel()
    {
        return $this->carousel;
    }
}
