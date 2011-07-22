function pua_ws(url){

    return {

	'url' : url,
	'tries': 0,

	'init' : function(){

	    var _this = this;

	    $.ajax({
		    'url': this.url, 
		    'success' : function(rsp){

			try {
			    rsp = JSON.parse(rsp);

			    if (rsp['stat'] != 'ok'){
				pua_set_text('foiled ' + rsp['code'] + ' times by flickr');
				return;
			    }

			    _this.connect(rsp);
			}

			catch(e){
			    pua_set_text('this is why we can\'t have nice things');
			}
		    },

		    'error' : function(e){
			pua_set_text('unable to phone home on line ' + e.code);
		    }
		});
	},

        'connect' : function(ws_url){

	    var _this = this;
	    var ws = null;

	    try {
		ws = new WebSocket(ws_url);
	    }

	    catch (e){
		pua_set_text('websocket has a size ' + e.code + ' migraine');
		return;
	    }

	    ws.onopen = function(e){
		_this.tries = 0;
	    };

	    ws.onerror = function(e){
		pua_set_text('websocket threw up in aisle ' + e.code);
	    };

	    ws.onclose = function(e){

		if (_this.tries > 10){
		    pua_set_text('websocket go boom!');
		    return;
		}

		var delay = 5000 * tries;

		setTimeout(function(){
			_this.tries += 1;
			_this.connect(ws_url);
		}, delay);
	    };

	    ws.onmessage = function(e){

		try {
		    var rsp = JSON.parse(e.data);
		    // do something with rsp here
		    // call pua_show_photo()
		}

		catch(e){}
	    };
	}
    }

}