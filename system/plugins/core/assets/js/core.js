(function(){

	var REQUEST_TOKEN = '';


	var CancelToken = axios.CancelToken;


	var ResponsePromise = function(axios) {
		this.cancel = function() {

		};
	};


	var Raccoon = {

		get: function(url, config) {
			config = config ? config : {};
			config.params = _.defaults({r_token: 'ahh'}, config.params);
			config.headers = _.defaults({'X-Requested-With': 'XMLHttpRequest'}, config.headers);
			return axios.get(url, config);
		},

		post: function(url, data, config) {
			config = config ? config : {};
			config.params = _.defaults({r_token: 'ahh'}, config.params);
			config.headers = _.defaults({'X-Requested-With': 'XMLHttpRequest'}, config.headers);
			return axios.post(url, data, config);
		}

	};

	window.Raccoon = Raccoon;

}());