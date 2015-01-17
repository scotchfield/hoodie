<?php

class HQTitle {

    public $ag;

    public function __construct( $ag ) {
        $ag->add_state( 'do_page_content', 'title',
            array( $this, 'title_content' ) );

        $this->ag = $ag;
    }

    public function title_content() {
        if ( FALSE != $this->ag->char ) {
            header( 'Location: game-logout.php' );
            exit;
        }

?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo( GAME_NAME ); ?></title>
    <link rel="stylesheet" href="<?php echo( GAME_CUSTOM_STYLE_URL );
        ?>bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo( GAME_CUSTOM_STYLE_URL );
        ?>hoodie.css">
    <link href="http://fonts.googleapis.com/css?family=Raleway:400,500"
          rel="stylesheet" type="text/css">
  </head>
  <body>
<div id="popup" class="invis"></div>
<div class="container">

  <div class="row">
    <div class="col-md-8 text-left">
      <img src="<?php echo( GAME_CUSTOM_STYLE_URL ); ?>hoodiequest.png"
           width="80%">
    </div>
    <div class="col-md-4">

      <form class="form-horizontal" role="form" name="login_form"
            id="login_form" method="post" action="game-login.php">
        <div class="form-group">
          <label for="login_user"
                 class="col-sm-4 control-label">Username</label>
          <div class="col-sm-8">
            <input class="form-control input-sm" name="user"
                   id="login_user" value="" type="text">
          </div>
        </div>
        <div class="form-group">
          <label for="login_pass"
                 class="col-sm-4 control-label">Password</label>
          <div class="col-sm-8">
            <input class="form-control" name="pass"
                   id="login_pass" value="" type="password">
          </div>
        </div>
        <div class="text-right">
          <button type="submit" class="btn btn-sm btn-default">Log in!</button>
        </div>
        <input type="hidden" name="state" value="login">
      </form>

    </div>
  </div>

<?php
    $err_obj = array(
        1 => 'Please provide a username.',
        2 => 'Please provide a password.',
        3 => 'Please provide a valid email address.',
        4 => 'That username already exists.',
        5 => 'That email address is already in use.',
        6 => 'That username and password combination does not exist.',
        100 => 'Thanks! Please check your email for a validation link.',
        101 => 'That account is already validated!',
        102 => 'Success! You can now log in.',
    );

    if ( isset( $_GET[ 'notify' ] ) ) {
        $notify = intval( $_GET[ 'notify' ] );
        if ( isset( $err_obj[ $notify ] ) ) {
            echo( '<div class="row text-center alert"><h2>' .
                  $err_obj[ $notify ] . '</h2></div>' );
        }
    }
?>

  <div class="row text-center">

    <div class="col-xs-3"></div>

    <div class="col-xs-6">

    <h3>Battle foes. Get loot. Take off every zig.</h3>
    <p>Hoodiequest is a free browser-based role playing game. Your mission,
should you choose to accept it, is to fight your way up from nothing to
become one of the greatest warriors in the land.</p>

<div><h3>Collect strange and powerful gear.</h3>
<p>Gear is procedurally generated, so your weapons and armour will be
unique. From the <a class="uncommon" href="#" onmouseover="popup('<span class=&quot;item_name&quot;>Absorbing Helm of Page Count</span><hr><span class=&quot;uncommon&quot;>Uncommon Quality</span><br><span>Thickness: <b>2</b><br>Savvy: <b>13</b><br>Power: <b>7</b><br></span>')" onmouseout="popout()">Absorbing Helm of Page Count</a>, the rare-quality
<a class="rare" href="#" onmouseover="popup('<span class=&quot;item_name&quot;>Chestpiece of Antifragility</span><hr><span class=&quot;rare&quot;>Rare Quality</span><br><span>Sanity: <b>46</b><br>Anti-Charm: <b>24</b><br>Charm: <b>5</b><br>Might: <b>2</b><br>Reflexes: <b>1</b><br></span>')" onmouseout="popout()">Chestpiece of Antifragility</a>, or even the epic 
<a class="epic" href="#" onmouseover="popup('<span class=&quot;item_name&quot;>Crumbling Throwing Pants</span><hr><span class=&quot;epic&quot;>Epic Quality</span><br><span>Vitality: <b>67</b><br>Z-Factor: <b>1</b><br>Tastiness: <b>56</b><br>Sanity: <b>26</b><br>Flexibility: <b>1</b><br>Stench: <b>9</b><br></span>')" onmouseout="popout()">Crumbling Throwing Pants</a>,
your gear will grant power, allowing you to venture farther out into the
dangerous realm.</p>
<h3>Search for the Golden Hoodie</h3>
<p>Although nobody knows if the tales are true, rumours of a legendary
hooded garment made of woven gold are spreading across the land. Will you
be the adventurer to find it?</p>
</div>

    </div>

    <div class="col-xs-3"></div>

  <div>
  <div class="row">

    <div class="col-md-6 text-left">
      <h3>Register for a free account</h3>

      <form class="form-horizontal" name="register_form" id="register_form"
            method="post" action="game-login.php">
        <div class="form-group">
          <label for="register_user"
                 class="col-sm-4 control-label">Username</label>
          <div class="col-sm-8">
            <input class="form-control input-sm" name="user"
                   id="register_user" value="" type="text">
          </div>
        </div>
        <div class="form-group">
          <label for="register_pass"
                 class="col-sm-4 control-label">Password</label>
          <div class="col-sm-8">
            <input class="form-control" name="pass"
                   id="register_pass" value="" type="password">
          </div>
        </div>
        <div class="form-group">
          <label for="register_email"
                 class="col-sm-4 control-label">Email</label>
          <div class="col-sm-8">
            <input class="form-control" name="email"
                   id="register_email" value="" type="text">
          </div>
        </div>
        <div class="text-right">
          <button type="submit"
                  class="btn btn-sm btn-default">Register</button>
        </div>
        <input type="hidden" name="state" value="register">
      </form>

    </div>

    <div class="col-md-2"></div>

    <div class="col-md-4 text-right">
      <h4>Fun, and free!</h4>
      <p>Hoodiequest is maintained by
<a href="https://twitter.com/scotchfield">@scotchfield</a>
(<a href="https://github.com/scotchfield">github</a>), and built using
the <a href="https://github.com/scotchfield/arcadia">Arcadia open-source web game engine</a>.</p>
<p>The map artwork is built using sprites from the
<a href="http://oryxdesignlab.com/product-sprites/lofi-fantasy-sprite-set">Lo-fi Fantasy Sprite Set</a> from <a href="http://oryxdesignlab.com/">Oryx Design Lab</a>.</p>
    </div>
  </div>

</div>

<script src="<?php echo GAME_CUSTOM_STYLE_URL; ?>popup.js"></script>
<script src="<?php echo GAME_CUSTOM_STYLE_URL; ?>jquery.min.js"></script>
<script src="<?php echo GAME_CUSTOM_STYLE_URL; ?>bootstrap.min.js"></script>

</body>
</html>

<?php
    }

}
