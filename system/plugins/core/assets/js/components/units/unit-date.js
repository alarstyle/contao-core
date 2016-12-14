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
                this.currentValue = Math.floor(selectedDates[0].getTime() / 1000);
                console.log('!!!!');
                console.log(this.currentValue);
            }

        },

        mounted: function () {
            console.log(this.microtime);
            console.log(new Date(this.microtime));
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