<?php

global $ag;

define( 'TUTORIAL_BIT_COMPLETE', 0 );

function ag_tutorial_check() {
    global $ag;

    if ( FALSE == $ag->char ) {
        return;
    }

    $t = character_meta( ag_meta_type_character, AG_TUTORIAL );
    if ( ! get_bit( $t, TUTORIAL_BIT_COMPLETE ) ) {
        $ag->set_state( 'tutorial' );
    }
}

$ag->add_state( 'state_set', FALSE, 'ag_tutorial_check' );

function ag_tutorial_print() {
    global $ag;

    if ( ! strcmp( 'tutorial', $ag->get_state() ) ) {
        $t = character_meta( ag_meta_type_character, AG_TUTORIAL );

        if ( ! get_bit( $t, 1 ) ) {
?>
<div class="row text-right">
  <h1 class="page_section">Tutorial</h1>
</div>
<div class="row text-center">
  <h2>It's a cold world out there.</h2>
  <p class="lead">You should grab a hoodie.</p>
  <h2>(<a href="game-setting.php?setting=tutorial&amp;status=1">Tell me
      more..</a>)</h2>
</div>
<?php
        } else if ( ! get_bit( $t, 2 ) ) {
?>
<div class="row text-right">
  <h1 class="page_section">Tutorial</h1>
</div>
<div class="row text-center">
  <h2>Hoodiequest is a game about gear.</h2>
  <p class="lead">There are lots of heroes in this cold, cold world of ours.
We're all tough, and we can all hold our own in combat.</p>
  <p class="lead"><b>The only thing that makes us stronger in this frozen
land is our gear.</b></p>
  <h2>(<a href="game-setting.php?setting=tutorial&amp;status=2">Say, that
     looks like a comfortable hoodie you've got there..</a>)</h2>
<?php
        } else if ( ! get_bit( $t, 3 ) ) {
?>
<div class="row text-right">
  <h1 class="page_section">Tutorial</h1>
</div>
<div class="row text-center">
  <h2>Gear, gear, gear.</h2>
  <p class="lead">Click on the Character menu at the top of the screen to
view detailed information about yourself. You can look below this screen
to get an idea of what you look like. Sure, there's not much to look at now,
but you'll grow stronger once you manage to find some delicious gear.</p>
  <p class="lead"><b>Your health and attack power improve with better
gear.</b></p>
  <h2>(<a href="game-setting.php?setting=tutorial&amp;status=3">Is that
me? What else can you tell me?</a>)</h2>
</div>

<?php

            ag_print_character( $ag->char );

        } else if ( ! get_bit( $t, 4 ) ) {
?>
<div class="row text-right">
  <h1 class="page_section">Tutorial</h1>
</div>
<div class="row text-center">
  <h2>This world is seething with evil.</h2>
  <p class="lead">You've got a Map option at the top of the screen. Your
current location indicates your place in the world. Everything kind of looks
the same here, so we keep track of our location using numbers. You start
at home, position (0, 0).</p>
  <p class="lead">There's monsters all over this frozen landscape. Take
care of some of 'em, would ya? Did I mention that they usually drop gear?
Click the Combat link in the Map menu, and you'll fight a random baddie.</p>
  <p class="lead">Be careful though. The further you go from home, the more
difficult your foes will be.</p>
  <p class="lead"><b>Combat is easier when you're closer to (0, 0).</b></p>
  <h2>(<a href="game-setting.php?setting=tutorial&amp;status=4">Okay, defeat
baddies to get gear. Can I go fight some monsters now?</a>)</h2>
<?php

            ag_draw_map( 0, 0 );

        } else if ( ! get_bit( $t, 5 ) ) {
?>
<div class="row text-right">
  <h1 class="page_section">Tutorial</h1>
</div>
<div class="row text-center">
  <h2>Gear is great. Did I already say that?</h2>
  <p class="lead">You'll encounter a lot of gear while adventuring. The
only thing that matters is how much that gear makes you better. There are
lots of attributes that your gear can enhance. From Strength to Wisdom,
from Versatility to Handwriting, they're all important.</p>
  <p class="lead">Some gear will be common, like that
<a class="common" href="#" onmouseover="popup('<span class=&quot;item_name&quot;>Plain Shirt</span><hr><span class=&quot;common&quot;>Common Quality</span><br><span>Strength: <b>1</b><br></span>')" onmouseout="popout()">Plain Shirt</a>
you've got on now. Hover your mouse over top of it, or click on it, and
take a look.</p>
  <p class="lead">From time to time, you'll discover gear of different
qualities. From uncommon goods like the
<a class="uncommon" href="#" onmouseover="popup('<span class=&quot;item_name&quot;>Crown of Sanity</span><hr><span class=&quot;uncommon&quot;>Uncommon Quality</span><br><span>Resolution: <b>4</b><br>Charm: <b>4</b><br>Handwriting: <b>1</b><br>Page Count: <b>7</b><br>Chance: <b>1</b><br></span>')" onmouseout="popout()">Crown of Sanity</a>,
the rare quality
<a class="rare" href="#" onmouseover="popup('<span class=&quot;item_name&quot;>Stinky Blade of Brawn</span><hr><span class=&quot;rare&quot;>Rare Quality</span><br><span>Luck: <b>9</b><br>Social Media: <b>3</b><br></span>')" onmouseout="popout()">Stinky Blade of Brawn</a>,
up to epic tier stuff like the
<a class="epic" href="#" onmouseover="popup('<span class=&quot;item_name&quot;>Advising Sword</span><hr><span class=&quot;epic&quot;>Epic Quality</span><br><span>Cautiousness: <b>8</b><br>Appearance: <b>7</b><br></span>')" onmouseout="popout()">Advising Sword</a>,
all gear is special, and all gear is unique.
  <h2>(<a href="game-setting.php?setting=tutorial&amp;status=5">Alright,
I think I'm picking up what you're putting down.</a>)</h2>
<?php
        } else if ( ! get_bit( $t, 6 ) ) {
?>
<div class="row text-right">
  <h1 class="page_section">Tutorial</h1>
</div>
<div class="row text-center">
  <h2>Stamina lets you adventure.</h2>
  <p class="lead">One last thing. If you keep walking around and fighting
all day, you're going to get tired. Stamina is a limited resource. When it's
at zero, you need to wait before you can take action again.</p>
  <p class="lead">The only way to get stamina back is to wait. The only
thing that makes waitin' go faster is having a nice soft hoodie on your
body. In this cold land, sometimes all you can do is cozy into a nice warm
hoodie to get back on your feet.</p>
  <p class="lead"><b>Your stamina automatically refills over time, but
replenishes more quickly with better hoodies.</b></p>

  <h2>(<a href="game-setting.php?setting=tutorial&amp;status=6">Okay, I've
got it! Let me at 'em.</a>)</h2>
<?php
        } else if ( ! get_bit( $t, 7 ) ) {
?>
<div class="row text-right">
  <h1 class="page_section">Tutorial</h1>
</div>
<div class="row text-center">
  <h2>Alright, get out there!</h2>
  <p class="lead">Best of luck out there, adventurer!</p>
  <h2>(<a href="?state=profile">Take me to my character page!</a>)</h2>
<?php
            update_character_meta( $ag->char[ 'id' ], ag_meta_type_character,
                AG_TUTORIAL,
                set_bit( $t, TUTORIAL_BIT_COMPLETE ) ) ;
        } else {
            /* This is bad!  Clear the tutorial to be safe. */
            //update_character_meta( $ag->char[ 'id' ], ag_meta_type_character,
            //    AG_TUTORIAL,
            //    set_bit( $t, TUTORIAL_BIT_COMPLETE ) ) ;
        }
    }
}

$ag->add_state( 'do_page_content', FALSE, 'ag_tutorial_print' );


function ag_tutorial_setting( $args ) {
    if ( ! isset( $args[ 'status' ] ) ) {
        return;
    }

    $bit = intval( $args[ 'status' ] );
    if ( ( $bit < 0 ) || ( $bit > 15 ) ) {
        return;
    }

    global $ag;

    $t = character_meta( ag_meta_type_character, AG_TUTORIAL );
    ensure_character_meta( $ag->char[ 'id' ], ag_meta_type_character,
        AG_TUTORIAL );
    update_character_meta( $ag->char[ 'id' ], ag_meta_type_character,
        AG_TUTORIAL, set_bit( $t, $bit ) );
}

$custom_setting_map[ 'tutorial' ] = 'ag_tutorial_setting';
