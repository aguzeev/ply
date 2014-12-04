<?php
	$monthVars = "";
?>
<!doctype html>
<html>
<head>
<meta charset="UTF-8">
<title>Поиск самых эффективных смен</title>
<script type="text/javascript" src="../js/jquery.min.js"></script>
<script language="javascript">
$(document).ready(function(e) {
    monthVariants = [
        //'0-1',
        '1-4',
        //'3-4',
        '3-5',
        '4-1',
        //'5-7',
        '6-2',
        '6-13',
        '8-0'
    ];

    for ( var i in monthVariants ) {
        $.ajax({
            url: "getMaxes.php",
            type: "GET",
            data: {
                monthVar: monthVariants[i]
            }
        }).done(function(data) {
                    $("#out").html( $("#out").html() + data );
                });
    }


});
</script>
</head>

<body>
<div id="out"></div>
</body>
</html>
