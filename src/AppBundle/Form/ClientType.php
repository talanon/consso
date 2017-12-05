<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use AppBundle\Entity\Client;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ClientType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            //->add('region')
            /**
             * QUESTION LOGEMENT
             */

            ->add('logement', ChoiceType::class, array('label'=>'Quel est le type de votre logement ', 'choices' => array("Un appartement"=>"appartement", "Une maison"=>"maison"),'required' => true,'data' => null))
            ->add('age', ChoiceType::class, array('label'=>'Depuis quand date les dernières rénovations de votre logement','choices' => array("- de 10 ans"=>0, "10 à 15 ans"=>10, "16 à 30 ans"=>16, "+ de 30 ans"=>30),'required' => true,'data' => null))
            ->add('chauffage', ChoiceType::class, array('label'=>'Chauffez-vous votre logement avec des radiateurs électriques ?','choices' => array("Oui"=>true, "Non"=>false),'expanded'=>true,'multiple'=>false,'required' => true,'data' => null))
            ->add('surface', IntegerType::class, array('label'=>'Votre surface ? (en m²)','required' => true,'data' => null))
            ->add('etage', ChoiceType::class, array('label'=>'Si votre bien est un appartement, il est :','choices' => array("au dernier étage"=>0, "un étage intermédiaire"=>1, "au rez-de-chaussée"=>2),'required' => true,'data' => null))


            /**
             * QUESTION UTILISATEUR
             */

            ->add('foyer',  IntegerType::class, array('label'=>'De combien de personnes est composé votre foyer ?','required' => true,'data' => null))
            ->add('piece', IntegerType::class, array('label'=>'De combien de pièces est composé votre foyer ?','required' => true,'data' => null))
            ->add('region', IntegerType::class, array('label'=>'Dans quel département se situe votre appartement ? (2 premiers chiffres)','required' => false,'data' => null))

            /**
             * QUESTION EQUIPEMENT
             */


            ->add('ampoule', ChoiceType::class, array('label'=>'Avez-vous des ampoules basses consommation ?','choices' => array("Oui"=>true, "Non"=>false),'expanded'=>true,'multiple'=>false,'required' => true,'data' => null))
            ->add('linge', ChoiceType::class, array('label'=>'Avez-vous un lave linge ?','choices' => array("Oui"=>true, "Non"=>false),'expanded'=>true,'multiple'=>false,'required' => true,'data' => null))
            ->add('seche', ChoiceType::class, array('label'=>'Avez-vous un seche linge ?','choices' => array("Oui"=>true, "Non"=>false),'expanded'=>true,'multiple'=>false,'required' => true,'data' => null))
            ->add('plaque', ChoiceType::class, array('label'=>'Avez-vous des plaques éléctriques ?','choices' => array("Oui"=>true, "Non"=>false),'expanded'=>true,'multiple'=>false,'required' => true,'data' => null))
            ->add("four", ChoiceType::class, array('label'=>'Avez-vous un four éléctrique ?','choices' => array("Oui"=>true, "Non"=>false),'expanded'=>true,'multiple'=>false,'required' => true,'data' => null))



            ->add('save', SubmitType::class, array('label' => 'enregistrer'))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Client::class,
        ));
    }
}