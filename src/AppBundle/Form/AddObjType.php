<?php

namespace AppBundle\Form;

use AppBundle\Entity\Object;
use Symfony\Component\Form\AbstractType;
use AppBundle\Entity\Client;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddObjType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('name', TextType::class, array('label'=>'Nom ', 'required' => true,'data' => null))
            ->add('quantity', IntegerType::class, array('label'=>'Quantité ', 'required' => true,'data' => null))
            ->add('utilisation', IntegerType::class, array('label'=>'Utilisation (heures par jour) ', 'required' => true,'data' => null))
            ->add('power', IntegerType::class, array('label'=>'Puissance (Watt)', 'required' => true,'data' => null))

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Object::class,
        ));
    }
}