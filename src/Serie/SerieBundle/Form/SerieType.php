<?php
namespace Serie\SerieBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Doctrine\ORM\EntityRepository;
use Serie\SerieBundle\Entity\Genre;

class SerieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title', 'text', array('attr' => array('class' => 'form-control')))
        		->add('alias', 'text', array('attr' => array('class' => 'form-control')))
	            ->add('nbSeasons', 'text', array('attr' => array('class' => 'form-control col-lg-2')))
	            ->add('nbEpisodes', 'text', array('attr' => array('class' => 'form-control col-lg-2')))
	            ->add('startDate', 'birthday')
				->add('endDate', 'birthday')
				->add('channel', 'text', array('attr' => array('class' => 'form-control')))
				->add('nationality', 'text', array('attr' => array('class' => 'form-control')))
				->add('formatEpisode', 'choice', array('choices' => array('20 min.' => '20 min.', '40 min.' => '40 min.', '52 min.' => '52 min.')))
				->add('synopsis', 'textarea', array('attr' => array('class' => 'form-control')))
				->add('file', 'file', array('required' => false))
				->add('onHome', 'choice', array('choices' => array(1 => 'Yes', 0 => 'No')))
				->add('bigfile', 'file', array('required' => false))
				->add('genres', 'entity', array(
					'class' => 'SerieSerieBundle:Genre', 
					'query_builder' => function(EntityRepository $er) {
        					return $er->createQueryBuilder('g')
            					->orderBy('g.title', 'ASC');
            				}, 
            		'multiple' => 'multiple',
            		'attr' => array('class' => 'form-control')))
				->add('actors', 'entity', array(
					'class' => 'SerieSerieBundle:Actor', 
					'query_builder' => function(EntityRepository $er) {
        					return $er->createQueryBuilder('g')
            					->orderBy('g.lastname', 'ASC');
            				}, 
            		'multiple' => 'multiple',
            		'attr' => array('class' => 'form-control')))
				->add('cancel', 'button', array('attr' => array('class' => 'btn btn-default')))
				->add('apply', 'submit', array('attr' => array('class' => 'btn btn-default')))
	            ->add('save', 'submit', array('attr' => array('class' => 'btn btn-default')));
    }

    public function getName()
    {
        return 'serie';
    }
}