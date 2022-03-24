<?php

namespace App\Controller;

use App\Entity\Student;
use App\Form\StudentType;
use App\Repository\StudentRepository;
use App\Service\Api;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StudentController extends AbstractController
{
    /**
     * @Route("/student", name="app_student")
     */
    public function index(Request $request,
                          StudentRepository $studentRepository,
                          EntityManagerInterface $entityManager,
                          Api $api): Response
    {
        $student = new Student();
        $form = $this->createForm(StudentType::class,$student);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $student->setUserMemory($_COOKIE["userMemory"]);

            $completCity = $form->get('address')->getData() . " " . $form->get('zipcode')->getData() . " " . $form->get('city')->getData();
            $locate = $api->localize($completCity);

            if (empty($locate)) {
                $locate[0]['lat'] = 0;
                $locate[0]['lon'] = 0;
            }

            $student->setLatitude($locate[0]['lat']);
            $student->setLongitude($locate[0]['lon']);
            $entityManager->persist($student);
            $entityManager->flush();

            if($locate[0]['lat'] === 0 & $locate[0]['lon'] === 0) {
                $this->addFlash('danger','Ville inconnue');
            }

            return $this->redirectToRoute('app_student', [], Response::HTTP_SEE_OTHER);
        }

        if(isset($_COOKIE["userMemory"])){
            $memoryStudent =  $studentRepository->findBy(['userMemory'=>$_COOKIE['userMemory']]);
        }else{
            $memoryStudent =  [];
        }


        return $this->render('student/index.html.twig', [
            'students' => $memoryStudent,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{id}", name="student_delete", methods={"POST"})
     */
    public function delete(Request $request, Student $student, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $student->getId(), $request->request->get('_token'))) {
            $entityManager->remove($student);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_student', [], Response::HTTP_SEE_OTHER);
    }
}
