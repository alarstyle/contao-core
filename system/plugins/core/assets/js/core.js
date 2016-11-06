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
			config.params = _.defaults({'REQUEST_TOKEN': Contao.request_token}, config.params);
			config.headers = _.defaults({'X-Requested-With': 'XMLHttpRequest'}, config.headers);
			return axios.get(url, config);
		},

		post: function(url, data, config) {
			config = config ? config : {};
			data = data ? data : {};
			config.headers = _.defaults({'X-Requested-With': 'XMLHttpRequest'}, config.headers);

			if (data.append) {
				data.append('REQUEST_TOKEN', Contao.request_token);
			} else {
				data = _.defaults({'REQUEST_TOKEN': Contao.request_token}, data);
			}

			console.log(data);

			return axios.post(url, data, config);
		}

	};

	window.Raccoon = Raccoon;

}());