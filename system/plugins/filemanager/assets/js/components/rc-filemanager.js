(function() {

    var FileManager = {

        template: '#rc-filemanager-template',

        props: {
            root: {type: String, default: '/'},
            path: String
        },

        data: function() {
            return {
                currentPath: null,
                items: [],
                upload: {},
                selected: [],
                preview: null,
                search: ''
            }
        },

        computed: {

            breadcrumbs: function() {
                var parts = [],
                    currentPath = '';
                _.forEach(this.currentPath.split('/'), function(value) {
                    if (value === '') return;
                    currentPath += '/' + value;
                    parts.push({
                        name: value,
                        path: currentPath
                    });
                });
                return parts;
            },

            folders: function() {
                return _.filter(this.items, { 'mime': 'application/folder' });
            },

            files: function() {
                return _.filter(this.items, { 'mime': 'application/file' });
            }

        },

        watch: {

            selected: function(items) {
                console.log(items.length);
                this.$emit('selected', items);
            },

            currentPath: function(newPath) {
                this.load()
            }

        },

        methods: {

            load: function () {
                var component = this;
                return grow.action('filemanagerList', {path: this.currentPath})
                    .then(function (response) {
                        component.items = response.data.data.items || [];
                        component.selected = [];
                    }, function () {
                        alert('Error loading data');
                    }
                );
            },

            selectItem: function(path) {
                //this.selected.push(path);
                this.selected = [path];
            },

            setPath: function(path) {
                this.currentPath = path;
            },

            test: function() {
                console.log('test');
            },

            isSelected: function(path) {
                return this.selected.indexOf(path.toString()) !== -1;
            },

            onUploadChange: function(e) {
                console.log('change');

                var files = e.target.files || e.dataTransfer.files;
                console.log(files);
                if (!files.length)
                    return;

                var input = e.target,
                    parent = input.parentNode;

                input.removeEventListener('change', this.onUploadChange, true);

                var newInput = input.cloneNode(false);

                newInput.value = '';
                newInput.addEventListener('change', this.onUploadChange, true);

                // console.log(input.files);
                // console.log(newInput.files);
                //
                // if (!window.oldInputs) window.oldInputs = [];
                // window.oldInputs.push(input);
                // window.newInput = newInput;

                parent.removeChild(input);
                parent.appendChild(newInput);

                console.log(files);

                this.upload(files);
            },

            upload: function(files) {
                var data = new FormData();
                data.append('action', 'upload');
                data.append('upload_path', this.currentPath);
                _.forEach(files, function(file, i) {
                    data.append('files[' + i + ']', file);
                });
                var config = {
                    onUploadProgress: function(progressEvent) {
                        var percentCompleted = progressEvent.loaded / progressEvent.total;
                        console.log(percentCompleted);
                    }
                };
                grow.post('/contao/filemanager', data, config)
                    .then(function (res) {
                        console.log(res);
                        //output.className = 'container';
                        //output.innerHTML = res.data;
                    })
                    .catch(function (err) {
                        console.log(err);
                        alert(err);
                        // output.className = 'container text-danger';
                        // output.innerHTML = err.message;
                    });
            }

        },

        created: function() {
            console.log('created');

            window.addEventListener('keydown', function(e) {

            });

            this.currentPath = this.path;

            this.$watch('path', function (path) {
                console.log('wwww');
                this.currentPath = this.path;
                //this.$session.set('finder.' + this.root + '.path', path);
            });
        },

        mounted: function () {
            this.$el.querySelector('.upload_input').addEventListener('change', this.onUploadChange, true);
        }

    };

    Vue.component('rc-filemanager', FileManager);

    //Vue.partial('pickup', '<span class="pickup">{{description}}</span>');

}());