<html>
  <head>
    <title>Twitter Puzzle :: Result</title>
    <style type="text/css">
      .focus {
        position: relative;
        height: 24em;
        width: 24em;
        left: 24em;
        background-size: cover;
        list-style-type: none;
        margin: 100px;
        padding: 0;
      }

      .focus li {
        position: absolute;
        top: 50%;
        margin-top: -25px;
        height: 50px;
      }

      .focus li img {
        height: 100px;
        width: 100px;
        border-radius: 100px;
        border: solid thin black;
        overflow: hidden;
      }
      a {
        padding-left: 50px;
        padding-top: 10px;
        font-size: 20px;
      }

      
    </style>
    <script type="text/javascript">
    function getTransformProperty(element) {
	    // Note that in some versions of IE9 it is critical that
	    // msTransform appear in this list before MozTransform
	    var properties = [
	        'transform',
	        'WebkitTransform',
	        'msTransform',
	        'MozTransform',
	        'OTransform'
	    ];
	    var p;
	    while (p = properties.shift()) {
	        if (typeof element.style[p] != 'undefined') {
	            return p;
	        }
	    }
	    return false;
	}
    function myFunction()
    {
      deg = -90
      // alert("rotate("+ deg +"deg) translate(12em) rotate("+ -deg +"deg)");
      for (var i = 0; i < document.getElementById("images").childNodes.length; i++) {
        var ele = document.getElementById("img" + i);
        var property = getTransformProperty(ele);
        ele.style[property] = "rotate("+ deg +"deg) translate(12em) rotate("+ -deg +"deg)";
        deg+=36;
      };
    }
    </script>
  </head>
  <body onload="myFunction()">
    <ul class="focus" id="images">
      <li>
        <img src="<?php echo $userImage; ?>"  />
      </li>
      <?php
      $i = 0;
      foreach ($users as $user) { ?>
        <li id="img<?php echo $i++; ?>">  <!-- TO DO -->
          <img src="<?php echo $user->profile_image  ?>" title="<?php echo $user->count  ?>" />
        </li>
      <?php
      $deg = $deg + 36;
      } ?>
    </ul>
    <a href="http://multunus-twitter.redatomstudios.com/puzzle.php"><input type="button" value="Try another handle"/></a>
  
  </body>
</html>