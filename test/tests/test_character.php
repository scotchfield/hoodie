<?php

class TestCharacter extends PHPUnit_Framework_TestCase {

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
     * @covers HQCharacter::__construct
     */
    public function test_character_new() {
        $this->assertNotFalse( $this->ag->c( 'hq_character' ) );
    }

    /**
     * @covers HQCharacter::profile_content
     * @covers HQCharacter::print_character
     */
    public function test_character_profile_content() {
        $component = new HQCharacter( $this->ag );

        ob_start();
        $result = $component->profile_content();
        ob_end_clean();

        $this->assertTrue( $result );
    }

    /**
     * @covers HQCharacter::char_content
     * @covers HQCharacter::print_character
     */
    public function test_character_char_content_no_id() {
        $component = new HQCharacter( $this->ag );

        ob_start();
        $result = $component->char_content();
        ob_end_clean();

        $this->assertFalse( $result );
    }

    /**
     * @covers HQCharacter::char_content
     * @covers HQCharacter::print_character
     */
    public function test_character_char_content_no_char() {
        $component = new HQCharacter( $this->ag );

        $this->ag->set_arg( 'id', 1 );

        ob_start();
        $result = $component->char_content();
        ob_end_clean();

        $this->assertFalse( $result );
    }

    /**
     * @covers HQCharacter::char_content
     * @covers HQCharacter::print_character
     */
    public function test_character_char_content() {
        $component = new HQCharacter( $this->ag );

        $this->ag->c( 'db' )->execute(
            'INSERT INTO characters ( id, user_id, character_name ) VALUES ' .
                '( 1, 1, "test" )' );
        $this->ag->set_arg( 'id', 1 );

        ob_start();
        $result = $component->char_content();
        ob_end_clean();

        $this->assertTrue( $result );
    }

    /**
     * @covers HQCharacter::print_character
     */
    public function test_character_print_character() {
        $component = new HQCharacter( $this->ag );

        $key_obj = array(
            'character_name', 'health', 'stamina', 'stamina_max', 'gold',
            'xp', 'x', 'y', 'wins', 'losses', 'max_damage_done',
            'max_damage_taken',
        );
        $character = array(
            'stats' => array( 'test' => 'test', 'hoodie' => '' ),
        );
        foreach ( $key_obj as $key ) {
            $character[ $key ] = 'test';
        }

        ob_start();
        $result = $component->print_character( $character );
        ob_end_clean();

        $this->assertTrue( $result );
    }

    /**
     * @covers HQCharacter::achievements_content
     */
    public function test_character_achievements_content() {
        $component = new HQCharacter( $this->ag );

        ob_start();
        $result = $component->achievements_content();
        ob_end_clean();

        $this->assertTrue( $result );
    }

}
