<?php

class HQMap {

    public $ag;

    public function __construct( $ag ) {
        $ag->add_state( 'do_page_content', 'map',
            array( $this, 'map_content' ) );
        $ag->add_state( 'do_setting', 'map_move',
            array( $this, 'map_move' ) );

        $this->ag = $ag;
    }

    public function map_content() {
        $map_obj = $this->get_map_state( $this->ag->char[ 'x' ], $this->ag->char[ 'y' ] );
?>

<div class="row text-right">
  <h1 class="page_section">Map</h1>
</div>
<div class="row text-center">
  <h3>Current Location: (<b><?php echo( $this->ag->char[ 'x' ] ); ?></b>,
  <b><?php echo( $this->ag->char[ 'y' ] ); ?>)</h3>

  <h4><a href="?state=combat">Find a foe to battle</a>
    (Monster Level: <?php echo( $map_obj[ 'level' ] ); ?>)</h4>
  <h4><a href="?state=vendor">Buy some goods from a nearby vendor</a></h4>
  <h5>Stamina: <b><?php echo( round(
      $this->ag->char[ 'stamina' ], $precision = 2 ) ); ?></b> / <b>100</b></h5>
</div>

<div class="row text-center">
  <h2>Move to a new location</h2>
</div>
<?php

        $this->draw_map( $this->ag->char[ 'x' ], $this->ag->char[ 'y' ] );
    }

    public function draw_map( $x, $y ) {
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
                $obj = $this->get_map_state( $j, $i );
                echo( '<div>' );

                if ( ! $char_origin ) {
                    echo( '<a href="game-setting.php?state=map_move&x=' . $j .
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

    public function get_map_state( $x, $y ) {
        mt_srand( $x );
        mt_srand( mt_rand() + $y );

        $obj = array();
        $obj[ 'height' ] = mt_rand( 0, 7 );
        $obj[ 'level' ] = floor( sqrt( ( $x * $x ) + ( $y * $y ) ) ) + 1;

        mt_srand();

        return $obj;
    }

    public function map_move() {
        $this->ag->set_redirect_header( GAME_URL . '?state=map' );

        if ( ! $this->ag->get_arg( 'x' ) || ! $this->ag->get_arg( 'y' ) ) {
            $this->ag->hq->tip( 'Missing x or y values.' );
            return;
        }

        $xd = abs( abs( $this->ag->get_arg( 'x' ) ) - abs( $this->ag->c( 'user' )->character_meta_int(
            ag_meta_type_character, AG_POS_X ) ) );
        $yd = abs( abs( $this->ag->get_arg( 'y' ) ) - abs( $this->ag->c( 'user' )->character_meta_int(
            ag_meta_type_character, AG_POS_Y ) ) );

        if ( $xd > 1 || $yd > 1 || ( $xd == 0 && $yd == 0 ) ) {
            return;
        }

        $stamina = $this->ag->c( 'user' )->character_meta_float( ag_meta_type_character, AG_STAMINA );
        $stamina_req = sqrt( $xd + $yd );

        if ( $stamina < $stamina_req ) {
            return;
        }

        $new_stamina = $stamina - $stamina_req;
        $this->ag->c( 'user' )->update_character_meta( $this->ag->char[ 'id' ], ag_meta_type_character,
            AG_STAMINA, $new_stamina );
        $this->ag->c( 'user' )->update_character_meta( $this->ag->char[ 'id' ], ag_meta_type_character,
            AG_POS_X, $this->ag->get_arg( 'x' ) );
        $this->ag->c( 'user' )->update_character_meta( $this->ag->char[ 'id' ], ag_meta_type_character,
            AG_POS_Y, $this->ag->get_arg( 'y' ) );
    }

}
