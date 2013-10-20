<?php
namespace Serie\SerieBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class ActorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('firstname', 'text', array('attr' => array('class' => 'form-control')))
        		->add('lastname', 'text', array('attr' => array('class' => 'form-control')))
                ->add('alias', 'text', array('attr' => array('class' => 'form-control')))
                ->add('birthdayDate', 'birthday')
                ->add('resume', 'textarea', array('attr' => array('class' => 'form-control')))
                ->add('file', 'file')
        		->add('cancel', 'button', array('attr' => array('class' => 'btn btn-default')))
				->add('apply', 'submit', array('attr' => array('class' => 'btn btn-default')))
	            ->add('save', 'submit', array('attr' => array('class' => 'btn btn-default')));
    }

    public function getName()
    {
        return 'genre';
    }
}