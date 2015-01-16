<?php

global $ag;

define( 'AG_VENDOR_GEAR_MAX', 3 );

function ag_vendor_content() {
    global $ag;

    if ( strcmp( 'vendor', $ag->get_state() ) ) {
       return;
    }
?>

<div class="row text-right">
  <h1 class="page_section">Vendor</h1>
</div>

<?php
    $obj = ag_get_map_state( $ag->char[ 'x' ], $ag->char[ 'y' ] );

    $vendor_gear = ag_get_vendor_gear( $ag->char[ 'x' ], $ag->char[ 'y' ],
        $obj[ 'level' ] );

?>
<div class="row text-center">
  <h3>Welcome! Care to purchase something?</h3>
</div>

<?php

    ag_xy_seed( $ag->char[ 'x' ], $ag->char[ 'y' ] );

    $gear_i = 0;
    foreach ( $vendor_gear as $gear ) {
        echo( '<div class="row text-center">' );
        echo( '<h4>Gear #' . ( $gear_i + 1 ) . ': ' . ag_gear_string( $gear ) .
              '<br>(<a href="game-setting.php?setting=vendor_buy&id=' .
              $gear_i . '">Purchase for ' .
              $obj[ 'level' ] * 1000 .
              ' gold?</a>)</h4>' );
        echo( '</div>' );

        $gear_i += 1;
    }

}

$ag->add_state( 'do_page_content', FALSE, 'ag_vendor_content' );



function ag_get_vendor_gear( $x, $y, $level ) {
    mt_srand( $x );
    mt_srand( mt_rand() + $y );

    $gear = array();

    for ( $i = 0; $i < AG_VENDOR_GEAR_MAX; $i++ ) {
        $gear[] = ag_get_gear( ag_get_gear_slot(), $level + 3 );
    }

    mt_srand();

    return $gear;
}


function ag_vendor_buy( $args ) {
    global $ag;

    $GLOBALS[ 'redirect_header' ] = GAME_URL . '?state=vendor';

    if ( ! isset( $args[ 'id' ] ) ) {
        return;
    }

    $id = intval( $args[ 'id' ] );

    if ( $id < 0 || $id >= AG_VENDOR_GEAR_MAX ) {
        return;
    }

    $x = character_meta( ag_meta_type_character, AG_POS_X );
    $y = character_meta( ag_meta_type_character, AG_POS_Y );

    $obj = ag_get_map_state( $x, $y );

    $vendor_gear = ag_get_vendor_gear( $x, $y, $obj[ 'level' ] );

    $gold = character_meta_int( ag_meta_type_character, AG_GOLD );
    $cost = intval( $obj[ 'level' ] ) * 1000;

    if ( $gold < $cost ) {
        ag_tip( 'You don\'t have enough gold!' );

        return;
    }

    $new_gold = $gold - $cost;
    update_character_meta( $ag->char[ 'id' ], ag_meta_type_character,
        AG_GOLD, $new_gold );

    $gear = $vendor_gear[ $id ];

    update_character_meta( $ag->char[ 'id' ], ag_meta_type_character,
        $gear[ 'slot' ], json_encode( $gear, $assoc = TRUE ) );

    $GLOBALS[ 'redirect_header' ] = GAME_URL . '?state=character';

    ag_tip( 'You purchase the ' . ag_gear_string( $gear ) . ' for ' .
            $cost . ' gold.' );
}

$custom_setting_map[ 'vendor_buy' ] = 'ag_vendor_buy';