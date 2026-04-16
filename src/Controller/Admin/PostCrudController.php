<?php

namespace App\Controller\Admin;

use App\Entity\Post;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;


class PostCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Post::class;
    }

    public function createEntity(string $entityFqcn): Post
    {
        $post = new Post();
        $post->setPublishedAt(new \DateTimeImmutable());
        $post->setUser($this->getUser());
        return $post;
    }
    
    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('title', 'Titre'),
            TextareaField::new('content',"Contenu")->hideOnIndex(),
            AssociationField::new('category', "Catégorie"),
            AssociationField::new('user', "Auteur"),
            DateTimeField::new('createdAt', "Créé le")->hideOnForm(),
            DateTimeField::new('publishedAt', "Publié le"),
            TextField::new('picture', "Image")->hideOnIndex(),
        ];
    }
    
}
