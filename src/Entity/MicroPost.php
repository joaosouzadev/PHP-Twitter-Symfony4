<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\ManyToMany;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MicroPostRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class MicroPost
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
    * @ORM\Column(type="string", length=280)
    * @Assert\Length(min=10, minMessage="Mensagem muito curta. Use pelo menos 10 caracteres.")
    */
    private $text;

    /**
    * @ORM\Column(type="datetime")
    */
    private $time;

    /**
    * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="posts")
    * @ORM\JoinColumn(nullable=false)
    */
    private $user;

    /**
    * @ORM\ManyToMany(targetEntity="App\Entity\User", inversedBy="postsLiked")
    * @ORM\JoinTable(name="post_likes",
    *   joinColumns={@ORM\JoinColumn(name="post_id", referencedColumnName="id")},
    *   inverseJoinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")}
    * )
    */
    private $likedBy;

    public function __construct() {
        $this->likedBy = new ArrayCollection();
    }

    public function getId(){
        return $this->id;
    }

    public function getText(){
        return $this->text;
    }

    public function setText($text): void{
        $this->text = $text;
    }

    public function getTime(){
        return $this->time;
    }

    public function setTime($time): void{
        $this->time = $time;
    }

    /**
    * @ORM\PrePersist()
    */
    public function setTimeOnPersist(): void {
        $this->time = new \DateTime();
    }

    public function getUser(){
        return $this->user;
    }

    public function setUser($user): void{
        $this->user = $user;
    }

    /**
    * @return Collection
    */
    public function getLikedBy() {
        return $this->likedBy;
    }

    public function like(User $user) {
        if ($this->likedBy->contains($user)) {
            return;
        }

        $this->likedBy->add($user);
    }
}
