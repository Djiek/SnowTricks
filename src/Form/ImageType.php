<?php

namespace App\Form;

use App\Entity\Images;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class ImageType extends AbstractType
{
public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
           // ->add('link', FileType::class);
            ->add('file', FileType::class, [
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/jpg',
                            'image/png',

                        ],
                        'mimeTypesMessage' => 'Le format de l\'image n\'est pas valide.(png,jpeg et jpg accepté)',
                    ])
                ],
                // 'label_attr' => ['class' => 'custom-file-label', 'type' => 'file'],
                'attr' => array('placeholder' => 'Selectionner un fichier' ),
                //  'label_attr' => [
                //     'class' => 'custom-file-label'
                // ]
            ])
         ;
    }

      public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Images::class,
        ]);
    }
}