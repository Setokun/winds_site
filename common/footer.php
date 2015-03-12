	</div>
        <footer class="row">
        <div class="col-sm-12">
          Pied de page
        </div>
      </footer>
    </div>
    <script src="js/jquery-2.1.1.js"></script>
    <script>
        // targeting navigation

var n = document.getElementById('navigation');
var m = $('#menu');

// nav initially closed is JS enabled

n.classList.add('is-closed');

function navi() {

// when small screen, create a switch button, and toggle navigation class

if (window.matchMedia("(max-width: 767px)").matches && document.getElementById("toggle-nav")==undefined) {
	n.insertAdjacentHTML('afterBegin','<button id="toggle-nav"><i class="glyphicon glyphicon-align-justify"></i></button>');
	t = $('#toggle-nav');
	console.log(t);
	//t.onclick=function(){ n.classList.toggle('is-closed');}
	t.click(function(){
		/*if(m.css('display') == "none"){
			m.slideDown().;
			m.css('display', 'initial');}
		else{
			m.slideUp(400, m.css('display', 'none'));
			;
		}*/
		m.slideToggle().done(function(){
			m.css('display',
				(m.css('display') == 'initial')? 'none': 'initial'
				)});
	});
}

// when big screen, delete switch button, and toggle navigation class
if (window.matchMedia("(min-width: 768px)").matches && $("#toggle-nav")) {
		document.getElementById("toggle-nav").outerHTML="";
		m.css('display','initial');
	}
}

navi();

// when resize or orientation change, reload function

window.addEventListener('resize', navi);
		
    </script>
    </body>
</html>