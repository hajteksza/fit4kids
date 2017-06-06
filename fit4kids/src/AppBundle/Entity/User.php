<?php
// src/AppBundle/Entity/User.php

namespace AppBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping\JoinTable;
use Doctrine\ORM\Mapping\JoinColumn;

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 */

class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToMany(targetEntity="Course", inversedBy="users")
     * @JoinTable(name="users_courses")
     */

    protected $courses;

    /**
     * @ORM\ManyToMany(targetEntity="Course", inversedBy="likedBy")
     * @JoinTable(name="users_likes")
     */

    protected $likes;

    /**
     * @ORM\ManyToMany(targetEntity="Movie", inversedBy="users")
     * @JoinTable(name="movies_users")
     */

    protected $movies;

    /**
     * @ORM\OneToOne(targetEntity="Basket", mappedBy="user")
     */

    protected $basket;

    /**
     * @ORM\OneToMany(targetEntity="Rating", mappedBy="user")
     */

    protected $ratings;

    /**
     * @ORM\OneToMany(targetEntity="Comment", mappedBy="user")
     */

    protected $comments;

    /**
     * @ORM\Column(type="integer")
     */

    protected $points = 0;

    public function __construct()
    {
        parent::__construct();
        $this->courses = new ArrayCollection();
        $this->ratings = new ArrayCollection();
        $this->comments = new ArrayCollection();
    }

    /**
     * Add course
     *
     * @param \AppBundle\Entity\Course $course
     *
     * @return User
     */
    public function addCourse(\AppBundle\Entity\Course $course)
    {
        $this->courses[] = $course;

        return $this;
    }

    /**
     * Remove course
     *
     * @param \AppBundle\Entity\Course $course
     */
    public function removeCourse(\AppBundle\Entity\Course $course)
    {
        $this->courses->removeElement($course);
    }

    /**
     * Get courses
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCourses()
    {
        return $this->courses;
    }

    /**
     * Add like
     *
     * @param \AppBundle\Entity\Course $like
     *
     * @return User
     */
    public function addLike(\AppBundle\Entity\Course $like)
    {
        $this->likes[] = $like;

        return $this;
    }

    /**
     * Remove like
     *
     * @param \AppBundle\Entity\Course $like
     */
    public function removeLike(\AppBundle\Entity\Course $like)
    {
        $this->likes->removeElement($like);
    }

    /**
     * Get likes
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getLikes()
    {
        return $this->likes;
    }

    /**
     * Set basket
     *
     * @param \AppBundle\Entity\Basket $basket
     *
     * @return User
     */
    public function setBasket(\AppBundle\Entity\Basket $basket = null)
    {
        $this->basket = $basket;

        return $this;
    }

    /**
     * Get basket
     *
     * @return \AppBundle\Entity\Basket
     */
    public function getBasket()
    {
        return $this->basket;
    }

    /**
     * Add rating
     *
     * @param \AppBundle\Entity\Rating $rating
     *
     * @return User
     */
    public function addRating(\AppBundle\Entity\Rating $rating)
    {
        $this->ratings[] = $rating;

        return $this;
    }

    /**
     * Remove rating
     *
     * @param \AppBundle\Entity\Rating $rating
     */
    public function removeRating(\AppBundle\Entity\Rating $rating)
    {
        $this->ratings->removeElement($rating);
    }

    /**
     * Get ratings
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRatings()
    {
        return $this->ratings;
    }

    /**
     * Set points
     *
     * @param integer $points
     *
     * @return User
     */
    public function setPoints($points)
    {
        $this->points = $points;

        return $this;
    }

    /**
     * Get points
     *
     * @return integer
     */
    public function getPoints()
    {
        return $this->points;
    }

    /**
     * Add movie
     *
     * @param \AppBundle\Entity\Movies $movie
     *
     * @return User
     */
    public function addMovie(\AppBundle\Entity\Movies $movie)
    {
        $this->movies[] = $movie;

        return $this;
    }

    /**
     * Remove movie
     *
     * @param \AppBundle\Entity\Movies $movie
     */
    public function removeMovie(\AppBundle\Entity\Movies $movie)
    {
        $this->movies->removeElement($movie);
    }

    /**
     * Get movies
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMovies()
    {
        return $this->movies;
    }

    /**
     * Add comment
     *
     * @param \AppBundle\Entity\Comment $comment
     *
     * @return User
     */
    public function addComment(\AppBundle\Entity\Comment $comment)
    {
        $this->comments[] = $comment;

        return $this;
    }

    /**
     * Remove comment
     *
     * @param \AppBundle\Entity\Comment $comment
     */
    public function removeComment(\AppBundle\Entity\Comment $comment)
    {
        $this->comments->removeElement($comment);
    }

    /**
     * Get comments
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getComments()
    {
        return $this->comments;
    }
}
