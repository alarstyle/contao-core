(function () {

    var REQUEST_TOKEN = '';

    var app;


    var CancelToken = axios.CancelToken;


    var ResponsePromise = function (axios) {
        this.cancel = function () {

        };
    };


    window.scrollTo = function (domElement, offset, ifNotInViewport) {
        if (!domElement) return;
        if (isNaN(offset)) offset = 0;

        var top = domElement.offsetTop + offset;

        if (ifNotInViewport && top + 20 > window.pageYOffset && top - 20 < window.pageYOffset + window.innerHeight) {
            return;
        }

        document.documentElement.scrollTop = document.body.scrollTop = top;
    };


    window.AbstractUnit = {
        props: {
            id: String,
            label: String,
            hint: String,
            error: {type: String, default: null},
            required: {type: Boolean, default: false},
            value: {},
            config: {}
        },

        data: function () {
            return {
                currentValue: null
            }
        },

        computed: {
            unitClass: function () {
                var c = this.config.class ? [this.config.class] : [];
                if (this.error) {
                    c.push('unit--error');
                }
                return c;
            }
        },

        watch: {
            value: function (value) {
                console.log('!!!!');
                this.currentValue = value;
            },
            currentValue: function(currentValue) {
                this.$emit('change', currentValue, this);
            }
        },

        methods: {
            reset: function() {
                var _this = this;
                this.currentValue = 'reseting_' + Date.now();
                Vue.nextTick(function() {
                    _this.currentValue = _this.value;
                });
            }
        },

        mounted: function() {
            this.currentValue = this.value;
        }
    };


    var grow = {

        get: function (url, config) {
            config = config ? config : {};
            config.params = _.defaults({'REQUEST_TOKEN': Contao.request_token}, config.params);
            config.headers = _.defaults({'X-Requested-With': 'XMLHttpRequest'}, config.headers);
            return axios.get(url, config);
        },

        post: function (url, data, config) {
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

        action: function (actionName, data, config) {
            data = data ? data : {};

            if (data.append) {
                data.append('ACTION', actionName);
            } else {
                data = _.defaults({'ACTION': actionName}, data);
            }

            return grow.post(window.location.pathname, data, config);
        },

        initApp: function () {
            var Vue = window.ExtendedVue || window.Vue;

            var appData = _.defaults({initialized: false}, APP_DATA);

            app = new Vue({

                el: '#app',

                data: function () {
                    return appData;
                },

                methods: {

                    showModal: function (id) {

                    }

                },

                created: function () {
                    this.initialized = true;
                }

            });
        }

    };



    var notifyDefault = {
        type: 'info'
    };

    grow.notify = function (message, settings) {
        if (!app) return;

        app.$emit('notify', _.defaults({message: message}, settings, notifyDefault));
    };


    window.grow = grow;

}());