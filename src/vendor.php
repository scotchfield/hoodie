<?php

define( 'AG_VENDOR_GEAR_MAX', 3 );

class HQVendor {

    public $ag;

    public function __construct( $ag ) {
        $ag->add_state( 'do_page_content', FALSE,
            array( $this, 'vendor_content' ) );
        $ag->add_state( 'do_setting', 'vendor_buy',
            array( $this, 'vendor_buy' ) );

        $this->ag = $ag;
    }

    public function vendor_content() {
        if ( strcmp( 'vendor', $this->ag->get_state() ) ) {
           return;
        }
?>

<div class="row text-right">
  <h1 class="page_section">Vendor</h1>
</div>

<?php
        $obj = $this->ag->c( 'hq_map' )->get_map_state( $this->ag->char[ 'x' ], $this->ag->char[ 'y' ] );

        $vendor_gear = $this->get_vendor_gear( $this->ag->char[ 'x' ], $this->ag->char[ 'y' ],
            $obj[ 'level' ] );

?>
<div class="row text-center">
  <h3>Welcome! Care to purchase something?</h3>
</div>

<?php

        $this->ag->hq->xy_seed( $this->ag->char[ 'x' ], $this->ag->char[ 'y' ] );

        $gear_i = 0;
        foreach ( $vendor_gear as $gear ) {
            echo( '<div class="row text-center">' );
            echo( '<h4>Gear #' . ( $gear_i + 1 ) . ': ' . $this->ag->hq->gear_string( $gear ) .
                  '<br>(<a href="game-setting.php?setting=vendor_buy&id=' .
                  $gear_i . '">Purchase for ' .
                  $obj[ 'level' ] * 1000 .
                  ' gold?</a>)</h4>' );
            echo( '</div>' );

            $gear_i += 1;
        }

    }

    public function get_vendor_gear( $x, $y, $level ) {
        mt_srand( $x );
        mt_srand( mt_rand() + $y );

        $gear = array();

        for ( $i = 0; $i < AG_VENDOR_GEAR_MAX; $i++ ) {
            $gear[] = $this->ag->hq->get_gear( $this->ag->hq->get_gear_slot(), $level + 3 );
        }

        mt_srand();

        return $gear;
    }

    public function vendor_buy( $args ) {
        $this->ag->set_redirect_header( GAME_URL . '?state=vendor' );

        if ( ! isset( $args[ 'id' ] ) ) {
            return;
        }

        $id = intval( $args[ 'id' ] );

        if ( $id < 0 || $id >= AG_VENDOR_GEAR_MAX ) {
            return;
        }

        $x = $this->ag->c( 'user' )->character_meta( ag_meta_type_character, AG_POS_X );
        $y = $this->ag->c( 'user' )->character_meta( ag_meta_type_character, AG_POS_Y );

        $obj = $this->ag->c( 'hq_map' )->get_map_state( $x, $y );

        $vendor_gear = $this->get_vendor_gear( $x, $y, $obj[ 'level' ] );

        $gold = $this->ag->c( 'user' )->character_meta_int(
            ag_meta_type_character, AG_GOLD );
        $cost = intval( $obj[ 'level' ] ) * 1000;

        if ( $gold < $cost ) {
            $this->ag->hq->tip( 'You don\'t have enough gold!' );

            return;
        }

        $new_gold = $gold - $cost;
        $this->ag->c( 'user' )->update_character_meta(
            $this->ag->char[ 'id' ], ag_meta_type_character, AG_GOLD, $new_gold );

        $gear = $vendor_gear[ $id ];

        $this->ag->c( 'user' )->update_character_meta(
            $this->ag->char[ 'id' ], ag_meta_type_character,
            $gear[ 'slot' ], json_encode( $gear, $assoc = TRUE ) );

        $this->ag->set_redirect_header( GAME_URL . '?state=character' );

        $this->ag->hq->tip( 'You purchase the ' . $this->ag->hq->gear_string( $gear ) . ' for ' .
                $cost . ' gold.' );
    }

}
