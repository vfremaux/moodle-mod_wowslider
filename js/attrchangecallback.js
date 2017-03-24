$(function($) {

    var views = new Array();
    views.push(1);
    var msg = '';

    $(".j_bullets").attrchange({
        trackValues: true, // set to true so that the event object is updated with old & new values
        callback: function(evnt) {
            if (views.length == $(".ws_bullets").data("id")) {
                url = wwwroot+"/mod/wowslider/ajax/services.php?wsid="+wowsliderid+"&what=complete&userid="+userid;
                $.get(url, function(data){
                    $(".notif_slide").html(data);
                });
            } else if ((evnt.newValue != evnt.oldValue) && (evnt.newValue == "j_bullets ws_selbull")) {
                if (views.indexOf($(this).data("id"))== -1) {
                    msg = '';
                    views.push($(this).data("id"));
                } else {
                    // msg = 'The slide show has been seen';
                }
            }
            // $(".notif_slide").html(msg);
        }
    });

});
