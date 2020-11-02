<?php

namespace App\Entity;

use App\Repository\TaskRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TaskRepository::class)
 */
class Task
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

   /**
   * @ORM\Column(type="text")
   */
   private $body;

   /**
    * @ORM\ManyToOne(targetEntity=User::class, inversedBy="tasks")
    * @ORM\JoinColumn(nullable=false)
    */
   private $user;

   /**
    * @ORM\Column(type="boolean")
    */
   private $isDone;

    //Getters & Setters
    public function getId(){
        return $this->id;
    }
  
    public function getBody(){
        return $this->body;
    }

    public function setBody($body){
        $this->body = $body;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getIsDone(): ?bool
    {
        return $this->isDone;
    }

    public function setIsDone(bool $isDone): self
    {
        $this->isDone = $isDone;

        return $this;
    }

}