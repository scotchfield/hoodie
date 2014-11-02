<?php

require( GAME_CUSTOM_PATH . 'title.php' );

require( GAME_CUSTOM_PATH . 'character.php' );
require( GAME_CUSTOM_PATH . 'combat.php' );
require( GAME_CUSTOM_PATH . 'map.php' );
require( GAME_CUSTOM_PATH . 'tutorial.php' );
require( GAME_CUSTOM_PATH . 'vendor.php' );

define( 'ag_meta_type_character',            1 );

define( 'AG_STAMINA', 1 );
define( 'AG_GOLD', 2 );
define( 'AG_XP', 3 );
define(	'AG_STAMINA_TIMESTAMP',	4 );

define( 'AG_POS_X', 10 );
define( 'AG_POS_Y', 11 );

define( 'AG_TIP', 20 );

define( 'AG_STORED_GEAR', 30 );
define( 'AG_STORED_SLOT', 31 );

define( 'AG_WINS', 40 );
define( 'AG_LOSSES', 41 );
define( 'AG_MAX_DAMAGE_DONE', 42 );
define( 'AG_MAX_DAMAGE_TAKEN', 43 );

define( 'AG_TUTORIAL', 50 );

define( 'AG_HOODIE', 100 );
define( 'AG_WEAPON', 101 );
define( 'AG_HEAD', 102 );
define( 'AG_CHEST', 103 );
define( 'AG_LEGS', 104 );
define( 'AG_HANDS', 105 );
define( 'AG_FEET', 106 );
define( 'AG_EYES', 107 );
define( 'AG_FINGERS', 108 );
define( 'AG_TOES', 109 );
define( 'AG_NOSE', 110 );
define( 'AG_NECK', 111 );
define( 'AG_WRISTS', 112 );

function ag_get_gear_obj() {
    return array(
        'hoodie' => AG_HOODIE,
        'weapon' => AG_WEAPON,
        'head' => AG_HEAD,
        'chest' => AG_CHEST,
        'legs' => AG_LEGS,
        'hands' => AG_HANDS,
        'feet' => AG_FEET,
        'eyes' => AG_EYES,
        'fingers' => AG_FINGERS,
        'toes' => AG_TOES,
        'nose' => AG_NOSE,
        'neck' => AG_NECK,
        'wrists' => AG_WRISTS,
    );
}

function ag_get_gear_slot() {
    $gear_obj = ag_get_gear_obj();

    $gear_values = array_values( $gear_obj );

    return $gear_values[ mt_rand( 0, count( $gear_values ) - 1 ) ];
}

function ag_default_action() {
    global $user, $character, $game;

    if ( FALSE == $user ) {
        $game->set_action( 'title' );
    } else if ( FALSE == $character ) {
        $character = get_character_by_name( $user[ 'user_name' ] );

        if ( FALSE == $character ) {
            $character_id = add_character(
                $user[ 'id' ], $user[ 'user_name' ] );

            $character = get_character_by_name( $user[ 'user_name' ] );
            // @todo: arcadia should do this, not me
        }

        $_SESSION[ 'c' ] = $character[ 'id' ];
        $GLOBALS[ 'character' ] = $character;

        do_action( 'select_character' );

        header( 'Location: ' . GAME_URL );
        exit;
    } else {
        $game->set_action( 'profile' );
    }
}

add_action( 'set_default_action', 'ag_default_action' );


function ag_unpack_character() {
    global $character;

    if ( FALSE == $character ) {
        return;
    }

    $character = ag_get_unpacked_character( $character );
}

function ag_meta_int( $char, $key_type, $meta_key ) {
    if ( ! isset( $char[ 'meta' ][ $key_type ] ) ) {
        return 0;
    } else if ( ! isset( $char[ 'meta' ][ $key_type ][ $meta_key ] ) ) {
        return 0;
    }

    return intval( $char[ 'meta' ][ $key_type ][ $meta_key ] );
}

function ag_meta_float( $char, $key_type, $meta_key ) {
    if ( ! isset( $char[ 'meta' ][ $key_type ] ) ) {
         return 0;
    } else if ( ! isset( $char[ 'meta' ][ $key_type ][ $meta_key ] ) ) {
         return 0;
    }

    return floatval( $char[ 'meta' ][ $key_type ][ $meta_key ] );
}

function ag_get_unpacked_character( $char ) {
    $gear_obj = ag_get_gear_obj();

    $char[ 'x' ] = ag_meta_int( $char, ag_meta_type_character, AG_POS_X );
    $char[ 'y' ] = ag_meta_int( $char, ag_meta_type_character, AG_POS_Y );

    $char[ 'stamina' ] = ag_meta_float( $char,
        ag_meta_type_character, AG_STAMINA );
    $char[ 'stamina_max' ] = 100.0;
    $char[ 'stamina_timestamp' ] = ag_meta_int( $char,
        ag_meta_type_character, AG_STAMINA_TIMESTAMP );

    $char[ 'gold' ] = ag_meta_int( $char,
        ag_meta_type_character, AG_GOLD );
    $char[ 'xp' ] = ag_meta_int( $char,
        ag_meta_type_character, AG_XP );

    $char[ 'wins' ] = ag_meta_int( $char,
        ag_meta_type_character, AG_WINS );
    $char[ 'losses' ] = ag_meta_int( $char,
        ag_meta_type_character, AG_LOSSES );
    $char[ 'max_damage_done' ] = ag_meta_int( $char,
        ag_meta_type_character, AG_MAX_DAMAGE_DONE );
    $char[ 'max_damage_taken' ] = ag_meta_int( $char,
        ag_meta_type_character, AG_MAX_DAMAGE_TAKEN );

    $char[ 'stats' ] = array();
    $char[ 'ability' ] = 0.0;

    foreach ( $gear_obj as $k => $v ) {
        if ( ! isset( $char[ 'meta' ][ ag_meta_type_character ] ) ) {
            continue;
        }

        $obj = json_decode( $char[ 'meta' ][ ag_meta_type_character ][ $v ],
            $assoc = TRUE );
        $char[ $k ] = $obj;

        if ( ! isset( $obj[ 'stats' ] ) ) {
            continue;
        }

        foreach ( $obj[ 'stats' ] as $stat_k => $stat_v ) {
            if ( ! isset( $char[ 'stats' ][ $stat_k ] ) ) {
                $char[ 'stats' ][ $stat_k ] = 0;
            }

            $char[ 'stats' ][ $stat_k ] += floatval( $stat_v );
            if ( 'Hoodie' != $stat_k ) {
                $char[ 'ability' ] += floatval( $stat_v );
            }
        }
    }

    $char[ 'health' ] = 10 + $char[ 'ability' ] * 2;

    return $char;
}

function ag_login() {
    global $character;

    log_add( 1, $character[ 'id' ], '' );

    $gear_obj = ag_get_gear_obj();

    // @todo: should use array_values in ensure calls (or db calls?)
    ensure_character_meta_keygroup(
        $character[ 'id' ], ag_meta_type_character, '',
        array_values( $gear_obj ) );

    ensure_character_meta_keygroup(
        $character[ 'id' ], ag_meta_type_character, '',
        array(
            AG_STAMINA, AG_GOLD, AG_XP, AG_STAMINA_TIMESTAMP,
            AG_TIP, AG_STORED_GEAR, AG_STORED_SLOT,
        ) );

    ensure_character_meta_keygroup(
        $character[ 'id' ], ag_meta_type_character, 0,
        array(
            AG_POS_X, AG_POS_Y,
            AG_WINS, AG_LOSSES, AG_MAX_DAMAGE_DONE, AG_MAX_DAMAGE_TAKEN,
            AG_TUTORIAL,
        ) );

    if ( '' == character_meta( ag_meta_type_character, AG_HOODIE ) ) {
        update_character_meta( $character[ 'id' ],
            ag_meta_type_character, AG_HOODIE,
            '{"name":"Boring Black Hoodie","stats":{"Hoodie":1} }' );
    }

    if ( '' == character_meta( ag_meta_type_character, AG_CHEST ) ) {
       update_character_meta( $character[ 'id' ],
            ag_meta_type_character, AG_CHEST,
            '{"name":"Plain Shirt","stats":{"Strength":1} }' );
    }

    if ( '' == character_meta( ag_meta_type_character, AG_LEGS ) ) {
       update_character_meta( $character[ 'id' ],
            ag_meta_type_character, AG_LEGS,
            '{"name":"Old Jeans","stats":{"Emotion":1} }' );
    }

}

add_action( 'select_character', 'ag_login' );

function ag_header() {
    global $user, $character, $game;

    if ( ! strcmp( 'title', $game->get_action() ) ) {
        return;
    }

?><!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo( GAME_NAME ); ?> (<?php echo( $game->get_action() );
        ?>)</title>
    <link rel="stylesheet" href="<?php echo( GAME_CUSTOM_STYLE_URL );
        ?>bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo( GAME_CUSTOM_STYLE_URL );
        ?>hoodie.css">
    <link href="http://fonts.googleapis.com/css?family=Raleway:400,500"
          rel="stylesheet" type="text/css">
    <link href='http://fonts.googleapis.com/css?family=Oswald:700'
          rel='stylesheet' type='text/css'>
  </head>
  <body>
    <div id="popup" class="invis"></div>
    <div class="navbar navbar-default navbar-fixed-top" role="navigation">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle"
                  data-toggle="collapse" data-target=".navbar-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="<?php echo( GAME_URL ); ?>"><?php
              echo( GAME_NAME ); ?></a>
        </div>
<?php

    if ( FALSE != $character ) {

        add_heartbeat();

?>
        <div class="collapse navbar-collapse">
          <ul class="nav navbar-nav">
            <li class="dropdown">
              <a href="#" class="dropdown-toggle"
                 data-toggle="dropdown">Character <b class="caret"></b></a>
              <ul class="dropdown-menu">
                <li><a href="?action=profile">Profile</a></li>
                <li><a href="?action=achievements">Achievements</a></li>
                <li class="divider">
                <li><a href="?action=online">Characters Online</a></li>
              </ul>
            </li>
            <li class="dropdown">
              <a href="#" class="dropdown-toggle"
                 data-toggle="dropdown">Map <b class="caret"></b></a>
              <ul class="dropdown-menu">
                <li><a href="?action=map">Current Location</a></li>
                <li class="divider">
                <li><a href="?action=combat">Combat</a></li>
                <li><a href="?action=boss">Boss Battles</a></li>
                <li><a href="?action=vendor">Vendor</a></li>
              </ul>
            </li>
            <li class="dropdown">
              <a href="#" class="dropdown-toggle"
                 data-toggle="dropdown">About <b class="caret"></b></a>
              <ul class="dropdown-menu">
                <li><a href="?action=about">About Hoodiequest</a></li>
                <li class="divider">
                <li><a href="?action=upgrade">Upgrade Hoodie</a></li>
              </ul>
            </li>
          </ul>
          <ul class="nav navbar-nav navbar-right">
            <li><a href="game-logout.php">Log out</a></li>
          </ul>
        </div>
<?php
    }
?>
      </div>
    </div>

    <div class="container">
<?php
}

function ag_footer() {
    global $character, $game;

    if ( ! strcmp( 'title', $game->get_action() ) ) {
        return;
    }

?>

    </div>
    <script src="<?php echo( GAME_CUSTOM_STYLE_URL );
        ?>popup.js"></script>
    <script src="<?php echo( GAME_CUSTOM_STYLE_URL );
        ?>jquery.min.js"></script>
    <script src="<?php echo( GAME_CUSTOM_STYLE_URL );
        ?>bootstrap.min.js"></script>
  </body>
</html>
<?php
}

add_action( 'game_header', 'ag_header' );
add_action( 'game_footer', 'ag_footer' );



function ag_about() {
    global $game;

    if ( strcmp( 'about', $game->get_action() ) ) {
       return;
    }

?>
<div class="row text-right">
  <h1 class="page_section">About</h1>
</div>
<div class="row	text-left">
  <h2>Who made this?</h2>
  <p><?php echo( GAME_NAME ); ?> was made by
<a href="https://twitter.com/scotchfield">@scotchfield</a>
(<a href="https://github.com/scotchfield">github</a>). Hello!</p>
  <p>The map artwork is built using sprites from the
<a href="http://oryxdesignlab.com/product-sprites/lofi-fantasy-sprite-set">Lo-fi Fantasy Sprite Set</a> from
<a href="http://oryxdesignlab.com/">Oryx Design Lab</a>. Go buy sprite packs
and make stuff!
  <h2>Why?</h2>
  <p>I was inspired by a hoodie! Check <a href="?action=upgrade">this
other page</a> for the story.
  <h2>What's up with the name?</h2>
  <p>The game was called Hoodiecraft for a couple of weeks, because I
used to play a lot of Warcraft. This has less war and more hoodie.
Watch for the sequel, World of Hoodiequest, coming April 5th, 1993!</p>
  <h2>What's up with all the stats?</h2>
  <p>That's a great question! I hope it makes you smile. :)</p>
<?php
}

function ag_upgrade() {
    global $game;

    if ( strcmp( 'upgrade', $game->get_action() ) ) {
       return;
    }

?>
<div class="row text-right">
  <h1 class="page_section">Upgrade Hoodie</h1>
</div>
<div class="row text-left">
  <h2>After all, this was inspired by a Hoodie!</h2>
  <p>Hello, and thank you for playing! <?php echo( GAME_NAME ); ?> is
designed to be a fun free-to-play
experiment. Not the standard <i>free-to-play</i> free-to-play, though,
where you play for free for a little while, and then realize how it would
feel kind of nice to pay a couple of bucks to recover some time. Before
you know it, you're living on the street, your garden is a wreck, the car's
been towed, and some other person is married to your spouse. Nobody
wants that kind of free-to-play.</p>
  <p>This whole thing started with two goals in mind. First, test out
the <a href="https://github.com/scotchfield/arcadia">open source web-based
gaming platform</a> I've been building, called Arcadia. Second, to help
offset the cost of a
<a href="http://www.galloree.com/Shops/Electronic-Super-Store--906/index.php">really sweet hoodie</a> I ordered.
If I meet both of those goals, I'm basically the happiest guy this side of
Toronto.</p>
  <p>If you want to support my quest for a sweet hoodie, please feel free
to use the completely optional donate button located below. If I reach
$68.40USD in donations, the project will have been a complete and total
success. And if I reach $136.80USD in donations, I'll ship a hoodie to
<a href="https://twitter.com/thegamedesigner">Michael Todd</a>, who created
the sweet hoodie in the first place. Oh, and I'll open source this game
immediately, just like Arcadia, so that you can build hoodie games of
your own. :)</p>
  <p>A one-time donation of at least one dollar will award you
the prestigious
<a class="legendary" href="#" onmouseover="popup('<span class=&quot;item_name&quot;>Epic Red Hoodie</span><hr><span class=&quot;legendary&quot;>Legendary Quality</span><br><span>Hoodie: <b>100</b><br></span>')" onmouseout="popout()">Epic Red Hoodie</a>, with a Hoodie stat of 100. The only thing it
does, like all hoodies, is increase your stamina refresh rate, up to double
the standard rate. Rest assured, you will never find a sweeter hoodie.</p>
  <p>And this is purely optional--this item does not affect how powerful
you are, how well you can defeat the bosses, your damage, your defense,
or any other real aspect of gameplay in any way. It's the same as the
most powerful hoodie that can drop in the game. If you're patient enough,
then you don't even need to think about this as an option, unless you
just want to say thanks. (If that's the case, just send me a greeting on
Twitter, and that's more than enough!)</p>

</div>
<div class="row text-center">
  <h2>Gear: <a class="legendary" href="#" onmouseover="popup('<span class=&quot;item_name&quot;>Epic Red Hoodie</span><hr><span class=&quot;legendary&quot;>Legendary Quality</span><br><span>Hoodie: <b>100</b><br></span>')" onmouseout="popout()">Epic Red Hoodie</a></h2>
  <h2>Cost: One Buck. $1. A Loonie.</h2>
</div>
<div class="row text-center">

<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="hosted_button_id" value="WK6GBT2TWCCSQ">
<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_buynowCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
</form>

</div>
<div class="row text-left" style="padding-top:16px;">
  <p>I love you for reading this far. Go play
<a href="http://store.steampowered.com/app/244870/">Electronic Super Joy</a>,
then go star and fork the Arcadia project on github. Then go hug a friend,
or pet a cat. Do something nice, and have a wonderful day!</p>
</div>
<div class="row text-right" style="padding-top:16px;">
  <p>(and if you just want the hoodie without paying the buck, just
  <a href="<?php echo( GAME_URL ); ?>?action=thank_you">click here</a>!)</p>
</div>
<?php
}

add_action( 'do_page_content', 'ag_about' );
add_action( 'do_page_content', 'ag_upgrade' );


function ag_regen_stamina() {
    global $character;

    if ( FALSE == $character ) {
        return;
    }

    $stamina = character_meta_float(
        ag_meta_type_character, AG_STAMINA );

    if ( $stamina < 100 ) {
        $stamina_boost = 1.0;
        if ( isset( $character[ 'stats' ][ 'Hoodie' ] ) ) {
            $hoodie = intval( $character[ 'stats' ][ 'Hoodie' ] );
            $hoodie = max( 0, min( 100, $hoodie ) );
            $stamina_boost += ( $hoodie / 100.0 ) ;
        }

        $stamina_seconds = time() - character_meta_int(
            ag_meta_type_character, AG_STAMINA_TIMESTAMP );
        $stamina_gain = $stamina_boost * ( $stamina_seconds / 120.0 );

        $new_stamina = min( 100, $stamina + $stamina_gain );
        update_character_meta( $character[ 'id' ], ag_meta_type_character,
            AG_STAMINA, $new_stamina );

        $character[ 'stamina' ] = $new_stamina;
    }

    update_character_meta( $character[ 'id' ], ag_meta_type_character,
        AG_STAMINA_TIMESTAMP, time() );
}

add_action( 'character_load', 'ag_unpack_character' );
add_action( 'character_load', 'ag_regen_stamina' );

function ag_tip_print() {
    global $character;

    if ( FALSE == $character ) {
        return;
    }

    $tip = character_meta( ag_meta_type_character, AG_TIP );

    if ( 0 < strlen( $tip ) ) {
        echo( '<p class="tip">' . $tip . '</p>' );
        update_character_meta( $character[ 'id' ], ag_meta_type_character,
            AG_TIP, '' );
    }
}

add_action_priority( 'do_page_content', 'ag_tip_print' );

function ag_gear_string( $item ) {
    if ( '' == $item ) {
        $item = array( 'name' => 'Nothing', 'stats' => array() );
    }

    if ( ! isset( $item[ 'rarity' ] ) ) {
        $item[ 'rarity' ] = 1;
    }

    $rarity_obj = array(
        5 => 'legendary', 4 => 'epic', 3 => 'rare',
        2 => 'uncommon', 1 => 'common',
    );

    $st = '<a class="' . $rarity_obj[ $item[ 'rarity' ] ] .
          '" href="#" onmouseover="popup(\'' .
          '<span class=&quot;item_name&quot;>' . $item[ 'name' ] .
          '</span><hr>';

    if ( 5 == $item[ 'rarity' ] ) {
        $st = $st . '<span class=&quot;legendary&quot;>' .
              'Legendary Quality</span><br><span>';
    } else if ( 4 == $item[ 'rarity' ] ) {
        $st = $st . '<span class=&quot;epic&quot;>' .
              'Epic Quality</span><br><span>';
    } else if ( 3 == $item[ 'rarity' ] ) {
        $st = $st . '<span class=&quot;rare&quot;>' .
              'Rare Quality</span><br><span>';
    } else if ( 2 == $item[ 'rarity' ] ) {
        $st = $st . '<span class=&quot;uncommon&quot;>' .
              'Uncommon Quality</span><br><span>';
    } else {
        $st = $st . '<span class=&quot;common&quot;>' .
              'Common Quality</span><br><span>';
    }

    if ( isset( $item[ 'stats' ] ) ) {
        foreach ( $item[ 'stats' ] as $k => $v ) {
            $st = $st . $k . ': <b>' . $v . '</b><br>';
        }
    }
    $st = $st . '</span>\')" onmouseout="popout()" class="item">' .
          $item[ 'name' ] . '</a>';
    return $st;
}

function ag_validate_user( $args ) {
    if ( ! isset( $args[ 'user_id' ] ) ) {
        return;
    }

    set_user_max_characters( $args[ 'user_id' ], 1 );
}

add_action( 'validate_user', 'ag_validate_user' );

function ag_get_gear( $slot, $level ) {
    $rare_rand = mt_rand( 1, 100 );
    if ( $rare_rand <= 1 ) {
        $rarity = 4;
        $level += 5;
    } else if ( $rare_rand <= 8 ) {
        $rarity = 3;
        $level += 2;
    } else if ( $rare_rand <= 80 ) {
        $rarity = 2;
    } else {
        $rarity = 1;
    }

    $prefix_obj = array(
        'Glowing ', 'Absorbing ', 'Petulant ', 'Stinky ', 'Crumbling ', 'Ex-',
    );

    $stat_obj = array(
        'Strength', 'Dexterity', 'Intelligence', 'Wisdom',
        'Constitution', 'Charisma', 'Appearance', 'Power',
        'Size', 'Sanity', 'Education', 'Idea', 'Luck', 'Knowledge',
        'Versatility', 'Gumption', 'Savvy', 'Tastiness', 'Verisimilitude',
        'Green Ranger', 'Art', 'Coffee', 'Aesthetics', 'Body', 'Might',
        'Brawn', 'Endurance', 'Vitality', 'Agility', 'Reflexes',
        'Speed', 'Intellect', 'Brains', 'Z-Factor', 'Knowledge',
        'Charm', 'Anti-Charm', 'Stench', 'Social', 'Psychic', 'Wits',
        'Ego', 'Id', 'Super-Ego', 'Cautiousness', 'Fate', 'Luck',
        'Chance', 'Gambling', 'Handwriting', 'Ambidexterity',
        'Volume', 'Social Media', 'Animal Magnetism', 'Fresh Breath',
        'Flexibility', 'Woe', 'Antifragility', 'Thickness', 'Static',
        'Page Count', 'Brightness', 'Shadow', 'Resolution', 'Hair',
        'Viscosity', 'Upbringing', 'Definition', 'Cubism', 'Comfort',
    );

    $adj_obj = array(
        'Cromulence', 'Embiggening', 'Glowing Force', 'Titanism',
    );
    $adj_obj = array_merge( $adj_obj, $stat_obj );

    $verb_obj = array(
        'Throwing', 'Standing', 'Accepting', 'Adding', 'Advising', 'Alerting',
        'Annoying', 'Apologising', 'Attracting', 'Avoiding', 'Baking',
        'Balancing', 'Bathing', 'Behaving', 'Blinding', 'Blushing', 'Boiling',
        'Boring', 'Borrowing', 'Boxing', 'Bubbling', 'Calculating',
        'Camping', 'Changing', 'Carving', 'Chewing', 'Chopping', 'Coaching',
        'Confusing', 'Continuing', 'Copying', 'Covering', 'Coughing',
        'Curving', 'Damaging', 'Decaying', 'Deceiving', 'Describing',
        'Detecting', 'Disagreeing', 'Disappearing', 'Discovering', 'Dreaming',
        'Drowning', 'Educating', 'Embarrassing', 'Encouraging', 'Entertaining',
        'Excusing', 'Exploding', 'Failing', 'Fading', 'Fencing', 'Fetching',
        'Flapping', 'Floating', 'Flashing', 'Flooding', 'Folding',
        'Frightening', 'Gathering', 'Gluing', 'Grabbing', 'Greeting',
        'Gripping', 'Groaning', 'Guaranteeing', 'Hammering', 'Harassing',
        'Harming', 'Healing', 'Heating', 'Hooking', 'Hurrying', 'Ignoring',
        'Imagining', 'Influencing', 'Instructing', 'Interrupting', 'Itching',
        'Jamming', 'Jogging', 'Joking', 'Juggling', 'Kicking', 'Kissing',
        'Knitting', 'Laughing', 'Launching', 'Learning', 'Listening',
        'Marching', 'Marrying', 'Measuring', 'Memorising', 'Mining',
        'Multiplying', 'Naming', 'Nesting', 'Noticing', 'Obeying', 'Objecting',
        'Offending', 'Overflowing', 'Paddling', 'Painting', 'Pausing',
        'Performing', 'Pinching', 'Planting', 'Pointing', 'Practising',
        'Preparing', 'Preserving', 'Pretending', 'Programming', 'Pulling',
        'Questioning', 'Queueing', 'Racing', 'Radiating', 'Recording',
        'Reducing', 'Refusing', 'Remembering', 'Removing', 'Reporting',
        'Rhyming', 'Sailing', 'Searching', 'Shaving', 'Skiing', 'Smoking',
        'Snoring', 'Snowing', 'Subtracting', 'Talking', 'Telephoning',
        'Terrifying', 'Tumbling', 'Uniting', 'Unpacking', 'Untidying',
        'Vanishing', 'Visiting', 'Waiting', 'Wandering', 'Warning',
        'Winking', 'Whistling', 'Wrapping', 'X-Raying', 'Yawning', 'Zooming'
    );

    $slot_obj = array(
        AG_HOODIE => array( 'Hoodie of {adj}' ),
        AG_WEAPON => array( 'Blade of {adj}', 'Dagger of {adj}',
            '{verb} Sword' ),
        AG_HEAD => array( 'Helm of {adj}', 'Crown of {adj}' ),
        AG_CHEST => array( 'Chestpiece of {adj}', 'Torso of {adj}' ),
        AG_LEGS => array( '{verb} Pants', '{verb} Leggings' ),
        AG_HANDS => array( 'Gloves of {adj}' ),
        AG_FEET => array( 'Boots of {adj}' ),
        AG_EYES => array( 'Spectacles of {adj}' ),
        AG_FINGERS => array( 'Ring of {adj}' ),
        AG_TOES => array( 'Toe of {adj}' ),
        AG_NOSE => array( '{adj} Ring' ),
        AG_NECK => array( 'Necklace of {adj}' ),
        AG_WRISTS => array( 'Bracelet of {adj}' ),
    );

    if ( ! isset( $slot_obj[ $slot ] ) ) {
        return '';
    }

    $x = $slot_obj[ $slot ][ mt_rand( 0, count( $slot_obj[ $slot ] ) - 1 ) ];

    $x = str_replace( '{adj}',
                      $adj_obj[ mt_rand( 0, count( $adj_obj ) - 1 ) ], $x );
    $x = str_replace( '{verb}',
                      $verb_obj[ mt_rand( 0, count( $verb_obj ) - 1 ) ], $x );

    if ( mt_rand( 1, 10 ) < 8 ) {
        $x = $prefix_obj[ mt_rand( 0, count( $prefix_obj ) - 1 ) ] . $x;
    }

    $stats = array();
    while ( $level > 0 ) {
        $stat_add = mt_rand( 1, $level );
        $level -= $stat_add;
        $stat_name = $stat_obj[ mt_rand( 0, count( $stat_obj ) - 1 ) ];

        if ( ! isset( $stats[ $stat_name ] ) ) {
            $stats[ $stat_name ] = 0;
        }
        $stats[ $stat_name ] += $stat_add;
    }

    $x_obj = array(
        'name' => $x,
        'stats' => $stats,
        'slot' => $slot,
        'rarity' => $rarity,
    );

    return $x_obj;
}

function ag_tip( $st ) {
    global $character;

    update_character_meta( $character[ 'id' ], ag_meta_type_character,
        AG_TIP, $st );
}

function ag_st( $st ) {
    return $st;
}

function ag_xy_seed( $x, $y ) {
    mt_srand( $x );
    mt_srand( mt_rand() + $y );
}

function ag_achievement_print( $args ) {
    global $character;

    if ( ! isset( $args[ 'achievement_id' ] ) ) {
        return;
    }

    $achievement = get_achievement( $args[ 'achievement_id' ] );
    $meta = json_decode( $achievement[ 'meta_value' ], TRUE );
?>
<div class="row text-center alert">
  <h2>You have completed a new achievement!</h2>
  <h3><?php echo( $meta[ 'name' ] ); ?></h3>
  <h4><?php echo( $meta[ 'text' ] ); ?></h4>
</div>
<?php
}

add_action( 'award_achievement', 'ag_achievement_print' );


function ag_thank_you() {
    global $character, $game;

    if ( strcmp( 'thank_you', $game->get_action() ) ) {
        return;
    }

    if ( FALSE == $character ) {
        return;
    }

    update_character_meta( $character[ 'id' ],
        ag_meta_type_character, AG_HOODIE,
        '{"name":"Epic Red Hoodie","stats":{"Hoodie":100},"rarity":"5"}' );
?>
<div class="row text-right">
  <h1 class="page_section">Thank You</h1>
</div>
<div class="row text-center">
  <h2>Holy smokes! Thanks!</h2>
  <p>You've got a brand new hoodie attached to your character now. It'll
keep you warm and cozy, while giving you a slight boost to your stamina
recovery.</p>
  <p>Thanks for supporting the game! Be sure to check out the
<a href="https://github.com/scotchfield/arcadia">Arcadia project</a> on
Github, if that's your sort of thing, and enjoy the rest of the game!</p>
  <h3>Most of all, have fun!</h3>
<?php
}

add_action( 'do_page_content', 'ag_thank_you' );

function ag_online() {
    global $game;

    if ( strcmp( 'online', $game->get_action() ) ) {
        return;
    }

    $char_obj = get_heartbeat_characters( 300 );
?>
<div class="row text-right">
  <h1 class="page_section">Characters Online</h1>
</div>
<div class="row text-center">
<?php
    foreach ( $char_obj as $char ) {
        echo( '<h3><a href="?action=char&id=' . $char[ 'id' ] .
              '">' . $char[ 'character_name' ] . '</a></h3>' );
    }
?>
</div>
<?php
}

add_action( 'do_page_content', 'ag_online' );
