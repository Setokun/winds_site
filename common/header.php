<?php
/**
 * Description of header file
 * @author Damien.D & Stephane.G
 */
 
/**
 * File used at first in all pages which interact with the users.
 */

require_once "../core/config.php";
?>

<!DOCTYPE HTML>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <meta name="viewport" content="width=device-width" />
        <link href="../css/bootstrap.css" rel="stylesheet">
        <link href="../css/style.css" rel="stylesheet">
        <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet">
        <title>Winds</title>
        <script src="../js/jquery.js"></script>
        <script src="../js/jquery-2.1.1.js"></script>
        <script src="../js/bootstrap.min.js"></script>
        <script>
        $(document).ready(function(){
            $("#logout").click(function(){
                $.post("session.php", {logout: true} );
            });
        });
        </script>
    </head>
    <body>
    <?php
        if( !ManagerDB::availableDB() ){
            include_once "../common/banner.php"; ?>
            <div class="container">
                <section style="padding:20px" class="col-sm-9 col-md-10">
                    <div class="mainbox col-xs-12 col-sm-6 col-sm-offset-3 col-lg-offset-3 col-lg-8" >
                        <div class="panel panel-info">
                            <div class="panel-heading">
                                <div class="panel-title">Unavailable database</div>
                            </div>
                            <div class="panel-body form-horizontal" >
                                <div class="form-group col-md-12" >
                                    <h4>The Winds team apologizes for any inconvenience caused.</h4>
                                    <p>We try to fix it as soon as possible.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </section><?php
            include "../common/footer.php";
            die;
        } ?>