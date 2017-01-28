(function() {

    var Form = {

        template: '#gr-form-template',

        props: {
            fields: {
                type: [Object, Array],
                default: function() {
                    return {};
                }
            },
            errors: {
                type: Object,
                default: {}
            }
        },

        data: function() {
            return {
                isChanged: false,
                doNotEmitUpdate: true,
                changedFields: {}
            }
        },

        watch: {
            fields: {
                handler: function (fields) {
                    this.doNotEmitUpdate = true;
                    if (this.$refs.units) {
                        for (var i=0; i < this.$refs.units.length; i++) {
                            this.$refs.units[i].softReset();
                        }
                    }
                    var _this = this;
                    Vue.nextTick(function() {
                        _this.doNotEmitUpdate = false;
                    });
                },
                deep: true
            },
            isChanged: function(isChanged) {
                this.$root.unsaved = isChanged;
            }
        },

        methods: {

            unitChange: function(value, unit) {
                console.log('AAAA');
                console.log(unit);
                if (this.doNotEmitUpdate) return;
                this.isChanged = true;
                this.changedFields[unit.id] = value;
                this.$emit('change', value, unit, this);
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
                _this.doNotEmitUpdate = false;
            });
        },

        beforeDestroy: function () {
            this.isChanged = false;
            this.doNotEmitUpdate = false;
            this.$root.unsaved = false;
        }

    };

    Vue.component('gr-form', Form);

}());