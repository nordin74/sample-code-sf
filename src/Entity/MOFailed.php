<?php

namespace App\Entity;

use App\Repository\MOFailedRepository;
use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity(repositoryClass=MOFailedRepository::class)
 */
class MOFailed extends AbstractMainTableDefinition
{

}