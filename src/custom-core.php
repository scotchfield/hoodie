<?php

require( GAME_CUSTOM_PATH . 'hoodiequest.php' );

require( GAME_CUSTOM_PATH . 'character.php' );
require( GAME_CUSTOM_PATH . 'combat.php' );
require( GAME_CUSTOM_PATH . 'map.php' );
require( GAME_CUSTOM_PATH . 'title.php' );
require( GAME_CUSTOM_PATH . 'tutorial.php' );
require( GAME_CUSTOM_PATH . 'vendor.php' );

global $ag;
$ag->hq = new Hoodiequest( $ag );
