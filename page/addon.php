<?php
include_once "../common/header.php";
include_once "../common/banner.php";
include_once "../common/menu.php";
require_once "../core/config.php";
?>

<section style="padding-bottom:20px" class="col-sm-9 col-md-10">

    <div id="upload-new-addon">
        <article class="col-xs-12 ">
            <form method="post" class="table table-bordered">
                <table>
                    <tr>
                        <th class="col-xs-12">Upload a new addon</th>
                    </tr>
                    <tr>
                        <td>
                            <div style="margin-top: 10px" class="col-xs-12 col-md-5">
                                <div class="col-xs-4 col-sm-3"><label for="addon-name">Name</label></div>
                                <div class="col-xs-8 col-sm-9"><input name="addon-name"/></div>
                            </div>
                            <div style="margin-top: 10px" class="col-xs-12 col-md-7">
                                <div class="col-xs-4 col-sm-3"><label for="addon-description">Description</label></div>
                                <div class="col-xs-8 col-sm-9"><input name="addon-description"/></div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div style="margin-top: 10px" class="col-xs-12 col-md-5">
                                <div class="col-xs-4 col-sm-3">
                                    <label for="addon-name" style="text-align: right;">Type :</label>
                                </div>
                                <div class="col-xs-8 col-sm-9">
                                    <select id="addon-type">
                                        <option value="-1">Type of addon</option>
                                        <option value="theme">theme</option>
                                        <option value="level">level</option>
                                    </select>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        
                        <td>
                            <div style="margin-top: 10px" class="col-xs-12">
                                <div class="col-xs-4 col-sm-3 col-md-2">
                                    <button class="button-blue" style="width: 100px;" type="button">select a file</button></span>
                                </div>
                                <div class="col-xs-8 col-sm-9 col-md-10">
                                    <label>file path</label>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <button style="margin-top: 10px; margin-bottom: 10px;" class="button-green col-xs-12 col-sm-offset-5 col-sm-2" type="button">Upload</button>
                        </td>
                    </tr>
                </table>
            </form>
        </article>
        
        <article class="col-xs-12">
            <table id="news-forum" class="table table-bordered">
                <tr><th colspan="3">Available custom levels</th></tr>
                <tr>
                    <td style="vertical-align: middle" width="25"><input disabled type="checkbox" name="checkbox1" checked="checked" value="checkbox"></td>
                    <td style="vertical-align: middle" width="50"><img src="../resources/logo-honey.png" class="theme-image" /></td>
                    <td style="vertical-align: middle">Custom level 1 by Anonymus</td>
                </tr>
                <tr>
                    <td style="vertical-align: middle" width="25"><input disabled type="checkbox" name="checkbox1" checked="checked" value="checkbox"></td>
                    <td style="vertical-align: middle" width="50"><img src="../resources/logo-ronces.png" class="theme-image" /></td>
                    <td style="vertical-align: middle">Custom level 2 by Invisible Man</td>
                </tr>
                <tr>
                    <td style="vertical-align: middle" width="25"><input disabled type="checkbox" name="checkbox1" value="checkbox"></td>
                    <td style="vertical-align: middle" width="50"><img src="../resources/logo-ice.png" class="theme-image" /></td>
                    <td style="vertical-align: middle">Custom level 3 by Roger Rabbit</td>
                </tr>
            </table>
        </article>
    </div>
</section>

<?php
include "../common/footer.php";
?>