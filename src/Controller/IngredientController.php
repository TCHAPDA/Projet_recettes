<?php

namespace App\Controller;

use App\Repository\IngredientRepository;
use App\Form\IngredientFormType;
use App\Form\IngredientFormType_v3;
use App\Entity\Ingredient;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;


final class IngredientController extends AbstractController
{
    #[Route('/ingredient', name: 'app_ingredient')]
    public function index(IngredientRepository $repository): Response
    {

        // On récupère tous les ingrédients en BDD grâce au repository
        $ingredients = $repository->findAll();

        return $this->render('ingredient/index.html.twig', [
            'ingredients' => $ingredients,
        ]);
    }

    #[Route('/ingredient/greater_than_100', name: 'app_ingredient_greater_than_100')]
    public function index_only_greater_than_100(IngredientRepository $repository): Response
    {
        
        $ingredients = $repository->findAll();
        $ingredients_100 = [];
        foreach ($ingredients as $ingredient) {
            if ($ingredient->getPrice() >100) {
                $ingredients_100 []= $ingredient;
            }
        }
        return $this->render('ingredient/index.html.twig', [
        'ingredients' => $ingredients_100,]);
    }

    #[Route('/ingredient/greater_than_100_v2', name: 'app_ingredient_greater_than_100_v2')]
    public function index_only_greater_than_100_v2(IngredientRepository $repository): Response
    {
        $ingredients = $repository->findAll();
        $ingredient_collection = new ArrayCollection($ingredients);

        $filterIngredientCollection = $ingredient_collection->filter(
            fn(Ingredient $ingredient) => $ingredient->getPrice() > 100
        );

        return $this->render('ingredient/index.html.twig', [
            'ingredients' => $filterIngredientCollection,]);
    }

    #[Route('/ingredient/greater_than_100_v3', name: 'app_ingredient_greater_than_100_v3')]
    public function index_only_greater_than_100_v3(IngredientRepository $repository): Response
    {
        $ingredientCollection = new ArrayCollection($repository->findAll());

        // critere de selction
        $criteria = Criteria::create()->where(Criteria::expr()->gt('price', 100));
        
        $ingredient_than_100 = $ingredientCollection->matching($criteria);

        return $this->render('ingredient/index.html.twig', [
            'ingredients' => $ingredient_than_100,]);
    }

    #[Route('/ingredient/greater_than_100_v4', name: 'app_ingredient_greater_than_100_v4')]
    public function index_only_greater_than_100_v4(IngredientRepository $repository): Response
    {
        //on cree notre critere puis on match directement avec $repository de IngredientRepository
        $criteria = Criteria::create()->where(Criteria::expr()->gt('price', 100));
        $ingredients_100 = $repository->matching($criteria);


        return $this->render('ingredient/index.html.twig', [
            'ingredients' => $ingredients_100,]);
    }

    #[Route('/ingredient/create', name: 'ingredient.create', methods: ['GET'])]
    public function create(): response
    {
        //instanciation de l'entité
        $ingredient = new Ingredient();

        // Valeurs par défaut injectées dans l'objet
        $ingredient->setName('Poivre');
        $ingredient->setPrice(10);

        //construction du formulaire via formbuilder
        $crea_form = $this->createFormBuilder($ingredient)
            ->setAction($this->generateUrl('ingredient.store'))
            ->setMethod('POST')
            ->add('name', TextType::class, [
                'label' => 'Nom de l\'ingredient'
            ])
            ->add('price', MoneyType::class, [
                'label' => 'prix',
                'currency' => 'EUR'
            ])
            // ->add('submit', SubmitType::class, [
            //     'label' => 'Enregistrer'
            // ])
            ->getForm();
        
        // retour de 
        return $this->render('ingredient/create.html.twig', [
            'form' => $crea_form->createView(),
        ]);

    }

    #[Route('/ingredient/store', name: 'ingredient.store', methods: ['POST'])]
    public function store(Request $request, EntityManagerInterface $entity_manager): Response
    {
        $ingredient = new Ingredient();
        // on recree le formulaire
        // $crea_form = $this->createFormBuilder($ingredient)
        //     ->setAction($this->generateUrl('ingredient.store'))
        //     ->setMethod('POST')
        //     ->add('name', TextType::class, [
        //         'label' => 'Nom de l\'ingredient'
        //     ])
        //     ->add('price', MoneyType::class, [
        //         'label' => 'prix',
        //         'currency' => 'EUR'
        //     ])
        //     ->getForm();

        //on lie les donnees de la requette post au formunaile
        $data = $request->request->all();

        // Si le formulaire est wrappé par FormBuilder (souvent sous la clé 'form'),
        // on extrait le tableau sous-jacent, sinon on prend $data directement.
        $formData = $data['form'] ?? $data;

        // 14. Affectation des données récupérées aux propriétés de l'objet Ingredient
        if (isset($formData['name'])) {
            $ingredient->setName($formData['name']);
        }

        if (isset($formData['price'])) {
            $ingredient->setPrice((float) $formData['price']);
        }

        // 15. Rendre persistant l'objet en mémoire
        $entity_manager->persist($ingredient);

        // 16. Enregistrer la transaction en base de données
        $entity_manager->flush();

        // 17. Redirection de l'utilisateur vers la liste des ingrédients
        return $this->redirectToRoute('app_ingredient');
    }

        // 3. On vérifie si le formulaire a été soumis et est valide
        // if ($crea_form->isSubmitted() && $crea_form->isValid()) {
        //     $entityManager->persist($ingredient);
        //     $entityManager->flush();

        //     // Redirection vers la liste des ingrédients
        //     return $this->redirectToRoute('ingredient.index');
        // }

            // En cas de problème ou si le formulaire n'est pas valide, on réaffiche le formulaire avec les erreurs
        // return $this->render('ingredient/create.html.twig', [
        //     'form' => $crea_form->createView(),
        // ]);
        // }

    // deuxieme methode de la mission 13
    //cette route gere a la fois l'affichage et la soumission des données
    #[Route('/ingredient/create_and_store', name: 'ingredient.create_and_store', methods: ['GET','POST'])]
    public function create_and_store(Request $request, EntityManagerInterface $entity_manager): Response
    {
        //instanciation de l'objet ingredient
        $ingredient = new Ingredient();

        // 14. Date de création automatique (exclue du formulaire HTML)
        // Si tu as un attribut createdAt dans ton entité Ingredient :
        if (method_exists($ingredient, 'setCreatedAt')) {
            $ingredient->setCreatedAt(new \DateTimeImmutable());
        }
        //construction du form avec les element nom et price des ingredient
        $crea_form = $this->createFormBuilder($ingredient)
            ->add('name', TextType::class, [
                'label' => 'Nom de l\'ingrédient'
            ])
            ->add('price', MoneyType::class, [
                'label' => 'Prix'
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Enregistrer'
            ])
            ->getForm();
        
        //
        $crea_form->handleRequest($request);  
        //verif de la soumission et de la validation du formulaire
        if($crea_form->isSubmitted() && $crea_form->isValid()){
            //recup des donnees du form
            $data = $crea_form->getData();
            // permet d'inspecter data lors du test
            //dd($data);
            // Affectation explicite (grâce au FormBuilder lié à l'entité, 
            // $ingredient est déjà hydraté par Symfony, mais on peut réassigner si besoin) :
            $ingredient->setName($data->getName());
            $ingredient->setPrice($data->getPrice());

            // Persistance et enregistrement en BDD
            $entity_manager->persist($ingredient);
            $entity_manager->flush();

            // Redirection vers la liste des ingrédients
            return $this->redirectToRoute('app_ingredient');
        }
        // Si le formulaire n'a pas été soumis ou n'est pas valide (phase GET)
        return $this->render('ingredient/create_and_store.html.twig', [
            'form' => $crea_form->createView(),
        ]);
    }

    //methode 3
    #[Route('/ingredient/create_and_store_v2', name: 'ingredient.create_and_store_v2', methods: ['GET','POST'])]
    public function create_and_store_v2(Request $request, EntityManagerInterface $entity_manager): Response
    {
        //instanciation de l'objet ingredient
        $ingredient = new Ingredient();

        // 14. Date de création automatique (exclue du formulaire HTML)
        // Si tu as un attribut createdAt dans ton entité Ingredient :
        if (method_exists($ingredient, 'setCreatedAt')) {
            $ingredient->setCreatedAt(new \DateTimeImmutable());
        }
        //construction du form avec les element nom et price des ingredient
        $crea_form = $this->createForm(IngredientFormType::class, $ingredient);
        
        //
        $crea_form->handleRequest($request);  
        //verif de la soumission et de la validation du formulaire
        if($crea_form->isSubmitted() && $crea_form->isValid()){
            //recup des donnees du form
            $data = $crea_form->getData();
            // permet d'inspecter data lors du test
            //dd($data);
            // Affectation explicite (grâce au FormBuilder lié à l'entité, 
            // $ingredient est déjà hydraté par Symfony, mais on peut réassigner si besoin) :
            // $ingredient->setName($data->getName());
            // $ingredient->setPrice($data->getPrice());

            // Persistance et enregistrement en BDD
            $entity_manager->persist($ingredient);
            $entity_manager->flush();

            // 9. Ajout du message flash
            $this->addFlash('success', 'Votre ingrédient a bien été créé avec succès !');

            // Redirection vers la liste des ingrédients
            return $this->redirectToRoute('app_ingredient');
        }
        // Si le formulaire n'a pas été soumis ou n'est pas valide (phase GET)
        return $this->render('ingredient/create_v2.html.twig', [
            'form' => $crea_form->createView(),
        ]);
    }

    //mission 15
    #[Route('/ingredient/create_and_store_v3', name: 'ingredient.create_and_store_v3', methods: ['GET','POST'])]
    public function create_and_store_v3(Request $request, EntityManagerInterface $entity_manager): Response
    {
        //instanciation de l'objet ingredient
        $ingredient = new Ingredient();

        // 14. Date de création automatique (exclue du formulaire HTML)
        // Si tu as un attribut createdAt dans ton entité Ingredient :
        if (method_exists($ingredient, 'setCreatedAt')) {
            $ingredient->setCreatedAt(new \DateTimeImmutable());
        }
        //construction du form avec les element nom et price des ingredient
        $crea_form = $this->createForm(IngredientFormType_v3::class, $ingredient, [
            'submit_label' => 'Creer mon ingredient',
        ]);
        
        //
        $crea_form->handleRequest($request);  
        //verif de la soumission et de la validation du formulaire
        if($crea_form->isSubmitted() && $crea_form->isValid()){
            //recup des donnees du form
            $data = $crea_form->getData();
            // permet d'inspecter data lors du test
            //dd($data);

            // Persistance et enregistrement en BDD
            $entity_manager->persist($ingredient);
            $entity_manager->flush();

            // 9. Ajout du message flash
            $this->addFlash('success', 'Votre ingrédient a bien été créé avec succès !');

            // Redirection vers la liste des ingrédients
            return $this->redirectToRoute('app_ingredient');
        }
        // Si le formulaire n'a pas été soumis ou n'est pas valide (phase GET)
        return $this->render('ingredient/create_v3.html.twig', [
            'crea_form' => $crea_form->createView(),
        ]);
    }

    //mission 16 le CRUD
    #[Route('/ingredient/edit/{id}', name: 'ingredient.edit', methods: ['GET', 'PUT', 'POST'])]
    public function edit(
        int $id,
        IngredientRepository $repository,
        Request $request,
        EntityManagerInterface $entity_manager
    ): Response {
        //recup de l'ingredient en BD
        $ingredient= $repository->find($id);

        if (!$ingredient) {
            throw $this->createNotFoundException('Ingrédient non trouvé.');
        }
        //creation du formulaire
        $form = $this->createForm(IngredientFormType_v3::class, $ingredient, [
            'method' => 'PUT',
            'submit_label' => 'Enregistrer les modifications',
        ]);
        //traitement de la requette
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            // 9. Sauvegarde (flush suffit car l'entité est déjà suivie par Doctrine)
            $entity_manager->flush();

            // 10. Message flash
            $this->addFlash('success', 'Votre ingrédient a été modifié avec succès !');

            // Redirection
            return $this->redirectToRoute('app_ingredient');
        }

        return $this->render('ingredient/create_v3.html.twig', [
            'crea_form' => $form->createView(),
        ]);
        
    }

    //mission 17 supprsion
    #[Route('/ingredient/delete/{id}', name: 'ingredient.delete', methods: ['DELETE'])]
    public function delete (
        int $id,
        IngredientRepository $repository,
        EntityManagerInterface $manager
    ): Response{
        // retrouver l'ingredient
        $ingredient = $repository->find($id);

        if (!$ingredient) {
            throw $this->createNotFoundException('Ingrédient non trouvé.');
        }

        //suppresion proprement dite
        $manager->remove($ingredient);
        $manager->flush();

        //message flash de succes
        $this->addFlash('success', 'Votre ingrédient a été supprimé avec succès !');

        //redirection vers la liste des ingredients
        return $this->redirectToRoute('app_ingredient');
    }
    //mission 18 methode 2
    //pour tester les methode dql remplacer sql par dql lors des appel de methode
    #[Route('/ingredient/sql', name: 'ingredient.index.sql', methods: ['GET'])]
    public function index_sql(IngredientRepository $repository): Response
    {
        //appel de notre methode personalisé
        $ingredients = $repository->findAll_sql();

        return $this->render('ingredient/index.html.twig', [
            'ingredients' => $ingredients,
        ]);
    }

    #[Route('/ingredient/tomatesql', name: 'ingredient.tomate.index.sql', methods: ['GET'])]
    public function index_tomate_sql(IngredientRepository $repository): Response
    {
        //appel de notre methode personalisé
        $ingredients = $repository->find_ingredient_tomato_sql();

        return $this->render('ingredient/index.html.twig', [
            'ingredients' => $ingredients,
        ]);
    }

    #[Route('/ingredient/price-sql', name: 'ingredient.price_sql', methods: ['GET'])]
    public function index_ingredient_by_price_sql(IngredientRepository $repository): Response
    {
        // On fixe un prix max de test (ex: 5.00 €)
        $ingredients = $repository->find_ingredient_by_price_sql(51.00);

        return $this->render('ingredient/index.html.twig', [
            'ingredients' => $ingredients,
        ]);
}

}
