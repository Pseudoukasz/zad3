<?php

namespace App\Controller;

use App\Entity\Sale;
use App\Entity\Zajecia;
use App\Form\ZajeciaType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ApiController extends AbstractController
{
    private $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }
    public function fetchUsers(): array
    {
        $response = $this->client->request(
            'GET',
            'https://gorest.co.in/public-api/users'
        );

        $statusCode = $response->getStatusCode();
        // $statusCode = 200
        $contentType = $response->getHeaders()['content-type'][0];
        // $contentType = 'application/json'
        $content = $response->getContent();
        // $content = '{"id":521583, "name":"symfony-docs", ...}'
        $content = $response->toArray();
        // $content = ['id' => 521583, 'name' => 'symfony-docs', ...]

        return $content;
    }


    /**
     * @Route("/api", name="api")
     */
    public function index()
    {
        $data=$this->fetchUsers();

        return $this->render('api/index.html.twig', [
            'controller_name' => 'ApiController',
            'users' => $data['data'],
        ]);
    }

    /**
     * @Route("/new", name="user_new", methods={"GET","POST"})
     */
    public function Usernew(Request $request): Response
    {

        $form = $this->createFormBuilder()
            ->add('name')
            ->add('email')
            ->add('gender', ChoiceType::class,[
                'choices' =>[
                    'Male' => 'Male',
                    'Female' => 'Female',
                ]
            ])
            ->add('status',ChoiceType::class,[
                'choices' =>[
                    'Active'=>'Active',
                    'Inactive'=>'Inactive',
                ]
            ])
            ->add('submit', SubmitType::class, [
                'attr'=>[
                    'class'=>'btn btn-success float-rignt'
                ]])
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data=$form->getData();
            dump($data);
            $response= $this->client->request(
                'POST',
                'https://gorest.co.in/public-api/users', [
                'auth_bearer' => 'cd77a775e1122771b45afa911a414c5a12f04dfcc7766d70ee1717878623c592',
                // defining data using an array of parameters
                'body' => ['name' => $data['name'],
                    'email'=>$data['email'],
                    'gender'=>$data['gender'],
                    'status'=>$data['status'],
                ],

            ]);


            return $this->redirectToRoute('api');
        }

        return $this->render('api/new.html.twig', [
            //'user' =>$user,
            'form' => $form->createView(),
        ]);
    }
    /**
     * @Route("/{id}/edit", name="user_edit", methods={"GET","POST"})
     */
    public function edit(Request $request): Response
    {
        $id=$request->get('id');
        $response = $this->client->request(
            'GET',
            'https://gorest.co.in/public-api/users/'.$id
        );
        $statusCode = $response->getStatusCode();
        // $statusCode = 200
        $contentType = $response->getHeaders()['content-type'][0];
        // $contentType = 'application/json'
        $content = $response->getContent();
        // $content = '{"id":521583, "name":"symfony-docs", ...}'
        $content = $response->toArray();
        // $content = ['id' => 521583, 'name' => 'symfony-docs', ...]

        $form = $this->createFormBuilder($content['data'])
            ->add('name')
            ->add('email')
            ->add('gender', ChoiceType::class,[
                'choices' =>[
                    'Male' => 'Male',
                    'Female' => 'Female',
                ]
            ])
            ->add('status',ChoiceType::class,[
                'choices' =>[
                    'Active'=>'Active',
                    'Inactive'=>'Inactive',
                ]
            ])
            ->add('submit', SubmitType::class, [
                'attr'=>[
                    'class'=>'btn btn-success float-rignt'
                ]])
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data=$form->getData();
            dump($data);
            $response= $this->client->request(
                'PUT',
                'https://gorest.co.in/public-api/users/'.$id, [
                'auth_bearer' => 'cd77a775e1122771b45afa911a414c5a12f04dfcc7766d70ee1717878623c592',
                // defining data using an array of parameters
                'body' => ['name' => $data['name'],
                    'email'=>$data['email'],
                    'gender'=>$data['gender'],
                    'status'=>$data['status'],

                ],

            ]);

            return $this->redirectToRoute('api');
        }

        return $this->render('api/edit.html.twig', [
            'user' =>$content,
            'form' => $form->createView(),
        ]);
    }
    /**
     * @Route("/{id}", name="user_delete", methods={"GET"})
     */
    public function delete(Request $request): Response
    {
        $id=$request->get('id');
        $response= $this->client->request(
            'DELETE',
            'https://gorest.co.in/public-api/users/'.$id, [
            'auth_bearer' => 'cd77a775e1122771b45afa911a414c5a12f04dfcc7766d70ee1717878623c592',
            // defining data using an array of parameters


        ]);

        return $this->redirectToRoute('api');
    }
}
