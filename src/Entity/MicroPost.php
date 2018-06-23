<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MicroPostRepository")
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
}
