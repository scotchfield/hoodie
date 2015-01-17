<?php

class TestCharacter extends PHPUnit_Framework_TestCase {

    public function setUp() {
        $ag = new ArcadiaGame();

        $ag->set_component( 'common', new ArcadiaCommon() );
        $ag->set_component( 'db',
            new ArcadiaDb( DB_ADDRESS, DB_NAME, DB_USER, DB_PASSWORD ) );
        $ag->set_component( 'user', new ArcadiaUser( $ag ) );

        $this->ag = $ag;
        $this->hq = new Hoodiequest( $ag );
    }

    public function tearDown() {
        unset( $this->hq );
        unset( $this->ag );
    }

    /**
     * @covers HQCharacter::__construct
     */
    public function test_character_new() {
        $this->assertNotFalse( $this->ag->c( 'hq_character' ) );
    }

}
