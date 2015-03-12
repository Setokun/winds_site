<?php
include_once "../common/header.php";
include_once "../common/banner.php";
include_once "../common/menu.php";
require_once "../core/config.php";
?>
<section style="padding:20px" class="col-sm-9 col-md-10">
    <article class="table-bordered col-xs-12">
        <p>Winds accounts</p>
        <div class="col-xs-12">
            <div class="account col-xs-12">
                <div class="col-xs-12 bold">
                    <h4 id="player1">Player1</h4>
                </div>
                <div id="player1">
                    <form method="post">
                        <div class="col-xs-3">Rights</div>
                        <div class="col-xs-3"><input type="radio" name="player1" value="player" />Player</div>
                        <div class="col-xs-3"><input type="radio" name="player1" value="moderator" />Moderator</div>
                        <div class="col-xs-3"><input type="radio" name="player1" value="administrator" />Administrator</div>
                    </form>
                    <div class="row">
                        <div class="col-xs-3">Actions :</div>
                        <div class="col-xs-3"><input class="button-green" type="button" value="Valid rights" /></div>
                        <div class="col-xs-3"><input class="button-red" type="button" value="Delete" /></div>
                        <div class="col-xs-3"><input class="button-orange" type="button" value="Banish" /></div>
                    </div>
                </div>
            </div>
            <div class="account col-xs-12">
                <div class="col-xs-12 bold">
                    <h4>Player2</h4>
                </div>
                <div id="player2" style="display:none">
                    <form method="post">
                        <div class="col-xs-3">Rights</div>
                        <div class="col-xs-3"><input type="radio" name="player2" value="player" />Player</div>
                        <div class="col-xs-3"><input type="radio" name="player2" value="moderator" />Moderator</div>
                        <div class="col-xs-3"><input type="radio" name="player2" value="administrator" />Administrator</div>
                    </form>
                    <div class="row">
                        <div class="col-xs-3">Actions :</div>
                        <div class="col-xs-3"><input class="button-green" type="button" value="Valid rights" /></div>
                        <div class="col-xs-3"><input class="button-red" type="button" value="Delete" /></div>
                        <div class="col-xs-3"><input class="button-orange" type="button" value="Banish" /></div>
                    </div>
                </div>
            </div>
            <div class="account col-xs-12">
                <div class="col-xs-12 bold">
                    <h4>Player3</h4>
                </div>
                <div id="player3" style="display:none">
                    <form method="post">
                        <div class="col-xs-3">Rights</div>
                        <div class="col-xs-3"><input type="radio" name="player3" value="player" />Player</div>
                        <div class="col-xs-3"><input type="radio" name="player3" value="moderator" />Moderator</div>
                        <div class="col-xs-3"><input type="radio" name="player3" value="administrator" />Administrator</div>
                    </form>
                    <div class="row">
                        <div class="col-xs-3">Actions :</div>
                        <div class="col-xs-3"><input class="button-green" type="button" value="Valid rights" /></div>
                        <div class="col-xs-3"><input class="button-red" type="button" value="Delete" /></div>
                        <div class="col-xs-3"><input class="button-orange" type="button" value="Banish" /></div>
                    </div>
                </div>
            </div>
            <div class="account col-xs-12">
                <div class="col-xs-12 bold">
                    <h4>Player4</h4>
                </div>
                <div id="player4" style="display:none">
                    <form method="post">
                        <div class="col-xs-3">Rights</div>
                        <div class="col-xs-3"><input type="radio" name="player4" value="player" />Player</div>
                        <div class="col-xs-3"><input type="radio" name="player4" value="moderator" />Moderator</div>
                        <div class="col-xs-3"><input type="radio" name="player4" value="administrator" />Administrator</div>
                    </form>
                    <div class="row">
                        <div class="col-xs-3">Actions :</div>
                        <div class="col-xs-3"><input class="button-green" type="button" value="Valid rights" /></div>
                        <div class="col-xs-3"><input class="button-red" type="button" value="Delete" /></div>
                        <div class="col-xs-3"><input class="button-orange" type="button" value="Banish" /></div>
                    </div>
                </div>
            </div>
            <div class="account col-xs-12">
                <div class="col-xs-12 bold">
                    <h4>Player5</h4>
                </div>
                <div id="player5"  style="display:none">
                    <form method="post">
                        <div class="col-xs-3">Rights</div>
                        <div class="col-xs-3"><input type="radio" name="player5" value="player" />Player</div>
                        <div class="col-xs-3"><input type="radio" name="player5" value="moderator" />Moderator</div>
                        <div class="col-xs-3"><input type="radio" name="player5" value="administrator" />Administrator</div>
                    </form>
                    <div class="row">
                        <div class="col-xs-3">Actions :</div>
                        <div class="col-xs-3"><input class="button-green" type="button" value="Valid rights" /></div>
                        <div class="col-xs-3"><input class="button-red" type="button" value="Delete" /></div>
                        <div class="col-xs-3"><input class="button-orange" type="button" value="Banish" /></div>
                    </div>
                </div>
            </div>
            
        </div>
    </article>
    <article class="table-bordered col-xs-12">
        <p>Accounts waiting deletion - <em style="font-size:12px">click on an item to focus it in the previous list</em></p>
        <div class="col-xs-12">
            <div class="col-xs-12 bold">
                <h5 id="player1">Player1</h5>
            </div>
            <div class="col-xs-12 bold">
                <h5 id="player2">Player2</h5>
            </div>
            <div class="col-xs-12 bold">
                <h5 id="player3">Player3</h5>
            </div>
            <div class="col-xs-12 bold">
                <h5 id="player4"Player4</h5>
            </div>
            <div class="col-xs-12 bold">
                <h5 id="player5">Player5</h5>
            </div>
        </div>
    </article>
</section>
<?php
include "../common/footer.php";
?>