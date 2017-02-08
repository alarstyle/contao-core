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
                selected: [],
                preview: null,
                search: '',
                isToParentVisible: false,
                isToParentSelected: false,
                isLoading: false,

                isUploadVisible: false,
                isUploading: false,
                uploadingPercent: 0,
                filesToUpload: []
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
                this.$emit('selected', items);
            },

            currentPath: function (newPath) {
                this.load()
            },

            filesToUpload: function(filesToUpload) {

                if (filesToUpload.length == 0) {
                    this.load();
                    return;
                }

                if (this.isUploading) return;

                this.isUploadVisible = true;
                this.isUploading = true;
                
                var _this = this,
                    file = filesToUpload[0];

                console.log(this.uploadFile);

                this.uploadFile(file, function() {
                    _this.isUploading = false;
                    filesToUpload.shift();
                });
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
                var files = e.target.files || e.dataTransfer.files;

                if (!files.length)
                    return;

                var input = e.target,
                    parent = input.parentNode;

                input.removeEventListener('change', this.onUploadChange, true);

                var newInput = input.cloneNode(false);
                newInput.value = '';
                newInput.addEventListener('change', this.onUploadChange, true);

                parent.removeChild(input);
                parent.appendChild(newInput);

                var _this = this;
                _.forEach(files, function(file) {
                    _this.filesToUpload.push(file);
                });
            },

            uploadFile: function (file, callback) {
                var _this = this;
                var data = new FormData();
                data.append('upload_path', this.currentPath);
                data.append('files[0]', file);
                var config = {
                    onUploadProgress: function (progressEvent) {
                        var percentCompleted = progressEvent.loaded / progressEvent.total;
                        _this.uploadingPercent = percentCompleted * 100;
                    }
                };
                grow.action('filemanagerUpload', data, config)
                    .then(function (response) {
                        if (response.data.success) {

                        }
                        else if (response.data.error) {

                        }
                        else {
                            alert('Unknown error');
                        }
                        callback && callback();
                    })
                    .catch(function (err) {
                        alert(err);
                    });
            },

            newFolder: function () {
                this.$refs.newFolderInput.value = '';
                this.$refs.newFolderModal.open();
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
                this.$refs.renameInput.value = this.selected[0].name;
                this.$refs.renameModal.open();
            },

            renameOk: function () {
                var newName = this.$refs.renameInput.value,
                    _this = this;
                if (newName === '') return;
                grow.action('filemanagerRename', {path: this.selected[0].path, newName: newName})
                    .then(function (response) {
                            if (response.data.success) {
                                _this.load();
                                _this.$refs.renameModal.close();
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

            renameCancel: function () {
                this.$refs.renameModal.close();
            },

            deleteFile: function() {
                var _this = this,
                    path = this.selected[0].path;

                this.$root.confirmDelete(function() {
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
                });
            }

        },

        created: function () {
            window.addEventListener('keydown', function (e) {

            });

            this.currentPath = this.path;

            this.$watch('path', function (path) {
                this.currentPath = this.path;
            });
        },

        mounted: function () {
            this.$el.querySelector('.upload_input').addEventListener('change', this.onUploadChange, true);
        }

    };

    Vue.component('rc-filemanager', FileManager);

    //Vue.partial('pickup', '<span class="pickup">{{description}}</span>');

}());