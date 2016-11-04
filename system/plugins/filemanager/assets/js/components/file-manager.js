(function() {

    var FileManager = {
        template: '#file-manager',

        created: function() {
            console.log('created');
        },

        ready: function () {

            console.log('ready');
        },

        methods: {

        }
    };

    Vue.component('file-manager', FileManager);

}());