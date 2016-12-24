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
            error: {
                type: String,
                default: null
            },
            required: {
                type: Boolean,
                default: false
            },
            value: {},
            config: {
                type: [Array, Object],
                default: function() {return {}}
            }
        },

        data: function () {
            return {
                currentValue: null
            }
        },

        computed: {
            unitClass: function () {
                var c = this.config.class ? [this.config.class] : [];
                c.push('id_' + this.id);
                if (this.error) {
                    c.push('unit--error');
                }
                return c;
            }
        },

        watch: {
            value: {
                handler: function (value) {
                    this.currentValue = value;
                },
                deep: true
            },
            currentValue: {
                handler: function (currentValue) {
                    this.$emit('change', currentValue, this);
                },
                deep: true
            }
        },

        methods: {
            softReset: function() {
                this.currentValue = this.value;
            },
            reset: function () {
                var _this = this;
                this.currentValue = 'reseting_' + Date.now();
                Vue.nextTick(function () {
                    _this.currentValue = _this.value;
                });
            }
        },

        mounted: function () {
            this.currentValue = this.value;
        }
    };


    window.AbstractAction = {

    };


    window.AbstractApp = {

        data: function () {
            return {
                unsaved: false,
                locked: false
            }
        },

        watch: {
            unsaved: function (isChanged) {
                if (isChanged) {
                    window.addEventListener("beforeunload", this.beforeunload);
                }
                else {
                    window.removeEventListener("beforeunload", this.beforeunload);
                }
            }
        },

        methods: {

            beforeunload: function (e) {
                var confirmationMessage = "\o/";
                e.returnValue = confirmationMessage;     // Gecko, Trident, Chrome 34+
                return confirmationMessage;              // Gecko, WebKit, Chrome <34
            },

            action: function(actionName, data, config) {
                var _this = this,
                    promise = grow.action(actionName, data, config);
                promise.catch(function(error) {
                    alert(error);
                    _this.locked = false;
                });
                return promise;
            },

            confirmExit: function (successCallback) {
                var confirmExit = this.$refs.confirmExit;
                confirmExit.$on('ok', function() {
                    confirmExit.$off('ok');
                    confirmExit.$off('cancel');
                    successCallback && successCallback();
                });
                confirmExit.$on('cancel', function() {
                    confirmExit.$off('ok');
                    confirmExit.$off('cancel');
                });
                confirmExit.open();
            },

            confirmExitIfUnsaved: function (successCallback) {
                if (!this.unsaved) {
                    successCallback && successCallback();
                    return;
                }
                this.confirmExit(successCallback);
            },

            confirmDelete: function (successCallback) {
                var confirmDelete = this.$refs.confirmDelete;
                confirmDelete.$on('ok', function() {
                    confirmDelete.$off('ok');
                    confirmDelete.$off('cancel');
                    successCallback && successCallback();
                });
                confirmDelete.$on('cancel', function() {
                    confirmDelete.$off('ok');
                    confirmDelete.$off('cancel');
                });
                confirmDelete.open();
            }

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
            var App = APP_DATA['jsAppClassName'] ? window[APP_DATA['jsAppClassName']] : window.ExtendedVue || window.Vue.extend(AbstractApp);

            var appData = _.defaults({initialized: false}, APP_DATA);

            app = new App({

                el: '#app',

                data: function () {
                    return appData;
                },

                methods: {

                    showModal: function (id) {

                    }

                },

                mounted: function () {
                    var _this = this;
                    window.Vue.nextTick(function() {
                        _this.initialized = true;
                    });
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