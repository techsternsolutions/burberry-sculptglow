<?php
namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\Submission;

class SubmissionRepository extends EntityRepository
{
    /**
     * @return Submission[]
     */
    public function findApproved()
    {
        return $this->createQueryBuilder('s')
            ->where('s.approved = true')
            ->orderBy('s.id', 'desc')
            ->getQuery()
            ->getResult();
    }
}