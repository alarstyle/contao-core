(function () {

    /**
     * options = [[value, label, disabled], ...]
     */

    var GrSelect = {

        template: '#gr-select-template',

        props: {
            name: String,
            value: null,
            options: {
                type: Array,
                default: []
            },
            disabled: {
                type: Boolean,
                default: false
            },
            multiple: {
                type: Boolean,
                default: false
            },
            placeholder: {
                type: String,
                default: ''
            }
        },

        data: function () {
            return {
                opened: false,
                currentValue: null,
                currentLabel: ''
            }
        },

        watch: {

            value: function(value) {
                if (this.currentValue === value) return;
                this.currentValue = value;
                this.setCurrentLabel();
            },

            options: function(options) {
                this.setCurrentLabel();
            },

            opened: function(opened) {
                if (opened) {
                    document.documentElement.addEventListener('click', this.documentClick, false);
                }
                else {
                    document.documentElement.removeEventListener('click', this.documentClick, false);
                }
            },

            currentValue: function(currentValue) {
                this.$emit('change', currentValue, this);
            }

        },

        methods: {

            inputClick: function () {
                if (this.disabled)  return;
                this.opened = !this.opened;
            },

            optionClick: function (option) {
                if (this.disabled || option.disabled)  return;
                this.opened = false;
                this.currentLabel = option.label || option.value;
                this.currentValue = option.value;
            },

            documentClick: function(event) {
                if (this.$el.contains(event.target)) return;
                this.opened = false;
            },

            setCurrentLabel: function() {
                if (!this.value || !this.options || !this.options.length) {
                    this.currentLabel = this.value;
                }
                var _this = this;
                this.options.some(function(option) {
                    if (option.value === _this.value) {
                        _this.currentLabel = option.label;
                        return true;
                    }
                });
            }

        },

        created: function () {
            if (!this.value) return;
            this.currentValue = this.value;
            this.setCurrentLabel();
        }

    };

    Vue.component('gr-select', GrSelect);

}());