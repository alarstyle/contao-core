(function () {

    var FileManager = {

        template: '#rc-filemanager-template',

        props: {
            root: {type: String},
            path: {type: String},
            baseUrl: {type: String}
        },

        data: function () {
            return {
                currentPath: null,
                items: [],
                upload: {},
                selected: [],
                preview: null,
                search: '',
                isToParentVisible: false,
                isToParentSelected: false,
                isLoading: false
            }
        },

        computed: {

            breadcrumbs: function () {
                var parts = [],
                    currentPath = '';
                _.forEach(this.currentPath.split('/'), function (value) {
                    if (value === '') return;
                    currentPath += '/' + value;
                    parts.push({
                        name: value,
                        path: currentPath
                    });
                });
                return parts;
            },

            folders: function () {
                return _.filter(this.items, {'mime': 'application/folder'});
            },

            files: function () {
                //return _.filter(this.items, { 'mime': 'application/file' });
                return _.filter(this.items, function (item) {
                    return item.mime !== 'application/folder';
                });
            }

        },

        watch: {

            selected: function (items) {
                console.log(items.length);
                this.$emit('selected', items);
            },

            currentPath: function (newPath) {
                this.load()
            }

        },

        methods: {

            load: function () {
                this.isLoading = true;
                var _this = this;
                return grow.action('filemanagerList', {path: this.currentPath})
                    .then(function (response) {
                            var itemsArr = [];
                            _.forEach(response.data.data.items, function (item, i) {
                                item['url'] = _this.baseUrl + item['path'];
                                if (item['mime'] !== 'application/folder' && item['name'].match(/\.(jpg|jpeg|png|gif)$/i)) {
                                    item['isImage'] = true;
                                }
                                itemsArr.push(item);
                            });
                            _this.items = itemsArr;
                            _this.selected = [];
                            _this.isToParentSelected = false;
                            _this.isToParentVisible = _this.currentPath !== _this.root;
                            console.log(_this.isToParentVisible);
                        }, function () {
                            alert('Error loading data');
                        }
                    );
            },

            selectToParent: function () {
                this.isToParentSelected = true;
                this.selected = [];
            },

            openParentFolder: function () {
                this.currentPath = this.currentPath.split('/').slice(0, -1).join('/');
            },

            selectItem: function (path) {
                this.isToParentSelected = false;
                //this.selected.push(path);
                this.selected = [path];
            },

            setPath: function (path) {
                this.currentPath = path;
            },

            test: function () {
                console.log('test');
            },

            isSelected: function (file) {
                return this.selected.indexOf(file) !== -1;
            },

            onUploadChange: function (e) {
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

            upload: function (files) {
                var data = new FormData();
                data.append('action', 'upload');
                data.append('upload_path', this.currentPath);
                _.forEach(files, function (file, i) {
                    data.append('files[' + i + ']', file);
                });
                var config = {
                    onUploadProgress: function (progressEvent) {
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
            },

            newFolder: function () {
                this.$refs.newFolderModal.open();
                this.$refs.newFolderInput.focus();
            },

            newFolderCancel: function () {
                this.$refs.newFolderModal.close();
            },

            newFolderOk: function () {
                var folderName = this.$refs.newFolderInput.value.trim();
                if (folderName === '') return;
                var _this = this;
                grow.action('filemanagerNewFolder', {path: this.currentPath, folderName: folderName})
                    .then(function (response) {
                            if (response.data.success) {
                                _this.load();
                                _this.$refs.newFolderModal.close();
                            }
                            else if (response.data.errorData) {
                                alert(response.data.errorData[0]);
                            }
                            else {
                                alert('Unknown error');
                            }
                        }, function () {
                            alert('Error loading data');
                        }
                    );
            },

            renameFile: function() {
                console.log('rename')
            },

            deleteFile: function() {
                if (!confirm('Are you sure?')) return;
                var _this = this,
                    path = this.selected[0].path;
                grow.action('filemanagerDelete', {path: path})
                    .then(function (response) {
                            if (response.data.success) {
                                _this.load();
                            }
                            else if (response.data.errorData) {
                                alert(response.data.errorData[0]);
                            }
                            else {
                                alert('Unknown error');
                            }
                        }, function () {
                            alert('Error loading data');
                        }
                    );
            }

        },

        created: function () {
            console.log('created');

            window.addEventListener('keydown', function (e) {

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