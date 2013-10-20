<?php

namespace Serie\SerieBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * GenreRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class GenreRepository extends EntityRepository
{

	public function getAllGenresQuery()
	{
		return $this->_em
                    ->createQueryBuilder('g')
                    ->select('g')
                    ->from('SerieSerieBundle:Genre', 'g')
                    ->getQuery();
	}
}
