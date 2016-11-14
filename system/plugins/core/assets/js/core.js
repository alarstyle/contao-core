(function(){

	var REQUEST_TOKEN = '';


	var CancelToken = axios.CancelToken;


	var ResponsePromise = function(axios) {
		this.cancel = function() {

		};
	};


	var grow = {

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
		},

		action: function(actionName, data) {
			data = data ? data : {};

			data = _.defaults({'ACTION': actionName}, data);

			return grow.post(window.location.pathname, data);
		},

		initApp: function() {
			var Vue = window.ExtendedVue || window.Vue;

			var appData = _.defaults({initialized: false}, APP_DATA);

			new Vue({

				el: '#app',

				data: function() {
					return appData;
				},

				methods: {

					showModal: function(id) {

					}

				},

				created: function () {
					this.initialized = true;
				}

			});
		}

	};

	window.grow = grow;

}());