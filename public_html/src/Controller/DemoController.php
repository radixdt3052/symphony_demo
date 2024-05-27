<?php

// src/Controller/DemoController.php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Record;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\Tools\Pagination\Paginator;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

class DemoController extends AbstractController
{
    #[Route('/demo', name: 'app_demo')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Get the repository for the Record entity
        $recordRepository = $entityManager->getRepository(Record::class);

        // Set up pagination
        $page = $request->query->get('page', 1); // Get the current page from the request, default to 1 if not provided
        $perPage = 5; // Number of records per page
        $recordsQuery = $recordRepository->createQueryBuilder('r')
            ->getQuery();

        $paginator = new Paginator($recordsQuery);
        $paginator->getQuery()
            ->setFirstResult($perPage * ($page - 1)) // Offset calculation
            ->setMaxResults($perPage); // Limit

        // Fetch paginated records
        $records = $paginator->getIterator()->getArrayCopy();

        // Render view with paginated records
        return $this->render('demoIndex.html.twig', [
            'records' => $records,
            'page' => $page,
            'totalPages' => ceil(count($paginator) / $perPage)
        ]);
    }

    #[Route('/create/record', name: 'create_record')]
    public function index2(): Response
    {
        return $this->render('demo.html.twig');
    }

    #[Route('/form_submit', name: 'form_submit', methods: ['POST'])]
    public function submitForm(Request $request, EntityManagerInterface $entityManager)
    {
        // Retrieve form data from the request
        $name = $request->request->get('name');
        $email = $request->request->get('email');
        $phone = $request->request->get('phone');

        // Create a new instance of your entity and set its properties
        $entity = new Record();
        $entity->setName($name);
        $entity->setEmail($email);
        $entity->setPhone($phone);

        // Persist the entity to the database
        $entityManager->persist($entity);
        $entityManager->flush();

        // Optionally, you can add a flash message to indicate successful submission
        $this->addFlash('success', 'Form submitted successfully!');

        // Redirect the user to a different page
        return $this->redirectToRoute('create_record');
    }

    #[Route('/edit/record/{id}', name: 'edit_record')]
    public function editRecord(Request $request, EntityManagerInterface $entityManager, $id): Response
    {
        // Find the record entity by its ID
        $record = $entityManager->getRepository(Record::class)->find($id);

        // Render the edit form
        return $this->render('editRecord.html.twig', [
            'record' => $record,
        ]);
    }

    #[Route('/form_update', name: 'form_update', methods: ['POST'])]
    public function updateForm(Request $request, EntityManagerInterface $entityManager)
    {

        // Retrieve the existing record entity by its ID
        $record = $entityManager->getRepository(Record::class)->find($request->get('id'));
       
        // Retrieve form data from the request
        $name = $request->request->get('name');
        $email = $request->request->get('email');
        $phone = $request->request->get('phone');
        
        // Update the properties of the existing record entity
        $record->setName($name);
        $record->setEmail($email);
        $record->setPhone($phone);

        // Persist the changes to the database
        $entityManager->flush();

        // Optionally, you can add a flash message to indicate successful update
        $this->addFlash('success', 'Record updated successfully!');

        // Redirect the user to a different page
        return $this->redirectToRoute('app_demo');
    }
}
