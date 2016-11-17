(function() {

    var Form = {

        template: '#gr-form-template',

        props: {
            fields: {type: Array, default: []}
        },

        data: function() {
            return {
                changedFields: {}
            }
        },

        watch: {
            fields: function() {
                console.log('changed');
            }
        },

        methods: {

            onChange: function(value, unit) {
                this.changedFields[unit.id] = value;
            },

            getData: function() {
                return this.changedFields;
            },

            showErrors: function(errorData) {
                _.forEach(this.fields, function(field, i) {
                    //if (!errorData[field.name]) return;
                    //console.log(field.name);
                    //field.error = 'true';
                });
            }

        }

    };

    Vue.component('gr-form', Form);

}());