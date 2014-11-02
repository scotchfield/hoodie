<?php

function ag_combat_content() {
    global $game;

    if ( strcmp( 'combat', $game->get_action() ) ) {
       return;
    }

?>
<div class="row text-right">
  <h1 class="page_section">Combat</h1>
</div>
<?php

    ag_do_combat();

?>
<div class="row text-center">
  <h2>(<a href="?action=combat">Click to battle a new foe</a>)</h2>
</div>
<?php
}

function ag_do_combat( $map_obj = FALSE, $foe = FALSE ) {
    global $character;

    $gear_obj = ag_get_gear_obj();

    $stamina = character_meta_float( ag_meta_type_character, AG_STAMINA );

    if ( $stamina < 1 ) {
?>
<div class="row text-center">
  <h2>You're too tired!</h2>
  <p class="lead">Wait until you have at least one point of stamina,
then try to engage in combat once more.</p>
</div>
<?php
        return;
    }

    if ( FALSE == $map_obj ) {
        $map_obj = ag_get_map_state( $character[ 'x' ], $character[ 'y' ] );
    }

    if ( FALSE == $foe ) {
        $foe = ag_get_foe( $map_obj[ 'level' ] );
    }

    echo( '<div class="row"><h2>' .
          ag_st( 'Your foe: ' . $foe[ 'name' ] ) .
          '</h2></div>' );

    $combat = TRUE;
    $round = 0;

    $player_turn = TRUE;
    $abil_all = $character[ 'ability' ] + $foe[ 'ability' ];
    if ( mt_rand( 1, $abil_all ) > $character[ 'ability' ] ) {
        $player_turn = FALSE;
    }

    $health_char = $character[ 'health' ];
    $health_foe = $foe[ 'health' ];

    if ( ! $player_turn ) {
        ag_echo_health( $character, $foe, $health_char, $health_foe );
    }

    while ( $combat ) {

        $round += 1;

        if ( $player_turn ) {
            $attack = ag_get_attack( $character[ 'ability' ], FALSE );

            ag_echo_health( $character, $foe, $health_char, $health_foe );

            echo( '<p class="attack">' . $attack[ 'message' ] . '</p>' );

            $health_foe -= $attack[ 'damage' ];
        } else {
            $attack = ag_get_attack( $foe[ 'ability' ], TRUE );
            echo( '<p class="attack">' . $attack[ 'message' ] . '</p>' );
            echo( '</div></div>' );

            $health_char -= $attack[ 'damage' ];
        }

        if ( $health_char <= 0 || $health_foe <= 0 ) {
            $combat = FALSE;
        }

        $player_turn = ! $player_turn;
    }

    if ( $health_foe <= 0 ) {
        echo( '</div></div>' );
    }

    if ( $health_char <= 0 ) {
        echo( '<div class="row text-center">' );
        echo( '<h2>' . $foe[ 'name' ] . ' wins!</h2>' );
        echo( '<p class="lead">You take a huge stamina hit as you lurch ' .
              'back to a safe spot and heal.</p>' );

        $new_stamina = max( 0, $stamina - 10.0 );
        update_character_meta( $character[ 'id' ], ag_meta_type_character,
            AG_STAMINA, $new_stamina );

        $new_losses = character_meta_int(
            ag_meta_type_character, AG_LOSSES ) + 1;
        update_character_meta( $character[ 'id' ], ag_meta_type_character,
            AG_LOSSES, $new_losses );
    } else if ( $health_foe <= 0 ) {
        echo( '<div class="row text-center">' );
        echo( '<h2>You win!</h2>' );

        if ( isset( $foe[ 'boss_id' ] ) ) {
            if ( 1 == $foe[ 'boss_id' ] ) {
                award_achievement( 1 );
            } else if ( 2 == $foe[ 'boss_id' ] ) {
                award_achievement( 2 );
            } else if ( 3 == $foe[ 'boss_id' ] ) {
                award_achievement( 3 );
            }
        }

        $new_stamina = $stamina - 1.0;
        update_character_meta( $character[ 'id' ], ag_meta_type_character,
            AG_STAMINA, $new_stamina );

        $new_wins = character_meta_int(
            ag_meta_type_character, AG_WINS ) + 1;
        update_character_meta( $character[ 'id' ], ag_meta_type_character,
            AG_WINS, $new_wins );

        echo( '<h4>You gain ' . $foe[ 'gold' ] . ' gold.</h4>' );
        $new_gold = $character[ 'gold' ] + $foe[ 'gold' ];
        update_character_meta( $character[ 'id' ], ag_meta_type_character,
            AG_GOLD, $new_gold );

        echo( '<h4>You gain ' . $foe[ 'ability' ] .
              ' experience.</h4>' );
        $new_xp = $character[ 'xp' ] + $foe[ 'ability' ];
        update_character_meta( $character[ 'id' ], ag_meta_type_character,
            AG_XP, $new_xp );

        mt_srand();
        if ( ( isset( $foe[ 'boss_id' ] ) ) || ( mt_rand( 1, 100 ) <= 100 ) ) {
            echo( '<h4>A piece of incredible loot falls to the ground!</h4>' );

            $gear_drop = array(
                AG_WEAPON, AG_HEAD, AG_CHEST, AG_LEGS, AG_HANDS, AG_FEET,
                AG_EYES, AG_FINGERS, AG_TOES, AG_NOSE, AG_NECK, AG_WRISTS,
            );

            $gear_slot = $gear_drop[ array_rand( $gear_drop ) ];
            $gear = ag_get_gear( $gear_slot, $foe[ 'ability' ] );

            echo( '<h3>Available Gear: ' . ag_gear_string( $gear ) . '</h3>' );
            echo( '<h3>Currently Equipped: ' .
                  ag_gear_string( $character[
                      array_search( $gear_slot, $gear_obj ) ] ) );
            echo( '<h3>(<a href="game-setting.php?setting=equip_gear">' .
                  'Click to discard your old gear and take the new gear' .
                  '</a>)</h3>' );

            update_character_meta( $character[ 'id' ], ag_meta_type_character,
                AG_STORED_GEAR, json_encode( $gear ) );
            update_character_meta( $character[ 'id' ], ag_meta_type_character,
                AG_STORED_SLOT, $gear_slot );
        }
    }
?>
</div>
<?php
}

add_action( 'do_page_content', 'ag_combat_content' );

function ag_echo_health( $character, $foe, $health_char, $health_foe ) {
?>
  <div class="row attack">
    <div class="col-xs-3 text-center">
      <?php echo( $character[ 'character_name' ] ); ?><br>
      Health: <?php echo( $health_char ); ?> /
              <?php echo( $character[ 'health' ] ); ?>
    </div>
    <div class="col-xs-3 text-center">
      <?php echo( $foe[ 'name' ] ); ?><br>
      Health: <?php echo( $health_foe ); ?> /
              <?php echo( $foe[ 'health' ] ); ?>
    </div>
    <div class="col-xs-6 text-center">
<?php
}

function ag_get_foe( $level ) {
    $foe = array();

    $prefix = array(
        'Furious ', 'Temperate ', 'Devious ', 'Dark ', 'Ticklish ',
        'Musty ', 'Juicy ', 'Smelly ', 'Frumpish ', 'Foul ', 'Purple ',
        'Green ', 'Blue ', 'Powerful ', 'Silly ', 'Calm ', 'Drunken ',
    );

    $middle = array(
        'Ooze', 'Kobold', 'Warrior', 'Enchantress', 'Fungus', 'Beast',
        'Dinosaur', 'Dragon', 'Fairy', 'Fiend', 'Fish', 'Insect',
        'Machine', 'Plant', 'Reptile', 'Serpent', 'Zombie', 'Basilisk',
        'Dog', 'Chimera', 'Cyclops', 'Demon', 'Artist', 'Mathematician',
        'Accountant', 'Librarian', 'Elf', 'Golem', 'Hydra', 'Imp',
        'Mothman', 'Ogre', 'Wendigo', 'Wraith', 'Ant', 'Bag of Hair',
    );

    $suffix = array(
        ' of Yendor', ' of Doom', ', Esq.', ', Jr.', ', Sr.',
    );

    $st = '';
    if ( mt_rand( 0, 10 ) < 8 ) {
        $st .= $prefix[ array_rand( $prefix ) ];
    }
    $st .= $middle[ array_rand( $middle ) ];
    if ( mt_rand( 0, 10 ) < 6 ) {
        $st .= $suffix[ array_rand( $suffix ) ];
    }

    $foe[ 'name' ] = $st;

    $foe[ 'health' ] = round( pow( $level * 2, 1.5 ) );
    $foe[ 'health' ] += round(
        $foe[ 'health' ] * ( mt_rand() / mt_getrandmax() ) );

    $foe[ 'gold' ] = $level * 10;
    $foe[ 'gold' ] += round(
        $foe[ 'gold' ] * ( mt_rand() / mt_getrandmax() ) );

    $foe[ 'ability' ] = $level;

    return $foe;
}

function ag_boss( $boss_id, $name, $health, $gold, $ability ) {
    return array(
        'boss_id' => $boss_id,
        'name' => $name,
        'health' => $health,
        'gold' => $gold,
        'ability' => $ability
    );
}

function ag_get_boss( $id ) {
    $map = array();
    $foe = array();

    if ( 1 == $id ) {
        $map = ag_get_map_state( 5, 5 );
        $foe = ag_boss(
            1, 'Chester, he who is Mediocre', mt_rand( 20, 30 ),
            mt_rand( 60, 120 ), mt_rand( 4, 6 ) );
    } else if ( 2 == $id ) {
        $map = ag_get_map_state( 10, 10 );
        $foe = ag_boss(
            2, 'Thunderface', round( pow( mt_rand( 20, 22 ), 1.5 ) ),
            mt_rand( 120, 200 ), mt_rand( 10, 12 ) );
    } else if ( 3 == $id ) {
        $map = ag_get_map_state( 15, 15 );
        $foe = ag_boss(
            3, 'Doctor Blob', round( pow( mt_rand( 35, 39 ), 1.5 ) ),
            mt_rand( 500, 700 ), mt_rand( 16, 19 ) );
    } else {
        $map = ag_get_map_state( 0, 0 );
        $foe = ag_boss(
            -1, 'Piotr the Hax0red', mt_rand( 200000, 300000 ), 1,
            mt_rand( 200000, 300000 ) );
    }

    return array( 'map' => $map, 'foe' => $foe );
}

function ag_get_attack( $ability, $is_foe ) {
    global $character;

    $obj = array();

    $obj[ 'damage' ] = round( pow( $ability * 2, 1.5 ) );//$ability;

    if ( $is_foe ) {
        $obj[ 'message' ] = 'Your foe delivers a crushing strike for ' .
            $obj[ 'damage' ] . ' damage!';

        $old_dmg = character_meta_int(
            ag_meta_type_character, AG_MAX_DAMAGE_TAKEN );
        if ( $obj[ 'damage' ] > $old_dmg ) {
            update_character_meta( $character[ 'id' ], ag_meta_type_character,
                AG_MAX_DAMAGE_TAKEN, $obj[ 'damage' ] );
        }
    } else {
        $obj[ 'damage' ] = $ability + round(
            $ability * ( mt_rand() / mt_getrandmax() ) );
        $obj[ 'message' ] = 'You deliver a crushing strike for ' .
            $obj[ 'damage' ] . ' damage!';

        $old_dmg = character_meta_int(
            ag_meta_type_character, AG_MAX_DAMAGE_DONE );
        if ( $obj[ 'damage' ] > $old_dmg ) {
            update_character_meta( $character[ 'id' ], ag_meta_type_character,
                AG_MAX_DAMAGE_DONE, $obj[ 'damage' ] );
        }
    }

    return $obj;
}

function ag_equip_gear( $args ) {
    global $character;

    $gear_obj = ag_get_gear_obj();

    $gear_slot = character_meta( ag_meta_type_character, AG_STORED_SLOT );

    update_character_meta( $character[ 'id' ], ag_meta_type_character,
        $gear_slot, character_meta( ag_meta_type_character, AG_STORED_GEAR ) );

    $GLOBALS[ 'redirect_header' ] = GAME_URL . '?action=profile';
}

$custom_setting_map[ 'equip_gear' ] = 'ag_equip_gear';


function ag_boss_content() {
    global $character, $game;

    if ( strcmp( 'boss', $game->get_action() ) ) {
       return;
    }
?>

<div class="row text-right">
  <h1 class="page_section">Boss Battles</h1>
</div>

<?php

    $boss_id = 0;
    if ( isset( $_GET[ 'id' ] ) ) {
        $boss_id = intval( $_GET[ 'id' ] );
    }

    if ( $boss_id > 0 ) {
        $boss_obj = ag_get_boss( $boss_id );

        ag_do_combat( $map_obj = $boss_obj[ 'map' ],
                      $foe = $boss_obj[ 'foe' ] );
    } else {
?>
<div class="row text-center">
  <h2>Which boss do you dare to challenge?</h2>
  <h3><a href="?action=boss&id=1">Boss 1</a></h3>
  <h3><a href="?action=boss&id=2">Boss 2</a></h3>
  <h3><a href="?action=boss&id=3">Boss 3</a></h3>
</div>
<?php
    }
}

add_action( 'do_page_content', 'ag_boss_content' );

