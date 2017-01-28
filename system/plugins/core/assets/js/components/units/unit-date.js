(function () {

    var UnitDate = {

        extends: AbstractUnit,

        template: '#unit-date-template',

        data: function () {
            return {
                fp: null
            }
        },

        computed: {
            microtime: function() {
                if (!this.currentValue) {
                    return null;
                }
                return parseInt(this.currentValue + '000');
            }
        },

        methods: {

            handleDateChange: function(selectedDates, dateStr) {
                if (!selectedDates.length) {
                    this.currentValue = '';
                }
                else {
                    this.currentValue = Math.floor(selectedDates[0].getTime() / 1000);
                }
                this.$emit('change', this.currentValue, this);
            }

        },

        mounted: function () {
            this.fp = new Flatpickr(this.$refs.input, {
                static: true,
                enableTime: this.config['enableTime'] || false,
                allowInput: true,
                defaultDate: this.microtime,
                onChange: this.handleDateChange
            });
        },

        destroyed: function () {
            this.fp.destroy()
        }

    };

    Vue.component('unit-date', UnitDate);

}());