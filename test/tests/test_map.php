<?php

class TestMap extends PHPUnit_Framework_TestCase {

    public function setUp() {
        $ag = new ArcadiaGame();

        $ag->set_component( 'common', new ArcadiaCommon() );
        $ag->set_component( 'db',
            new ArcadiaDb( DB_ADDRESS, DB_NAME, DB_USER, DB_PASSWORD ) );
        $ag->set_component( 'user', new ArcadiaUser( $ag ) );

        $this->ag = $ag;
        $this->ag->hq = new Hoodiequest( $ag );
    }

    public function tearDown() {
        $this->ag->c( 'db' )->execute( 'DELETE FROM characters' );
        $this->ag->c( 'db' )->execute( 'DELETE FROM character_meta' );
        $this->ag->c( 'db' )->execute( 'DELETE FROM game_meta' );

        $this->ag->clear_args();

        unset( $this->ag->hq );
        unset( $this->ag );
    }

    /**
     * @covers HQMap::__construct
     */
    public function test_map_new() {
        $this->assertNotFalse( $this->ag->c( 'hq_map' ) );
    }

}
