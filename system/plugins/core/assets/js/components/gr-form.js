(function() {

    var Form = {

        template: '#gr-form-template',

        props: {
            fields: {
                type: Object,
                default: {}
            },
            errors: {
                type: Object,
                default: {}
            }
        },

        data: function() {
            return {
                isChanged: false,
                changedFields: {}
            }
        },

        watch: {
            fields: function(fields) {
                console.log('FILEDS CHANGED')
            }
        },

        methods: {

            unitChange: function(value, unit) {
                console.log('unit change ', value, unit.id);
                this.isChanged = true;
                this.changedFields[unit.id] = value;
            },

            getValues: function() {
                var values = {};

                for (var fieldName in this.fields) {
                    values[fieldName] = this.changedFields[fieldName] !== undefined ? this.changedFields[fieldName] : this.fields[fieldName].value;
                }

                return values;
            }

        }

    };

    Vue.component('gr-form', Form);

}());