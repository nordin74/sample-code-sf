<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\MappedSuperclass;


/** @MappedSuperclass */
abstract class AbstractMainTableDefinition
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    protected int $id;


    /**
     * @ORM\Column(type="integer")
     */
    protected string $msisdn;


    /**
     * @ORM\Column(type="smallint")
     */
    protected int $operatorid;


    /**
     * @ORM\Column(type="string", length=15)
     */
    protected string $text;


    /**
     * @ORM\Column(type="string", length=60, nullable=true)
     */
    protected ?string $auth_token;


    /**
     * @ORM\Column(type="string", length=45)
     */
    protected string $node;


    /**
     * @ORM\Column(type="datetime_immutable")
     */
    protected \DateTimeImmutable $created_at;



    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->created_at;
    }


    public function setCreatedAt(\DateTimeImmutable $created_at): void
    {
        $this->created_at = $created_at;
    }


    public function getNode(): string
    {
        return $this->node;
    }


    public function setNode(string $node): void
    {
        $this->node = $node;
    }


    public function getAuthToken(): ?string
    {
        return $this->auth_token;
    }


    public function setAuthToken(?string $auth_token): void
    {
        $this->auth_token = $auth_token;
    }


    public function getText(): string
    {
        return $this->text;
    }


    public function setText($text): void
    {
        $this->text = $text;
    }


    public function getOperatorid(): int
    {
        return $this->operatorid;
    }


    public function setOperatorid(int $operatorid): void
    {
        $this->operatorid = $operatorid;
    }


    public function getMsisdn(): int
    {
        return $this->msisdn;
    }


    public function setMsisdn(int $msisdn): void
    {
        $this->msisdn = $msisdn;
    }


    public function getId(): int
    {
        return $this->id;
    }


    public function setId(int $id): void
    {
        $this->id = $id;
    }
}