<?php

namespace Tests\Services\Grapher\Graph\Access\Api;

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
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for
 * more details.
 *
 * You should have received a copy of the GNU General Public License v2.0
 * along with IXP Manager.  If not, see:
 *
 * http://www.gnu.org/licenses/gpl-2.0.html
 */


use Config;

use Tests\Services\Grapher\Graph\Access\Access;


/**
 * Test access restrictions for PhysicalInterface graphs
 *
 * Class PhysicalInterfaceAccessTest
 * @package Tests\Services\Grapher\Graph
 */
class PhysicalInterfaceWebAccessTest extends Access
{

    /**
     * Test access restrictions for public web access
     * @return void
     */
    public function testApiPublicAccess()
    {
        // this should be the default
        $response = $this->get('/statistics/member-drilldown/pi/1');
        $response->assertStatus(302);

        // force the default
        Config::set( 'grapher.access.customer', 'own_graphs_only' );
        $response = $this->get('/statistics/member-drilldown/pi/1');
        $response->assertStatus(302);

        // force public access
        Config::set( 'grapher.access.customer', 0 );
        $response = $this->get('/statistics/member-drilldown/pi/1');
        $response->assertStatus(200);
    }

    /**
     * Test access restrictions for verious non-public access settings
     * @return void
     */
    public function testWebNonPublicAccess()
    {
        Config::set( 'grapher.access.customer', '1' );
        $response = $this->get('/statistics/member-drilldown/pi/1');
        $response->assertStatus(302);

        Config::set( 'grapher.access.customer', '2' );
        $response = $this->get('/statistics/member-drilldown/pi/1');
        $response->assertStatus(302);

        Config::set( 'grapher.access.customer', '3' );
        $response = $this->get('/statistics/member-drilldown/pi/1');
        $response->assertStatus(302);

        Config::set( 'grapher.access.customer', 'blah' );
        $response = $this->get('/statistics/member-drilldown/pi/1');
        $response->assertStatus(302);

        Config::set( 'grapher.access.customer', null );
        $response = $this->get('/statistics/member-drilldown/pi/1');
        $response->assertStatus(302);
    }

    /**
     * Test access restrictions requiring own_graphs_only logged in user
     * @return void
     */
    public function testWebOwnUserCustUserAccess()
    {
        Config::set( 'grapher.access.customer', 'own_graphs_only' );
        $response = $this->get('/statistics/member-drilldown/pi/1');
        $response->assertStatus(302);

        // customer user
        $response = $this->actingAs( $this->getCustUser( 'hecustuser' ) )->get('/statistics/member-drilldown/pi/1');
        $response->assertStatus(200);

        // customer admin user
        $response = $this->actingAs( $this->getCustAdminUser( 'hecustadmin' ) )->get('/statistics/member-drilldown/pi/1');
        $response->assertStatus(200);

        // non-customer user
        $response =$this->actingAs( $this->getCustUser( 'imcustuser' ) )->get('/statistics/member-drilldown/pi/1');
        $response->assertStatus(302);

        // non-customer admin user
        $response = $this->actingAs( $this->getCustAdminUser( 'imcustadmin' ) )->get('/statistics/member-drilldown/pi/1');
        $response->assertStatus(302);

        // superadmin
        $response = $this->actingAs( $this->getSuperUser() )->get('/statistics/member-drilldown/pi/1');
        $response->assertStatus(200);
    }


    /**
     * Test access restrictions requiring minimum logged in user of CustUser (privs=1) for web access
     * @return void
     */
    public function testWebCustUserAccess()
    {
        Config::set( 'grapher.access.customer', '1' );
        $response = $this->get('/statistics/member-drilldown/pi/1');
        $response->assertStatus(302);

        $response = $this->actingAs( $this->getCustUser() )->get('/statistics/member-drilldown/pi/1');
        $response->assertStatus(200);

        $response = $this->actingAs( $this->getCustAdminUser() )->get('/statistics/member-drilldown/pi/1');
        $response->assertStatus(200);

        $response = $this->actingAs( $this->getSuperUser() )->get('/statistics/member-drilldown/pi/1');
        $response->assertStatus(200);
    }


    /**
     * Test access restrictions requiring minimum logged in user of CustAdmin (privs=2) for web access
     * @return void
     */
    public function testWebCustAdminAccess()
    {
        Config::set( 'grapher.access.customer', '2' );
        $response = $this->get('/statistics/member-drilldown/pi/1');
        $response->assertStatus(302);

        $response = $this->actingAs( $this->getCustUser() )->get('/statistics/member-drilldown/pi/1');
        $response->assertStatus(302);

        $response = $this->actingAs( $this->getCustAdminUser() )->get('/statistics/member-drilldown/pi/1');
        $response->assertStatus(200);

        $response = $this->actingAs( $this->getSuperUser() )->get('/statistics/member-drilldown/pi/1');
        $response->assertStatus(200);
    }

    /**
     * Test access restrictions requiring logged in superuser (privs=3) for web access
     * @return void
     */
    public function testWebSuperuserAccess()
    {
        Config::set( 'grapher.access.customer', '3' );
        $response = $this->get('/statistics/member-drilldown/pi/1');
        $response->assertStatus(302);

        $response = $this->actingAs( $this->getCustUser() )->get('/statistics/member-drilldown/pi/1');
        $response->assertStatus(302);

        $response = $this->actingAs( $this->getCustAdminUser() )->get('/statistics/member-drilldown/pi/1');
        $response->assertStatus(302);

        $response = $this->actingAs( $this->getSuperUser() )->get('/statistics/member-drilldown/pi/1');
        $response->assertStatus(200);
    }

}
