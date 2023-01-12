<?php

declare(strict_types=1);

namespace App\Model;

use DateTimeImmutable;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\ManyToOne;

#[Entity]
#[Table('stock_quote_researches')]
final class StockQuoteResearch
{
    #[Id]
    #[Column(type: 'integer'), GeneratedValue]
    private int $id;

    #[Column(name: 'user_id', type: 'integer', nullable: false)]
    private int $userId;
    
    #[Column(name: 'date', type: 'datetimetz_immutable', nullable: false)]
    private DateTimeImmutable $date;

    #[Column(type: 'string', nullable: false)]
    private string $name;
    
    #[Column(type: 'string', nullable: false)]
    private string $symbol;

    #[Column(type: 'decimal', precision: 10, scale: 3, nullable: false)]
    private float $open;
    
    #[Column(type: 'decimal', precision: 10, scale: 3, nullable: false)]
    private float $high;
    
    #[Column(type: 'decimal', precision: 10, scale: 3, nullable: false)]
    private float $low;
    
    #[Column(type: 'decimal', precision: 10, scale: 3, nullable: false)]
    private float $close;

    #[ManyToOne(targetEntity: User::class, inversedBy: 'researches')]
    private User $user;

    public function __construct()
    {
        $this->date = new DateTimeImmutable('now');
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getDate(): DateTimeImmutable {
        return $this->date;
    }

    public function getName(): string
    {
        return $this->name;
    }
    
    public function getSymbol(): string
    {
        return $this->symbol;
    }
    
    public function getOpen (): float
    {
        return $this->open;
    }
    
    public function getHigh (): float
    {
        return $this->high;
    }
    
    public function getLow (): float
    {
        return $this->low;
    }
    
    public function getClose (): float
    {
        return $this->close;
    }
    
    public function getUser (): User
    {
        return $this->user;
    }

    public function setUserId(int $userId): StockQuoteResearch {
        $this->userId = $userId;
        return $this;
    }

    public function setName(string $name): StockQuoteResearch {
        $this->name = $name;
        return $this;
    }

    public function setSymbol(string $symbol): StockQuoteResearch {
        $this->symbol = $symbol;
        return $this;
    }

    public function setOpen(float $open): StockQuoteResearch {
        $this->open = $open;
        return $this;
    }

    public function setHigh(float $high): StockQuoteResearch {
        $this->high = $high;
        return $this;
    }

    public function setLow(float $low): StockQuoteResearch {
        $this->low = $low;
        return $this;
    }

    public function setClose(float $close): StockQuoteResearch {
        $this->close = $close;
        return $this;
    }
    
    public function setUser(User $user): StockQuoteResearch {
        $this->user = $user;
        return $this;
    }
   

}

?>