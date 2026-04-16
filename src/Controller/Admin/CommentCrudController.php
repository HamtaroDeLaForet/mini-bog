<?php

namespace App\Controller\Admin;

use App\Entity\Comment;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
class CommentCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Comment::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextareaField::new('content', 'Contenu'),
            AssociationField::new('user', 'Utilisateur'),
            AssociationField::new('post', 'Article'),
            BooleanField::new('isApprouved', 'Approuvé'),
            DateTimeField::new('createdAt', 'Créé le')->hideOnForm(),
        ];
    }
}
