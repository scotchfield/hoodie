<?php

class TestCombat extends PHPUnit_Framework_TestCase {

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

    private function get_character() {
        $character = array();

        $key_obj = array(
            'character_name', 'health', 'stamina', 'stamina_max', 'gold',
            'xp', 'x', 'y', 'wins', 'losses', 'max_damage_done',
            'max_damage_taken', 'ability', 'id',
        );

        foreach ( $key_obj as $key ) {
            $character[ $key ] = 'test';
        }

        return $character;
    }

    /**
     * @covers HQCombat::__construct
     */
    public function test_combat_new() {
        $this->assertNotFalse( $this->ag->c( 'hq_combat' ) );
    }

    /**
     * @covers HQCombat::combat_content
     */
    public function test_combat_content() {
        $component = new HQCombat( $this->ag );

        ob_start();
        $result = $component->combat_content();
        ob_end_clean();

        $this->assertFalse( $result );
    }

    /**
     * @covers HQCombat::combat_content
     * @covers HQCombat::do_combat
     */
    public function test_combat_content_no_stamina() {
        $component = new HQCombat( $this->ag );

        $this->ag->char = $this->get_character();

        ob_start();
        $result = $component->combat_content();
        ob_end_clean();

        $this->assertTrue( $result );
    }


}