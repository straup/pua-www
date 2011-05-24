function pua_contacts_photos(){
	$("#nav").hide();
	$("#foot").hide();
	pua_get_contacts_photos();
}

function pua_get_contacts_photos(){

	$.ajax({
		'url' : '/photos/friends/data/',
		'success' : function(rsp){
			pua_show_contacts_photos(rsp.photos);
		}
	});
}

function pua_show_contacts_photos(photos){

	if (photos.length == 0){

		setTimeout(function(){
			pua_get_contacts_photos();
		}, 60000);

		pua_set_text('panda tickles unicorn');
		return;
	}

	var ph = photos.pop();
	pua_draw_photo(ph);

	setTimeout(function(){
		pua_show_contacts_photos(photos);
	}, 25000);

}

function pua_draw_photo(ph){

	var h = window.innerHeight;
	var w = window.innerWidth;

	var photo_id = ph.photo_id.replace("tag:flickr.com,2005:/photo/", "");
	var photo_url = ph.photo_url.replace("_x", "");

	var href = "http://www.flickr.com/photo.gne?id=" + photo_id;

	var img = "<img id=\"photo\" src=\"" + photo_url + "\" height=\"" + h + "\" width=\"" + w + "\" />";

	var html = "<a href=\"" + href + "\" target=\"_flickr\">" + img + "</a>";

	$("#photo_wrapper").html(html);

	pua_set_text(ph.title);
}

function pua_set_text(text){

	$('#message').html(text);
	$("#message_wrapper").textfill({ maxFontPixels: 500 });
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
}

window.onresize = function(){
	resize();
}
