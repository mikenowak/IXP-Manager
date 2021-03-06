<?php

/*
 * Copyright (C) 2009 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee.
 * All Rights Reserved.
 *
 * This file is part of IXP Manager.
 *
 * IXP Manager is free software: you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the Free
 * Software Foundation, version v2.0 of the License.
 *
 * IXP Manager is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GpNU General Public License for
 * more details.
 *
 * You should have received a copy of the GNU General Public License v2.0
 * along with IXP Manager.  If not, see:
 *
 * http://www.gnu.org/licenses/gpl-2.0.html
 */

namespace Repositories;

use Doctrine\ORM\EntityRepository;

use Entities\Location as LocationEntity;

/**
 * Location
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class Location extends EntityRepository
{
    /**
     * Return an array of all locations
     * @return array An array of all locations names with the location id as the key.
     */
    public function getAsArray(): array {
        $locations = [];

        foreach( $this->findBy( [], [ 'name' => 'ASC' ] ) as $l ) {
            /** @var LocationEntity $l */
            $locations[$l->getId()] = [
                'id' => $l->getId(),
                'name' => $l->getName(),
                'shortname' => $l->getShortname(),
                'tag' => $l->getTag()
            ];
        }

        return $locations;
    }

    /**
     * Return of all locations
     * @return array An array of all locations names with the location id as the key.
     */
    public function getNames(): array {
        $locations = [];

        foreach( $this->findBy( [], [ 'name' => 'ASC' ] ) as $l ) {
            /** @var LocationEntity $l */
            $locations[ $l->getId() ] =  $l->getName();
        }

        return $locations;
    }

    /**
     * Get all lcoation (or a particular one) for listing on the frontend CRUD
     *
     * @see \IXP\Http\Controller\Doctrine2Frontend
     *
     *
     * @param \stdClass $feParams
     * @param int|null $id
     * @return array Array of lcoation (as associated arrays) (or single element if `$id` passed)
     */
    public function getAllForFeList( \stdClass $feParams, int $id = null )
    {
        $dql = "SELECT  l.id AS id, 
                        l.name AS name, 
                        l.shortname AS shortname, 
                        l.tag AS tag,
                        l.nocphone AS nocphone, 
                        l.nocemail AS nocemail, 
                        l.address AS address,
                        l.nocfax AS nocfax, 
                        l.officephone AS officephone, 
                        l.officefax AS officefax,
                        l.officeemail AS officeemail, 
                        l.notes AS notes, 
                        l.pdb_facility_id AS pdb_facility_id
                FROM Entities\\Location l
                WHERE 1 = 1";

        if( $id ) {
            $dql .= " AND l.id = " . (int)$id;
        }

        if( isset( $feParams->listOrderBy ) ) {
            $dql .= " ORDER BY " . $feParams->listOrderBy . ' ';
            $dql .= isset( $feParams->listOrderByDir ) ? $feParams->listOrderByDir : 'ASC';
        }

        $query = $this->getEntityManager()->createQuery( $dql );

        return $query->getArrayResult();
    }

}
