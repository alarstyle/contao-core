(function () {

    var GrTextarea = {

        template: '#gr-textarea-template',

        props: {
            name: String,
            value: null,
            disabled: {
                type: Boolean,
                default: false
            },
            placeholder: {
                type: String,
                default: ''
            },
            autosize: {
                type: Boolean,
                default: true
            },
            minRows: {
                type: Number,
                default: 2
            },
            maxRows: {
                type: Number,
                default: 8
            }
        },

        data: function () {
            return {
                currentValue: null
            }
        },

        watch: {

            value: function(value) {
                this.currentValue = value;
            },

            currentValue: function(currentValue) {
                this.$emit('change', currentValue, this);
                this.$nextTick(function() {
                    this.resizeTextarea();
                });
            }

        },

        methods: {

            resizeTextarea: function() {
                if (!this.$refs.textarea || !this.autosize) return;
                var textarea = this.$refs.textarea,
                    styles = getComputedStyle(textarea),
                    lineHeight = parseInt(styles['line-height'], 10),
                    verticalBorder = parseInt(styles['border-top-width'], 10) + parseInt(styles['border-bottom-width'], 10),
                    verticalPadding = parseInt(styles['padding-top'], 10) + parseInt(styles['padding-bottom'], 10);

                textarea.style.height = 'auto';

                var scrollHeight = textarea.scrollHeight,
                    currentHeight = textarea.offsetHeight,
                    newHeight,
                    totalRows = Math.floor((scrollHeight - verticalPadding) / lineHeight),
                    visibleRows = Math.floor((currentHeight - verticalPadding - verticalBorder) / lineHeight);

                if (this.minRows && this.minRows > totalRows) {
                    totalRows = this.minRows;
                }
                if (this.maxRows && totalRows > this.maxRows) {
                    totalRows = this.maxRows;
                }

                if (totalRows > visibleRows) {
                    newHeight = totalRows * lineHeight + verticalBorder + verticalPadding;
                }
                else {
                    newHeight = textarea.scrollHeight + verticalBorder;
                }

                textarea.style.height = newHeight + 'px';
            },

            reset: function () {
                this.currentValue = this.value;
            }

        },

        created: function () {
            if (!this.value) return;
            this.currentValue = this.value;
        },

        mounted: function() {
            this.resizeTextarea();
        }

    };

    Vue.component('gr-textarea', GrTextarea);

}());