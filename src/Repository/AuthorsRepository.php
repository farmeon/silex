<?php

namespace Repository;

use Entities\Authors;
use Entity\Books;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Class AuthorsRepository
 * @package Repository
 * @method Authors find($id, $lockMode = null, $lockVersion = null)
 */
class AuthorsRepository extends ServiceEntityRepository
{

}