<?php

declare(strict_types=1);

namespace App\Model;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

#[Entity]
#[Table('users')]
final class User
{
    #[Id]
    #[Column(type: 'integer'), GeneratedValue]
    private int $id;

    #[Column(type: 'string', unique: true, nullable: false)]
    private string $email;
    
    #[Column(type: 'string', nullable: false)]
    private string $name;
    
    #[Column(type: 'string', nullable: false)]
    private string $password;

    #[OneToMany(targetEntity: StockQuoteResearch::class, mappedBy: 'user', cascade: [ 'remove' ])]
    private Collection $researches;

    public function __construct()
    {
        $this->researches = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }
    
    public function getName(): string
    {
        return $this->name;
    }
    
    public function getPassword(): string
    {
        return $this->password;
    }
    
    public function getResearches(): Collection {
        return $this->researches;
    }

    public function setName(string $name): User {
        $this->name = $name;
        return $this;
    }

    public function setPassword(string $password): User {
        $this->password = $password;
        return $this;
    }

    public function setEmail(string $email): User {
        $this->email = $email;
        return $this;
    }

    public function addResearch(StockQuoteResearch $research): User {
        $research->setUser($this);
        $this->researches->add($research);
        return $this;
    }
    
}

?>