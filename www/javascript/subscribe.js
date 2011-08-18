
var subscribe_geo_geocoded = {};

function subscribe_geo_geocode(){

	var wr = $('#sub_geocode_wrapper');
	wr.hide();

	var where = prompt('where');

	if (! where){
		return;
	}

	if (subscribe_geo_geocoded[where]){
		subscribe_geo_display(subscribe_geo_geocoded[where]);
		return;
	}	

	var url = 'http://api.flickr.com/services/rest/?method=flickr.places.find&format=json&nojsoncallback=1';
	url += '&api_key=4d5ab73de9b0ea433491a45a24187503';
	url += '&query=' + where;

	$.ajax({
		'url' : url,
		'error' : function(){
			alert('Hrmph... there was an unknown error geocoding your query.');
		},
		'success' : function(rsp){

			try {
				rsp = JSON.parse(rsp);
			}

			catch(e){
				alert('Ack! It looks like Flickr sent back bunk data for your query.');
				return;
			}

			if (rsp['stat'] != 'ok'){
				alert('Sad face. The Flickr API returned an error...');
				return;
			}

			subscribe_geo_geocoded[where] = rsp;
			subscribe_geo_display(rsp);
		}
	});
}

function subscribe_geo_display(rsp){

	var count = rsp['places']['place'].length;
	var results = new Array();

	for (var i=0; i < count; i++){
		var name = rsp['places']['place'][i]['_content'];
		var woeid = rsp['places']['place'][i]['woeid'];
		var html = '<li><a href="#" onclick="subscribe_geo_set_woeid('+ woeid + ');return false;">' + name + '</a></li>';
		results.push(html);
	}

	$('#sub_geocode_results').html(results.join(''));

	var wr = $('#sub_geocode_wrapper');
	wr.show();
}

function subscribe_geo_set_woeid(woeid){

	var current = $('#woeids').val();
	var ids = (current) ? current.split(',') : new Array();

	var count = ids.length;

	for (var i=0; i < count; i++){
		if (ids[i] == woeid){
			return;
		}
	}

	ids.push(woeid);
	$("#woeids").val(ids.join(','));

	var wr = $('#sub_geocode_wrapper');
	wr.hide();
}
