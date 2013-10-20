<?php

namespace Serie\SerieBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * SerieRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class SerieRepository extends EntityRepository
{

	/**
	 * Retourne la requête qui permet de paginer les séries dans l'admin
	 * @return Request
	 */
	public function getAllShowsQuery()
	{
		return $this->_em
                    ->createQueryBuilder('s')
                    ->select('s')
                    ->from('SerieSerieBundle:Serie', 's')
                    ->getQuery();
	}

	/**
	 * Retourne le show en fonction de son alias
	 * @param  string $alias alias de la série
	 * @return Serie
	 */
	public function getSerieByAlias($alias)
	{
		return $this->_em
					->createQueryBuilder('s')
					->select('s, a, g')
					->from('SerieSerieBundle:Serie', 's')
					->leftjoin('s.actors', 'a')
					->leftjoin('s.genres', 'g')
					->where('s.alias = :alias')
					->setParameter('alias', $alias)
					->getQuery()
					->getSingleResult();
	}

	/**
	 * Retourne un tableau de serie classés par ordre d'ajout descendant
	 * @param  int $limit Nombre de séries à retourner
	 * @return ArrayCollection de série
	 */
	public function getLastShows($limit)
	{
		return $this->_em
					->createQueryBuilder('s')
					->select('s')
					->from('SerieSerieBundle:Serie', 's')
					->orderby('s.dateAdded','desc')
					->setMaxResults($limit)
					->getQuery()
					->getResult();

	}
}