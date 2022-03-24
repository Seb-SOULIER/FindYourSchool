<?php

namespace App\Controller;

use App\Entity\Student;
use App\Service\Api;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SchoolController extends AbstractController
{
    /**
     * @Route("/school/list/{id}/{dist}", name="school_list", methods={"GET"})
     */
    public function index(Api $api, Student $student, $dist = 5000): Response
    {
        return $this->render('school/index.html.twig', [
            'student' => $student,
            'schools' => $api->searchSchool($student,$dist),
            'dist'=>$dist
        ]);
    }

    /**
     * @Route("/school/{id}/{dist}/show/{school}", name="school_show")
     */
    public function show(EntityManagerInterface $entityManager, Api $api, Student $student, Request $request, $dist=5000): Response
    {
        $school=[];
        $schools = $api->searchSchool($student,$dist);
        foreach ($schools['records'] as $schol) {
            if ($schol['fields']['identifiant_de_l_etablissement'] === $request->get('school')) {
                $school = $schol;
            }
        }

        if ($this->isCsrfTokenValid('school'.$student->getId(), $request->request->get('_token'))) {
            $student->setSchoolName($school['fields']['nom_etablissement']);
            $student->setSchoolId($school['fields']['identifiant_de_l_etablissement']);
            $entityManager->flush();
            $this->addFlash('success',
                $student->getSchoolName()
                . ' ajouter à ' . $student->getFirstname());
            return $this->redirectToRoute('app_student');
        }

        return $this->render('school/show.html.twig', [
            'student' => $student,
            'schools' => $schools,
            'school' => $school
        ]);
    }
}
