<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Submission;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class SubmissionData implements FixtureInterface
{
    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $s = new Submission();
        $s->setLocation('London');
        $s->setName('Eliazbeth Thomas');
        $s->setPath('Wendy_Rowe.png');
        $s->setApproved(true);
        $s->setLikes(0);

        $manager->persist($s);

        $s = new Submission();
        $s->setLocation('Toronto');
        $s->setName('Elizabeth Thomas');
        $s->setPath('Wendy_Rowe.png');
        $s->setApproved(true);
        $s->setLikes(0);

        $manager->persist($s);

        $manager->flush();
    }
}