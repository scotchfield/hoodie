<?php

global $ag;

function ag_map_content() {
    global $ag;

    if ( strcmp( 'map', $ag->get_state() ) ) {
       return;
    }

    $map_obj = ag_get_map_state( $ag->char[ 'x' ], $ag->char[ 'y' ] );
?>

<div class="row text-right">
  <h1 class="page_section">Map</h1>
</div>
<div class="row text-center">
  <h3>Current Location: (<b><?php echo( $ag->char[ 'x' ] ); ?></b>,
  <b><?php echo( $ag->char[ 'y' ] ); ?>)</h3>

  <h4><a href="?state=combat">Find a foe to battle</a>
    (Monster Level: <?php echo( $map_obj[ 'level' ] ); ?>)</h4>
  <h4><a href="?state=vendor">Buy some goods from a nearby vendor</a></h4>
  <h5>Stamina: <b><?php echo( round(
      $ag->char[ 'stamina' ], $precision = 2 ) ); ?></b> / <b>100</b></h5>
</div>

<div class="row text-center">
  <h2>Move to a new location</h2>
</div>
<?php

    ag_draw_map( $ag->char[ 'x' ], $ag->char[ 'y' ] );
}

$ag->add_state( 'do_page_content', FALSE, 'ag_map_content' );

function ag_draw_map( $x, $y ) {
    for ( $i = $y + 1; $i >= $y - 1; $i-- ) {
        echo( '<div class="row text-center">' );
        for ( $j = $x - 1; $j <= $x + 1; $j++ ) {

            $char_origin = FALSE;
            if ( $i == $y && $j == $x ) {
                $char_origin = TRUE;
            }

            if ( $j == $x - 1 ) {
                echo( '<div class="col-xs-2 col-xs-offset-3">' );
            } else {
                echo( '<div class="col-xs-2">' );
            }
            $obj = ag_get_map_state( $j, $i );
            echo( '<div>' );

            if ( ! $char_origin ) {
                echo( '<a href="game-setting.php?setting=map_move&x=' . $j .
                      '&y=' . $i . '">' );
            }

            echo( '<img src="' . GAME_CUSTOM_STYLE_URL . '/img/' .
                  $obj[ 'height' ] . '.png" width="96" height="96">' );

            if ( ! $char_origin ) {
                echo( '</a>' );
            }

            echo( '</div>' );
            echo( '<div class="map_overlay">(<b>' . $j .
                  '</b>, <b>' . $i . '</b>)</div>' );
            echo( '</div>' );
        }
        echo( '</div>' );
    }
}

function ag_get_map_state( $x, $y ) {
    mt_srand( $x );
    mt_srand( mt_rand() + $y );

    $obj = array();
    $obj[ 'height' ] = mt_rand( 0, 7 );
    $obj[ 'level' ] = floor( sqrt( ( $x * $x ) + ( $y * $y ) ) ) + 1;

    mt_srand();

    return $obj;
}


function ag_map_move( $args ) {
    global $ag;

    $GLOBALS[ 'redirect_header' ] = GAME_URL . '?state=map';

    if ( ! isset( $args[ 'x' ] ) || ! isset( $args[ 'y' ] ) ) {
        ag_tip( 'Missing x or y values.' );
        return;
    }

    $xd = abs( abs( $args[ 'x' ] ) - abs( character_meta_int(
        ag_meta_type_character, AG_POS_X ) ) );
    $yd = abs( abs( $args[ 'y' ] ) - abs( character_meta_int(
        ag_meta_type_character, AG_POS_Y ) ) );

    if ( $xd > 1 || $yd > 1 || ( $xd == 0 && $yd == 0 ) ) {
        return;
    }

    $stamina = character_meta_float( ag_meta_type_character, AG_STAMINA );
    $stamina_req = sqrt( $xd + $yd );

    if ( $stamina < $stamina_req ) {
        return;
    }

    $new_stamina = $stamina - $stamina_req;
    update_character_meta( $ag->char[ 'id' ], ag_meta_type_character,
        AG_STAMINA, $new_stamina );
    update_character_meta( $ag->char[ 'id' ], ag_meta_type_character,
        AG_POS_X, $args[ 'x' ] );
    update_character_meta( $ag->char[ 'id' ], ag_meta_type_character,
        AG_POS_Y, $args[ 'y' ] );
}

$custom_setting_map[ 'map_move' ] = 'ag_map_move';
