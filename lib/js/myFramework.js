document.addEventListener("DOMContentLoaded", function(event) {

	document.getElementById('ham-menu').onclick = function(){
		var ham = document.getElementById('ham-menu');
		var drawer = document.getElementById('drawer');
		if(ham.className == '') {
			openMenu(drawer, ham);
		} else {
			closeMenu(drawer, ham);
		}
	};
	// window.addEventListener('mousemove', function(e){
	//     if(e.which == 1){
	//         swipeMenu(e.pageX);
	//     }
	// }, true);
	// window.addEventListener('touchmove', function(e){
	// 	swipeMenu(e.touches[0].clientX);
	// }, true);
	// window.addEventListener('touchend', function(e){
	// 	document.getElementById('drawer-indicator').style.width = '0';
	// }, true);
	window.addEventListener('scroll', function(e){
		if(!document.getElementById('drawer').classList.contains('open'))
		{
			scrollNav();
		}
	});

	var formTextElements = document.getElementsByClassName('form-element-text');
	for(var i = 0; i < formTextElements.length; i++){
		var input = formTextElements[i].getElementsByTagName('input')[0];
		inputTextContentCheck(input);
		inputTextValidityCheck(input);
		formTextElements[i].addEventListener('keyup',function(e){
			inputTextContentCheck(e.target);
			inputTextValidityCheck(e.target);
		});
		formTextElements[i].addEventListener('change',function(e){
			inputTextContentCheck(e.target);
			inputTextValidityCheck(e.target);
		});
	}
	var passwordToggles = document.getElementsByClassName('password-toggle');
	for (i = 0; i < passwordToggles.length; i++){
		passwordToggles[i].addEventListener('click', function(e){
			passwordToggle(e.target);
		})
	}

	var selects = document.getElementsByClassName('form-element-select');
	for (i = 0; i < selects.length; i++){
		inputSelectContentCheck(selects[i].getElementsByTagName('select')[0]);
		selects[i].addEventListener('change', function (e) {
			inputSelectContentCheck(e.target);
		});
	}

	var ranges = document.getElementsByClassName('form-element-range');
	for (i = 0; i < selects.length; i++){
		inputRangeValueCheck(ranges[i].getElementsByTagName('input')[0]);
		ranges[i].addEventListener('change', function (e) {
			inputRangeValueCheck(e.target);
		});
		ranges[i].addEventListener('mousemove', function (e) {
			if(e.which == 1){
				inputRangeValueCheck(e.target);
			}
		});
		ranges[i].addEventListener('touchmove', function (e) {
			inputRangeValueCheck(e.target);
		});
	}

	if(document.getElementsByClassName('hero').length > 0){
		document.body.classList.add('has-hero')
	}
});


var oldMouseX = 0;
var startMouseX = 0;
var deltaMouseX = 0;
var swipeDist = window.outerWidth*4/7;
function swipeMenu(x){
	var ham = document.getElementById('ham-menu');
	var drawer = document.getElementById('drawer');

	if(oldMouseX != 0) {
		deltaMouseX = x - oldMouseX;
	}
	oldMouseX = x;
	startMouseX += deltaMouseX;

	var swipeRatio = (Math.abs(startMouseX/swipeDist));
	if (swipeRatio > 1){
		swipeRatio = 1;
	}

	if(deltaMouseX < 0 && startMouseX < -swipeDist) {
		openMenu(drawer, ham);
		startMouseX = 0;
	} else if (deltaMouseX > 0 && startMouseX > swipeDist) {
		closeMenu(drawer, ham);
		startMouseX = 0;
	} else if (deltaMouseX < 0 && drawer.className != 'open'){
		document.getElementById('drawer-indicator').style.width = window.outerWidth/7 * swipeRatio + 'px';

	}

}

function openMenu(drawer, ham){
	drawer.className = 'open';
	ham.className = 'open';
	navbar =  document.getElementById('navbar');
	navbar.classList.add('scrolled');
	// setTimeout(function(){
	//     document.getElementById('drawer-indicator').style.width = '0';
	// }, 100);
}
function closeMenu(drawer, ham){
	drawer.className = 'close';
	ham.className = '';
	scrollNav();
	// setTimeout(function(){
	//     document.getElementById('drawer-indicator').style.width = '0';
	// }, 100);
}

function toggleCollapse(elem){
	var collapse = elem.getElementsByClassName('collapse-toggle')[0];
	if(collapse.classList.contains('collapsed')){
		collapse.classList.remove('collapsed');
	} else {
		collapse.classList.add('collapsed');
	}
}
function scrollNav(){
	var navbar = document.getElementById('navbar');
	var drawer = document.getElementById('drawer');
	if(window.scrollY > navbar.offsetHeight && drawer.className != 'open'){
		navbar.classList.add('scrolled');
	} else if (navbar.classList.contains('scrolled')){
		navbar.classList.remove('scrolled');
	}
}
function inputTextContentCheck(elem){
	if(elem.value != ''){
		elem.parentElement.classList.add('has-text');
	} else {
		elem.parentElement.classList.remove('has-text');
	}
}
function inputTextValidityCheck(elem){
	var valid = elem.checkValidity();
	if(valid && (elem.classList.contains('invalid') || elem !== document.activeElement)){
		elem.parentElement.classList.remove('invalid');
	} else if(!valid){
		elem.parentElement.classList.add('invalid');
	}
}
function passwordToggle(elem) {
	var passwordInput = elem.parentElement.getElementsByTagName('input')[0];
	if(passwordInput.getAttribute('type')=='text'){
		passwordInput.setAttribute('type', 'password');
	} else {
		passwordInput.setAttribute('type', 'text');
	}
}

function inputSelectContentCheck(elem){
	var selected = elem.options[elem.selectedIndex];
	if(selected.value != ''){
		elem.parentElement.classList.add('has-value');
	} else {
		elem.parentElement.classList.remove('has-value');
	}
}

function inputRangeValueCheck(elem){
	var valueIndicator = elem.parentElement.getElementsByClassName('range-val')[0];
	valueIndicator.innerHTML = elem.value;
}

function openModal(id){
	var modal = document.getElementById(id);
	modal.classList.remove('closed');
	modal.classList.add('open');
	window.addEventListener('keyup', function(e){
		if(e.which == 27){
			closeModal(id);
		}
	});
}
function closeModal(id){
	var modal = document.getElementById(id);
	modal.classList.remove('open');
	modal.classList.add('closed');
	window.removeEventListener('keyup', function(e){
		if(e.which == 27){
			closeModal(id);
		}
	});
}