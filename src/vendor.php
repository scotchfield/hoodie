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

    $ag->hq->xy_seed( $ag->char[ 'x' ], $ag->char[ 'y' ] );

    $gear_i = 0;
    foreach ( $vendor_gear as $gear ) {
        echo( '<div class="row text-center">' );
        echo( '<h4>Gear #' . ( $gear_i + 1 ) . ': ' . $ag->hq->gear_string( $gear ) .
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
    global $ag;

    mt_srand( $x );
    mt_srand( mt_rand() + $y );

    $gear = array();

    for ( $i = 0; $i < AG_VENDOR_GEAR_MAX; $i++ ) {
        $gear[] = $ag->hq->get_gear( $ag->hq->get_gear_slot(), $level + 3 );
    }

    mt_srand();

    return $gear;
}


function ag_vendor_buy( $args ) {
    global $ag;

    $ag->set_redirect_header( GAME_URL . '?state=vendor' );

    if ( ! isset( $args[ 'id' ] ) ) {
        return;
    }

    $id = intval( $args[ 'id' ] );

    if ( $id < 0 || $id >= AG_VENDOR_GEAR_MAX ) {
        return;
    }

    $x = $ag->c( 'user' )->character_meta( ag_meta_type_character, AG_POS_X );
    $y = $ag->c( 'user' )->character_meta( ag_meta_type_character, AG_POS_Y );

    $obj = ag_get_map_state( $x, $y );

    $vendor_gear = ag_get_vendor_gear( $x, $y, $obj[ 'level' ] );

    $gold = $ag->c( 'user' )->character_meta_int(
        ag_meta_type_character, AG_GOLD );
    $cost = intval( $obj[ 'level' ] ) * 1000;

    if ( $gold < $cost ) {
        $ag->hq->tip( 'You don\'t have enough gold!' );

        return;
    }

    $new_gold = $gold - $cost;
    $ag->c( 'user' )->update_character_meta(
        $ag->char[ 'id' ], ag_meta_type_character, AG_GOLD, $new_gold );

    $gear = $vendor_gear[ $id ];

    $ag->c( 'user' )->update_character_meta(
        $ag->char[ 'id' ], ag_meta_type_character,
        $gear[ 'slot' ], json_encode( $gear, $assoc = TRUE ) );

    $ag->set_redirect_header( GAME_URL . '?state=character' );

    $ag->hq->tip( 'You purchase the ' . $ag->hq->gear_string( $gear ) . ' for ' .
            $cost . ' gold.' );
}

$ag->add_state( 'do_setting', 'vendor_buy', 'ag_vendor_buy' );
