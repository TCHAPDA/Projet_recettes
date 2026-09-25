<?php

namespace App\Controller;

use App\Repository\IngredientRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use App\Entity\Ingredient;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Doctrine\ORM\EntityManagerInterface;
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
}
