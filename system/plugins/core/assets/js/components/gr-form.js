(function() {

    var Form = {

        template: '#gr-form-template',

        props: {
            fields: {
                type: [Object, Array],
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
            fields: {
                handler: function (fields) {
                    if (this.$refs.units) {
                        for (var i=0; i < this.$refs.units.length; i++) {
                            this.$refs.units[i].softReset();
                        }
                    }
                    var _this = this;
                    Vue.nextTick(function() {
                        _this.isChanged = false;
                    });
                },
                deep: true
            },
            isChanged: function(isChanged) {
                console.log('isChan', isChanged);
                this.$root.unsaved = isChanged;
            }
        },

        methods: {

            unitChange: function(value, unit) {
                console.log('u');
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

        },

        mounted: function() {
            var _this = this;
            Vue.nextTick(function() {
                _this.isChanged = false;
            });
        },

        beforeDestroy: function () {
            this.isChanged = false;
            this.$root.unsaved = false;
        }

    };

    Vue.component('gr-form', Form);

}());