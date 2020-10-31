<?php

namespace App\Entity;

use App\Repository\MORepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\PrePersist;


/**
 * @ORM\Entity(repositoryClass=MORepository::class)
 */
class MO extends AbstractMainTableDefinition
{

}