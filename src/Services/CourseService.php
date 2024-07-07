<?php

namespace Src\Services;

use Src\Models\Course;
use Src\Models\Step;

class CourseService
{
    /**
     * Check if course is completed
     */
    public function checkCourseCompleted(Course $course): bool
    {
        require "../config/bootstrap.php";

        $allStepsCount = $entityManager->createQueryBuilder()
            ->select('count(s.id)')
            ->from(Step::class, 's')
            ->leftJoin('s.section', 'se')
            ->leftJoin('se.course', 'c')
            ->where('c.id = :c_id')->setParameter('c_id', $course->getId())
            ->getQuery()
            ->getSingleScalarResult();

        $allCompletedStepsByUser = $entityManager->createQueryBuilder()
            ->select('count(s.id)')
            ->from(Step::class, 's')
            ->leftJoin('s.section', 'se')
            ->leftJoin('se.course', 'c')
            ->leftJoin('s.users', 'u')
            ->where('c.id = :c_id')->setParameter('c_id', $course->getId())
            ->andWhere('u.id = :u_id')->setParameter('u_id', $_SESSION['auth']['id'])
            ->getQuery()
            ->getSingleScalarResult();

        if ($allStepsCount == $allCompletedStepsByUser) return true;

        return false;
    }
}
