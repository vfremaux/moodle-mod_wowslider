<?php
header("contentType: text/javascript\n\n");

require("../../../config.php");

$wid = required_param('wid', PARAM_INT);

if (!$wowslider = $DB->get_record('wowslider', array('id' => $wid))) {
    die;
}

?>
//***********************************************
// Obfuscated by Javascript Obfuscator
// http://javascript-source.com
//***********************************************
jQuery("#wowslider-container1").wowSlider({
        effect:"<?php echo $wowslider->effect ?>",
        prev:"",
        next:"",
        duration:<?php echo $wowslider->slideduration ?>*100,
        delay:<?php echo $wowslider->delay ?>*100,
        width:"<?php echo $wowslider->width ?>",
        height:"<?php echo $wowslider->height ?>",
        autoPlay:<?php echo ($wowslider->autoplay) ? 'true' : 'false' ?>,
        autoPlayVideo:<?php echo ($wowslider->autoplayvideo) ? 'true' : 'false' ?>,
        playPause:true,
        stopOnHover:<?php echo ($wowslider->stoponhover) ? 'true' : 'false' ?>,
        loop:<?php echo ($wowslider->playloop) ? 'true' : 'false' ?>,
        bullets:<?php echo $wowslider->bullets ?>,
        caption:<?php echo ($wowslider->caption) ? 'true' : 'false' ?>,  
        captionEffect:"parallax",
        controls:<?php echo ($wowslider->controls) ? 'true' : 'false' ?>,
        responsive:1,
        fullScreen:<?php echo ($wowslider->fullscreen) ? 'true' : 'false' ?>,
        gestures:2,
        onBeforeStep:0,
        images:0});