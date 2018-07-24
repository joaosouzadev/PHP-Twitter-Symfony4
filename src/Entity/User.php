<?php

namespace App\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity(fields="email", message="This e-mail is already used.")
 * @UniqueEntity(fields="username", message="This username is already used.")
 */
class User implements AdvancedUserInterface, \Serializable
{

    const ROLE_USER = 'ROLE_USER';
    const ROLE_ADMIN = 'ROLE_ADMIN';

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

     /**
     * @ORM\Column(type="string", length=50, unique=true)
     * @Assert\NotBlank()
     * @Assert\Length(min= 5, max= 50)
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $password;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(min= 8, max= 50)
     */
    private $plainPassword;

    /**
     * @ORM\Column(type="string", length=50, unique=true)
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=50)
     * @Assert\NotBlank()
     * @Assert\Length(min= 8, max= 50)
     */
    private $fullName;

    /**
    * @ORM\OneToMany(targetEntity="App\Entity\MicroPost", mappedBy="user")
    * @ORM\JoinColumn()
    */
    private $posts;

    /**
     * @ORM\Column(type="simple_array")
     */
    private $roles;

    /**
    * @ORM\ManyToMany(targetEntity="App\Entity\User", mappedBy="following")
    */
    private $followers;

    /**
    * @ORM\ManyToMany(targetEntity="App\Entity\User", inversedBy="followers")
    * @ORM\JoinTable(name="following",
    *   joinColumns={
    *       @ORM\JoinColumn(name="user_id", referencedColumnName="id")
    *   },
    *   inverseJoinColumns={
    *       @ORM\JoinColumn(name="following_user_id", referencedColumnName="id")
    *   }
    * )
    */
    private $following;

    /**
    * @ORM\ManyToMany(targetEntity="App\Entity\MicroPost", mappedBy="likedBy")
    */
    private $postsLiked;

    /**
     * @ORM\Column(type="string", nullable=true, length=30)
     */
    private $confirmationToken;

    /**
     * @ORM\Column(type="boolean")
     */
    private $enabled;

    public function __construct() {
        $this->posts = new ArrayCollection();
        $this->followers = new ArrayCollection();
        $this->following = new ArrayCollection();
        $this->postsLiked = new ArrayCollection();
        $this->roles = [self::ROLE_USER];
        $this->enabled = false;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getRoles(){

        return $this->roles;
    }

    public function setRoles(array $roles): void {
        $this->roles = $roles;
    }

    public function getPassword(){
        return $this->password;
    }

    public function getSalt(){
        return null;
    }

    public function getUsername(){
        return $this->username;
    }

    public function eraseCredentials(){
        
    }

    public function serialize()
    {
        return serialize([
            $this->id,
            $this->username,
            $this->password,
            $this->enabled
        ]);
    }

    public function unserialize($serialized)
    {
        list($this->id,
            $this->username,
            $this->password,
            $this->enabled) = unserialize($serialized);
    }


    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email): void
    {
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getFullName()
    {
        return $this->fullName;
    }

    /**
     * @param mixed $fullName
     */
    public function setFullName($fullName): void
    {
        $this->fullName = $fullName;
    }

    /**
     * @param mixed $username
     */
    public function setUsername($username): void
    {
        $this->username = $username;
    }

    /**
     * @param mixed $password
     */
    public function setPassword($password): void
    {
        $this->password = $password;
    }

    public function getPlainPassword(){
        return $this->plainPassword;
    }

    public function setPlainPassword($plainPassword): void {
            $this->plainPassword = $plainPassword;
    }

    public function getPosts(){
        return $this->posts;
    }

    /**
     * @return Collection
     */
    public function getFollowers() {
        return $this->followers;
    }

    /**
     * @return Collection
     */
    public function getFollowing() {
        return $this->following;
    }

    public function addPost(MicroPost $post): self
    {
        if (!$this->posts->contains($post)) {
            $this->posts[] = $post;
            $post->setUser($this);
        }

        return $this;
    }

    public function removePost(MicroPost $post): self
    {
        if ($this->posts->contains($post)) {
            $this->posts->removeElement($post);
            // set the owning side to null (unless already changed)
            if ($post->getUser() === $this) {
                $post->setUser(null);
            }
        }

        return $this;
    }

    public function addFollower(User $follower): self
    {
        if (!$this->followers->contains($follower)) {
            $this->followers[] = $follower;
            $follower->addFollowing($this);
        }

        return $this;
    }

    public function removeFollower(User $follower): self
    {
        if ($this->followers->contains($follower)) {
            $this->followers->removeElement($follower);
            $follower->removeFollowing($this);
        }

        return $this;
    }

    public function addFollowing(User $following): self
    {
        if (!$this->following->contains($following)) {
            $this->following[] = $following;
        }

        return $this;
    }

    public function removeFollowing(User $following): self
    {
        if ($this->following->contains($following)) {
            $this->following->removeElement($following);
        }

        return $this;
    }

    public function follow(User $userToFollow) {
        if ($this->getFollowing()->contains($userToFollow)) {
            return;
        }

        $this->getFollowing()->add($userToFollow);
    }

    /**
    * @return Collection
    */
    public function getPostsLiked() {
        return $this->postsLiked;
    }

    public function getConfirmationToken(): ?string
    {
        return $this->confirmationToken;
    }

    public function setConfirmationToken(?string $confirmationToken): self
    {
        $this->confirmationToken = $confirmationToken;

        return $this;
    }

    public function addPostsLiked(MicroPost $postsLiked): self
    {
        if (!$this->postsLiked->contains($postsLiked)) {
            $this->postsLiked[] = $postsLiked;
            $postsLiked->addLikedBy($this);
        }

        return $this;
    }

    public function removePostsLiked(MicroPost $postsLiked): self
    {
        if ($this->postsLiked->contains($postsLiked)) {
            $this->postsLiked->removeElement($postsLiked);
            $postsLiked->removeLikedBy($this);
        }

        return $this;
    }

    public function getEnabled(): ?bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): self
    {
        $this->enabled = $enabled;

        return $this;
    }

    public function isAccountNonExpired() {
        return true;
    }

    public function isAccountNonLocked() {
        return true;
    }

    public function isCredentialsNonExpired() {
        return true;
    }

    public function isEnabled() {
        return $this->enabled;
    }
}
