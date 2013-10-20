<?php
namespace Serie\SerieBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Serie\AdminBundle\Entity\User;


class UserFrontType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('username', 'text',  array('label' => 'Username *', 'attr' => array('class' => 'form-control')))
                ->add('firstname', 'text', array('label' => 'Firstname *', 'attr' => array('class' => 'form-control')))
                ->add('lastname', 'text', array('label' => 'Lastname *', 'attr' => array('class' => 'form-control')));
        if(!$options['profile']) {
        
        	$builder->add('password', 'repeated', array(
				    'type' => 'password',
				    'invalid_message' => 'The password fields must match.',
				    'required' => true,
				    'first_options'  => array('label' => 'Password', 'attr' => array('class' => 'form-control')),
				    'second_options' => array('label' => 'Confirm', 'attr' => array('class' => 'form-control')),
				));
        } 

        		
        $builder->add('email', 'email', array('label' => 'E-mail *', 'attr' => array('class' => 'form-control')))
                ->add('file', 'file', array('label' => 'Avatar', 'required' => false, 'attr' => array('class' => 'form-control')))
                ->add('cancel', 'button', array('attr' => array('class' => 'btn btn-cancel')))
				->add('apply', 'submit', array('attr' => array('class' => 'btn btn-default')));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'profile' => false
        ));
    }

    public function getName()
    {
    	return 'userFront';
    }
}