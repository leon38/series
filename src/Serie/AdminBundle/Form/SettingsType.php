<?php
namespace Serie\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Doctrine\ORM\EntityRepository;
use Serie\SerieBundle\Entity\Genre;

class SettingsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title', 'text', array('attr' => array('class' => 'form-control')))
        		->add('domainName', 'url', array('label' => 'Domain name', 'attr' => array('class' => 'form-control')))
				->add('cancel', 'button', array('attr' => array('class' => 'btn btn-default')))
				->add('apply', 'submit', array('attr' => array('class' => 'btn btn-default')))
	            ->add('save', 'submit', array('attr' => array('class' => 'btn btn-default')));
    }

    public function getName()
    {
        return 'settings';
    }
}