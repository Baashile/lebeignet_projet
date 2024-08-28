<?php

namespace App\Repository;

use App\Entity\Commande;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class CommandeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Commande::class);
    }

    public function findAllWithClientInfo()
    {
        return $this->createQueryBuilder('c')
            ->leftJoin('c.client', 'cl')
            ->addSelect('cl')
            ->getQuery()
            ->getResult();
    }

    public function findArchived()
    {
        return $this->createQueryBuilder('c')
            ->innerJoin('c.client', 'cl')
            ->addSelect('cl')
            ->where('c.archived = :archived')
            ->setParameter('archived', true)
            ->getQuery()
            ->getResult();
    }

    public function archive(Commande $commande): self
    {
        $commande->setArchived(true);
        $this->getEntityManager()->persist($commande);
        $this->getEntityManager()->flush();

        return $this;
    }

    public function unarchive(Commande $commande): self
    {
        $commande->setArchived(false);
        $this->getEntityManager()->persist($commande);
        $this->getEntityManager()->flush();

        return $this;
    }
}
