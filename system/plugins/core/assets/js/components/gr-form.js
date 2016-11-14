(function() {

    var Form = {

        template: '#gr-form-template',

        props: {
            fields: {type: Array, default: []}
        },

        data: function() {
            return {
                models: {}
            }
        },

        watch: {
            fields: function(newFileds) {
                console.log('fields changed');
            }
        },

        mounted: function() {

        },

        methods: {

            onChange: function(e) {
                console.log(e);
            },

            isValuesChanges: function() {
                return true;
            }

        }

    };

    Vue.component('gr-form', Form);

}());