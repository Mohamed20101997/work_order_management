export function imageUploader() {
    return {
        id: '',
        name: 'files',
        maxFiles: 10,
        maxSize: 10485760,
        acceptedTypes: [],
        acceptedExtensions: [],
        endpoint: '',
        modelId: 0,
        type: 'assets',
        entityId: 0,
        files: [],
        uploading: false,
        uploadingCount: 0,
        uploadComplete: false,
        uploadCompleteMessage: '',
        uploadedFiles: [],
        errorMessage: '',
        dragOverActive: false,
        _dragCounter: 0,

        init() {
            this.$watch('files', () => {
                this.validateAll();
                if (this.files.length > 0 && !this.uploading && !this.uploadComplete) {
                    this.$nextTick(() => {
                        this.uploadFiles();
                    });
                }
            }, { deep: true });
        },

        async handleFileSelect(event) {
            const selected = Array.from(event.target.files);
            await this.addFiles(selected);
            event.target.value = '';
        },

        handleDragOver(event) {
            event.preventDefault();
            event.stopPropagation();
            this.dragOverActive = true;
        },

        handleDragLeave(event) {
            event.preventDefault();
            event.stopPropagation();
            this._dragCounter--;
            if (this._dragCounter <= 0) {
                this.dragOverActive = false;
                this._dragCounter = 0;
            }
        },

        handleDrop(event) {
            event.preventDefault();
            event.stopPropagation();
            this.dragOverActive = false;
            this._dragCounter = 0;

            const dropped = Array.from(event.dataTransfer.files);
            this.addFiles(dropped);
        },

        async addFiles(newFiles) {
            this.errorMessage = '';
            const remaining = this.maxFiles - this.files.length;
            if (remaining <= 0) {
                this.errorMessage = `Maximum ${this.maxFiles} files allowed.`;
                return;
            }

            const filesToAdd = newFiles.slice(0, remaining);

            for (const file of filesToAdd) {
                const validation = this.validateFile(file);
                if (!validation.valid) {
                    this.errorMessage = validation.error;
                    this.files.push({
                        ...file,
                        preview: null,
                        uploading: false,
                        progress: 0,
                        error: validation.error,
                        webpPreview: null,
                    });
                    continue;
                }

                const preview = await this.generatePreview(file);
                const webpPreview = await this.generateWebPPreview(file);

                this.files.push({
                    ...file,
                    preview,
                    uploading: false,
                    progress: 0,
                    error: null,
                    webpPreview,
                    uploadedUrl: null,
                    uploadedThumbnail: null,
                    uploadedWebp: null,
                    type: file.type || this.getMimeType(file.name),
                });
            }
        },

        validateFile(file) {
            if (this.files.length >= this.maxFiles) {
                return { valid: false, error: `Maximum ${this.maxFiles} files allowed.` };
            }

            if (file.size > this.maxSize) {
                return { valid: false, error: `${file.name} exceeds 10MB limit.` };
            }

            const extension = file.name.split('.').pop().toLowerCase();
            if (!this.acceptedExtensions.includes(extension)) {
                return { valid: false, error: `${file.name} has an unsupported format.` };
            }

            if (file.size === 0) {
                return { valid: false, error: `${file.name} is empty.` };
            }

            return { valid: true };
        },

        validateAll() {
            this.files.forEach(file => {
                if (!file.error && file.size > this.maxSize) {
                    file.error = `Exceeds 10MB limit.`;
                }
            });
        },

        generatePreview(file) {
            return new Promise((resolve) => {
                if (!file.type.startsWith('image/')) {
                    resolve(null);
                    return;
                }

                const reader = new FileReader();
                reader.onload = (e) => resolve(e.target.result);
                reader.onerror = () => resolve(null);
                reader.readAsDataURL(file);
            });
        },

        async generateWebPPreview(file) {
            if (!file.type.startsWith('image/')) return null;

            return new Promise((resolve) => {
                try {
                    const img = new Image();
                    img.onload = () => {
                        const canvas = document.createElement('canvas');
                        const maxDim = 800;
                        let { width, height } = img;

                        if (width > height && width > maxDim) {
                            height = (height * maxDim) / width;
                            width = maxDim;
                        } else if (height > maxDim) {
                            width = (width * maxDim) / height;
                            height = maxDim;
                        }

                        canvas.width = width;
                        canvas.height = height;

                        try {
                            const ctx = canvas.getContext('2d');
                            ctx.drawImage(img, 0, 0, width, height);
                            const webpDataUrl = canvas.toDataURL('image/webp', 0.8);
                            resolve(webpDataUrl);
                        } catch (e) {
                            resolve(null);
                        }
                    };
                    img.onerror = () => resolve(null);
                    const previewPromise = this.generatePreview(file);
                    previewPromise.then((preview) => {
                        img.src = preview;
                    }).catch(() => resolve(null));
                } catch (e) {
                    resolve(null);
                }
            });
        },

        formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        },

        getMimeType(filename) {
            const ext = filename.split('.').pop().toLowerCase();
            const map = {
                'jpg': 'image/jpeg',
                'jpeg': 'image/jpeg',
                'png': 'image/png',
                'webp': 'image/webp',
                'gif': 'image/gif',
                'pdf': 'application/pdf',
            };
            return map[ext] || '';
        },

        removeFile(index) {
            this.files.splice(index, 1);
            this.uploadComplete = false;
            this.uploadedFiles = [];
        },

        async uploadFiles() {
            const pendingFiles = this.files.filter(f => !f.uploading && !f.error);
            if (pendingFiles.length === 0) return;

            this.uploading = true;
            this.uploadingCount = pendingFiles.length;
            this.uploadComplete = false;
            this.uploadedFiles = [];
            this.errorMessage = '';

            const formData = new FormData();
            const uploadQueue = [...pendingFiles];

            for (const file of uploadQueue) {
                formData.append('files[]', file);
                file.uploading = true;
                file.progress = 0;
            }

            try {
                const response = await fetch(this.endpoint, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    },
                    body: formData,
                });

                const result = await response.json();

                if (result.success && result.files) {
                    result.files.forEach((uploaded, i) => {
                        const idx = this.files.findIndex(f => f === pendingFiles[i]);
                        if (idx !== -1) {
                            this.files[idx].uploading = false;
                            this.files[idx].progress = 100;
                            this.files[idx].uploadedUrl = uploaded.url;
                            this.files[idx].uploadedThumbnail = uploaded.thumbnail;
                            this.files[idx].uploadedWebp = uploaded.webp;
                            this.files[idx].error = null;
                        }
                    });

                    this.uploadedFiles = result.files;
                    this.uploadComplete = true;
                    this.uploadCompleteMessage = result.message;

                    if (result.files.length > 0) {
                        this.dispatchEvent(new CustomEvent('files-uploaded', {
                            detail: { files: result.files },
                            bubbles: true,
                        }));
                    }
                } else {
                    this.errorMessage = result.message || 'Upload failed.';
                    pendingFiles.forEach(f => { f.uploading = false; f.error = 'Upload failed.'; });
                }
            } catch (error) {
                this.errorMessage = 'Network error during upload.';
                pendingFiles.forEach(f => { f.uploading = false; f.error = 'Network error.'; });
            } finally {
                this.uploading = false;
                this.uploadingCount = 0;
            }
        },

        async uploadWithProgress(file) {
            return new Promise((resolve, reject) => {
                const xhr = new XMLHttpRequest();
                const formData = new FormData();
                formData.append('files[]', file);

                xhr.upload.addEventListener('progress', (e) => {
                    if (e.lengthComputable) {
                        const percent = Math.round((e.loaded / e.total) * 100);
                        const idx = this.files.findIndex(f => f === file);
                        if (idx !== -1) {
                            this.files[idx].progress = percent;
                        }
                    }
                });

                xhr.addEventListener('load', () => {
                    if (xhr.status >= 200 && xhr.status < 300) {
                        resolve(JSON.parse(xhr.responseText));
                    } else {
                        reject(new Error('Upload failed'));
                    }
                });

                xhr.addEventListener('error', () => reject(new Error('Network error')));
                xhr.open('POST', this.endpoint);
                xhr.setRequestHeader('X-CSRF-TOKEN', document.querySelector('meta[name="csrf-token"]')?.content || '');
                xhr.send(formData);
            });
        },
    };
}
