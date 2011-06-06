// contacts photos
var is_gil = 0;

function pua_contacts_photos(){
	pua_setup();
	pua_get_contacts_photos();
}

function pua_get_contacts_photos(){

	$.ajax({
		'url' : '/photos/friends/data/',
		'success' : function(rsp){
			is_gil = rsp.is_gil;
			pua_show_contacts_photos(rsp.photos);
		}
	});
}

function pua_show_contacts_photos(photos){

	if (photos.length == 0){

		setTimeout(function(){
			pua_get_contacts_photos();
		}, 60000);

		pua_tickle();
		return;
	}

	var ph = photos.pop();

	try {
		pua_draw_photo(ph);
	}

	catch(e){
		pua_set_text('unicorn nudges panda');
	}

	setTimeout(function(){
		pua_show_contacts_photos(photos);
	}, 40000);

}

// contacts faves

function pua_contacts_faves(){
	pua_setup();
	pua_get_contacts_faves();
}

function pua_get_contacts_faves(){

	$.ajax({
		'url' : '/faves/friends/data/',
		'success' : function(rsp){
			is_gil = rsp.is_gil;
			pua_show_contacts_faves(rsp.photos);
		}
	});
}

function pua_show_contacts_faves(photos){

	if (photos.length == 0){

		setTimeout(function(){
			pua_get_contacts_faves();
		}, 60000);

		pua_tickle();
		return;
	}

	var ph = photos.pop();

	try {
		pua_draw_photo(ph);
	}

	catch(e){
		pua_set_text('unicorn nudges panda');
	}

	setTimeout(function(){
		pua_show_contacts_faves(photos);
	}, 40000);

}

// shared functions

function pua_draw_photo(ph){

	var h = window.innerHeight;
	var w = window.innerWidth;

	var photo_id = ph.photo_id.replace("tag:flickr.com,2005:/photo/", "");
	var photo_url = ph.photo_url.replace("_x", "");

	var href = "http://www.flickr.com/photo.gne?id=" + photo_id;

	var img = "<img id=\"photo\" src=\"" + photo_url + "\" height=\"" + h + "\" width=\"" + w + "\" />";

	var html = "<a href=\"" + href + "\" target=\"_flickr\">" + img + "</a>";

	$("#photo_wrapper").html(html);

	pua_set_text(ph.title, href);
}

function pua_set_text(text, href){

	if (href){
		text = "<a href=\"" + href + "\" target=\"_flickr\">" + text + "</a>";
	}

	$('#message').html(text);
	$("#message_wrapper").textfill({ maxFontPixels: 500 });
}

function pua_setup(){

	$("#nav").hide();
	$("#foot").hide();

	$(document).keyup(function(e){

		if (e.which != 61){
			return;
		}

		var display = $("#nav").css("display");
		display = (display == 'none') ? 'block' : 'none';

		$("#nav").css("display", display);
		$("#foot").css("display", display);
	});
}

function pua_tickle(){

	var text = 'panda tickles unicorn';

	if (is_gil){
		text = 'panda tickles eeeeeaaaaRRRs!!!!';
	}

	pua_set_text(text);
}

function resize(){

	var ph = $("#photo");
	if (! ph){ return; }

	var h = window.innerHeight;
	var w = window.innerWidth;

	ph.attr("height", h);
	ph.attr("width", w);

	$("#message_wrapper").textfill({ maxFontPixels: 500 });
}

function assign_wrapper_dimensions(){

	 return;

	var h = window.innerHeight;
	var w = window.innerWidth;

	$("#message_wrapper").css("max-width", (w  *.95) + 'px');
	$("#message_wrapper").css("max-height", (h * .95) + 'px');
	$("#message_wrapper").textfill({ maxFontPixels: 500 });
}

window.onload = function(){
	assign_wrapper_dimensions();

	window.onresize = function(){
		resize();
	}
}

