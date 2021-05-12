<?php

//https://mysterious-shore-28269.herokuapp.com/recipe/all

namespace App\Controller;

use App\Entity\RecipeList;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/home", name="home" ,methods={"GET"})
     */

    public function index(): Response
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/HomeController.php',
        ]);
    }
    /**
     * @Route("/recipe/add", name="add_new_recipe" ,methods={"POST"})
     */

    public function addRecipe(Request $request) : Response
    {
        $entityManager=$this->getDoctrine()->getManager();
        $data=json_decode($request->getContent(),true);

        if(isset($data['name']) && isset($data['ingredients']) && isset($data['image'])){
            $newName=$data['name'];
            $newIngredeints=$data['ingredients'];
            $newImage=$data['image'];
            $newInstructions=$data['instructions'];

            $newRecipe=new RecipeList();
            $newRecipe->setName($newName);
            $newRecipe->setIngredients($newIngredeints);
            $newRecipe->setImage($newImage);
            $newRecipe->setInstructions($newInstructions);

            $entityManager->persist($newRecipe);
            $entityManager->flush();

            return new Response('Adding new recipe'. $newRecipe->getName());
        }else{
            return new Response('wanting to add new recipe');
        }
    }

    /**
     * @Route("/recipe/all", name="fetch_all_recipe",methods={"GET"})
     */

    public function getAllRecipe(): Response
    {
        $recipes=$this->getDoctrine()->getRepository(RecipeList::class)->findAll();
        $response=[];
        foreach($recipes as $recipe){
            $response[]=array(
                'id'=>$recipe->getId(),
                'name'=>$recipe->getName(),
                'ingredients'=>$recipe->getIngredients(),
                'image'=>$recipe->getImage(),
                'instructions'=>$recipe->getInstructions()
            );
        }
        return $this->json($response);
    }

    /**
     * @Route("/recipe/find/{id}", name="fetch_a_recipe",methods={"GET"})
     */

    public function findRecipe($id): Response
    {
        $recipe=$this->getDoctrine()->getRepository(RecipeList::class)->find($id);
        if(!$recipe){
            throw $this->createNotFoundException(
                'nothing for '. $id
            );
        }else{
            return $this->json([
                'id'=>$recipe->getId(),
                'name'=>$recipe->getName(),
                'ingredients'=>$recipe->getIngredients(),
                'image'=>$recipe->getImage(),
                'instructions'=>$recipe->getInstructions()
            ]);
        }
    }

    /**
     * @Route("/recipe/edit/{id}/{name}", name="edit_a_recipe",methods={"PUT"})
     */

    public function editRecipe($id, $newName, $newIngredeints, $newImage ): Response
    {
        $entityManager=$this->getDoctrine()->getManager();
        $recipe=$this->getDoctrine()->getRepository(RecipeList::class)->find($id);
        if(!$recipe){
            throw $this->createNotFoundException(
                'nothing for '. $id
            );
        }else{

            $recipe->setName($newName);
            $recipe->setIngredients($newIngredeints);
            $recipe->setImage($newImage);
            $entityManager->flush();
        }
    }

    /**
     * @Route("/recipe/remove/{id}", name="remove_a_recipe",methods={"DELETE"})
     */

    public function removeRecipe($id): Response
    {
        $entityManager=$this->getDoctrine()->getManager();
        $recipe=$this->getDoctrine()->getRepository(RecipeList::class)->find($id);
        if(!$recipe){
            throw $this->createNotFoundException(
                'nothing for '. $id
            );
        }else{
            $entityManager->remove($recipe);
            $entityManager->flush();

            return $this->json([
                'message'=>'removed id' .$id
            ]);
        }
    }
}
