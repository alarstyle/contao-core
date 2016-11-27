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
                console.log(this.$refs.units);

                this.$forceUpdate();
                console.log('FIELDS CHANGED')
            }
        },

        methods: {

            unitChange: function(value, unit) {
                this.isChanged = true;
                this.changedFields[unit.id] = value;
            },

            getValues: function() {
                var values = {};

                for (var fieldName in this.fields) {
                    values[fieldName] = this.changedFields[fieldName] !== undefined ? this.changedFields[fieldName] : this.fields[fieldName].value;
                }

                return values;
            },

            reset: function() {
                for (var i=0; i < this.$refs.units.length; i++) {
                    this.$refs.units[i].reset();
                }
            }

        }

    };

    Vue.component('gr-form', Form);

}());