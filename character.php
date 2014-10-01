<?php

function ag_profile_content() {
    global $character;

    if ( strcmp( 'profile', game_get_action() ) ) {
       return;
    }

?>
<div class="row text-right">
  <h1 class="page_section">Profile</h1>
</div>
<?php
    if ( $character[ 'ability' ] >= 10 ) {
        award_achievement( 100 );
    }
    if ( $character[ 'ability' ] >= 25 ) {
        award_achievement( 101 );
    }
    if ( $character[ 'ability' ] >= 50 ) {
        award_achievement( 102 );
    }
    if ( $character[ 'ability' ] >= 100 ) {
        award_achievement( 103 );
    }
    if ( $character[ 'ability' ] >= 250 ) {
        award_achievement( 104 );
    }
    if ( $character[ 'ability' ] >= 500 ) {
        award_achievement( 105 );
    }
    if ( $character[ 'ability' ] >= 1000 ) {
        award_achievement( 106 );
    }

    ag_print_character( $character );
?>
</div>
<div class="row text-center">
<a href="https://twitter.com/share" class="twitter-share-button" data-url="<?php echo( GAME_URL ); ?>?action=char&id=<?php echo( $character[ 'id' ] ); ?>" data-text="I'm on a quest for the warmest hoodie." data-size="large" data-count="none" data-hashtags="hoodiecraft">Tweet</a>
<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script>
</div>
<?php
}

add_action( 'do_page_content', 'ag_profile_content' );

function ag_char_content() {
    if ( strcmp( 'char', game_get_action() ) ) {
       return;
    }

?>
<div class="row text-right">
  <h1 class="page_section">Profile</h1>
</div>
<?php

    if ( ! isset( $_GET[ 'id' ] ) ) {
        return;
    }

    $char_id = intval( $_GET[ 'id' ] );
    $char = get_character_by_id( $char_id );

    if ( FALSE == $char ) {
        return;
    }

    $char[ 'meta' ] = get_character_meta( $char_id );

    $char = ag_get_unpacked_character( $char );

    ag_print_character( $char );
}

add_action( 'do_page_content', 'ag_char_content' );


function ag_print_character( $character ) {
?>
<div class="row">
  <div class="col-md-6">
    <h2>Details</h2>

    <dl class="dl-horizontal">
      <dt>Name</dt>
      <dd><?php echo( $character[ 'character_name' ] ); ?></dd>
      <dt>Health</dt>
      <dd><?php echo( $character[ 'health' ] ); ?></dd>
      <dt>Stamina</dt>
      <dd><?php echo( round( $character[ 'stamina' ], $precision = 2 ) ); ?> /
          <?php echo( $character[ 'stamina_max' ] ); ?></dd>
      <dt>Gold</dt>
      <dd><?php echo( $character[ 'gold' ] ); ?></dd>
      <dt>Experience Points</dt>
      <dd><?php echo( $character[ 'xp' ] ); ?></dd>
      <dt>World Position</dt>
      <dd>(<?php echo( $character[ 'x' ] . ', ' . $character[ 'y' ] ); ?>)</dd>
      <dt>Record</dt>
      <dd><?php echo( $character[ 'wins' ] ); ?> -
          <?php echo( $character[ 'losses' ] ); ?></dd>
      <dt>Largest Hit Given</dt>
      <dd><?php echo( $character[ 'max_damage_done' ] ); ?> damage</dd>
      <dt>Largest Hit Taken</dt>
      <dd><?php echo( $character[ 'max_damage_taken' ] ); ?> damage</dd>
    </dl>

    <h2>Gear</h2>

    <dl class="dl-horizontal">
<?php
    $gear = array(
        'Hoodie' => 'hoodie',
        'Weapon' => 'weapon',
        'Head' => 'head',
        'Chest' => 'chest',
        'Legs' => 'legs',
        'Hands' => 'hands',
        'Feet' => 'feet',
        'Eyes' => 'eyes',
        'Fingers' => 'fingers',
        'Toes' => 'toes',
        'Nose' => 'nose',
        'Neck' => 'neck',
        'Wrists' => 'wrists',
    );

    foreach ( $gear as $k => $v ) {
        echo( "      <dt>$k</dt>\n" );
        $gear = FALSE;
        if ( isset( $character[ $v ] ) ) {
            $gear = $character[ $v ];
        }
        echo( '      <dd>' . ag_gear_string( $gear ) . "</dd>\n" );
    }
?>
    </dl>

  </div>
  <div class="col-md-6">

    <h2>Stats</h2>

    <dl class="dl-horizontal">
<?php
    $stat_keys = array_keys( $character[ 'stats' ] );
    usort( $stat_keys, 'ag_sort_stats_cmp' );

    foreach ( $stat_keys as $k ) {
        echo( "      <dt>$k</dt>\n" );
        echo( '      <dd>' . $character[ 'stats' ][ $k ] . "</dd>\n" );
    }
?>
    </dl>

  </div>

</div>
<?php
}

function ag_sort_stats_cmp( $a, $b ) {
    if ( 'Hoodie' == $a ) {
        return -1;
    } else if ( 'Hoodie' == $b ) {
        return 1;
    }
    return ( $a < $b ) ? -1 : 1;
}

function ag_achievements_content() {
    global $character;

    if ( strcmp( 'achievements', game_get_action() ) ) {
       return;
    }

?>
<div class="row text-right">
  <h1 class="page_section">Achievements</h1>
</div>
<div class="row">
  <div class="col-md-6">
    <h3>Your achievements</h3>
<?php
    if ( ( ! isset( $character[ 'meta' ][ game_meta_type_achievement ] ) ) ||
         ( 0 == count(
                    $character[ 'meta' ][ game_meta_type_achievement ] ) ) ) {
        echo( '<h4>None yet!</h4>' );
    } else {
        echo( '<dl class="dl-horizontal">' );
        $achieve_obj = get_achievements( $character[ 'id' ] );

        foreach ( $achieve_obj as $achieve ) {
            $meta = json_decode( $achieve[ 'meta_value' ], TRUE );
            echo( '<dt>' . $meta[ 'name' ] . '</dt><dd>' .
                  $meta[ 'text' ] . '</dd><dd>' .
                  date( 'F j, Y, g:ia', $achieve[ 'timestamp' ] ) .
                  '</dd>' );
        }
        echo( '</dl>' );
    }
?>
  </div>
  <div class="col-md-6">
    <h3>Achievements Remaining</h3>
    <dl class="dl-horizontal">
<?php
    $achieve_obj = get_all_achievements();

    foreach ( $achieve_obj as $k => $achieve ) {
        if ( isset( $character[ 'meta' ][
                        game_meta_type_achievement ][ $k ] ) ) {
            continue;
        }
        $meta = json_decode( $achieve[ 'meta_value' ], TRUE );
        echo( '<dt>' . $meta[ 'name' ] . '</dt><dd>' .
              $meta[ 'text' ] . '</dd>' );
    }
?>
  </dl>
  </div>
</div>
<?php
}

add_action( 'do_page_content', 'ag_achievements_content' );
