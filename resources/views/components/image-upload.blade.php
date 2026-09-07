@php
    $id = $id ?? 'image-upload-' . str()->random(8);
    $name = $name ?? 'files';
    $maxFiles = $maxFiles ?? 10;
    $maxSize = $maxSize ?? 10485760;
    $acceptedTypes = $acceptedTypes ?? ['image/jpeg', 'image/png', 'image/webp', 'image/gif', 'application/pdf'];
    $acceptedExtensions = $acceptedExtensions ?? ['jpg', 'jpeg', 'png', 'webp', 'gif', 'pdf'];
    $endpoint = $endpoint ?? route('attachments.store', [$type ?? 'assets', $entityId ?? 0]);
    $modelId = $modelId ?? 0;
    $autoUpload = $autoUpload ?? true;
@endphp

<div x-data="imageUploader({
    id: '{{ $id }}',
    name: '{{ $name }}',
    maxFiles: {{ $maxFiles }},
    maxSize: {{ $maxSize }},
    acceptedTypes: @json($acceptedTypes),
    acceptedExtensions: @json($acceptedExtensions),
    endpoint: '{{ $endpoint }}',
    modelId: {{ $modelId }},
    type: '{{ $type ?? 'assets' }}',
    entityId: {{ $entityId ?? 0 }},
    autoUpload: {{ $autoUpload ? 'true' : 'false' }},
})"
    x-cloak
    class="image-upload-component"
    data-id="{{ $id }}"
    x-bind:dir="document.documentElement.dir || 'ltr'"
>
    <div x-ref="dropZone"
         @dragover.prevent="handleDragOver($event)"
         @dragleave.prevent="handleDragLeave($event)"
         @drop.prevent="handleDrop($event)"
         :class="dragOverActive ? 'border-blue-500 bg-blue-50' : 'border-gray-300 bg-white hover:border-gray-400'"
         class="border-2 border-dashed rounded-xl p-6 transition-all duration-200 cursor-pointer"
         role="button"
         tabindex="0"
         aria-label="Drop zone for image upload"
         @keydown.enter="$refs.fileInput.click()"
         @keydown.space.prevent="$refs.fileInput.click()">
        <input type="file"
               :id="id"
               :name="name"
               multiple
               :accept="acceptedExtensions.map(e => '.' + e).join(',')"
               @change="handleFileSelect($event)"
               class="hidden"
               ref="fileInput">

        <div class="flex flex-col items-center justify-center text-center">
            <div class="mb-4 text-gray-400" aria-hidden="true">
                <svg class="w-12 h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                </svg>
            </div>
            <p class="text-sm font-medium text-gray-700">
                <span x-text="dragOverActive ? '{{ __('Drop files here') }}' : '{{ __('Drag & drop images here') }}'"></span>
                <span class="text-blue-600 cursor-pointer" @click.prevent="$refs.fileInput.click()">or browse</span>
            </p>
            <p class="mt-1 text-xs text-gray-500">
                JPG, PNG, WEBP, GIF or PDF. Max 10MB each. Up to <span x-text="maxFiles"></span> files.
            </p>
            <p class="mt-2 text-xs text-gray-400" x-show="files.length > 0">
                <span x-text="files.length + ' of ' + maxFiles + ' selected'"></span>
            </p>
        </div>
    </div>

    <div x-show="files.length > 0" class="mt-4">
        <div class="flex flex-wrap gap-3">
            <template x-for="(file, index) in files" :key="index">
                <div class="relative group w-32 bg-white border rounded-lg shadow-sm overflow-hidden">
                    <div class="relative h-24 flex items-center justify-center bg-gray-100">
                        <template x-if="file.type.startsWith('image/')">
                            <img :src="file.preview"
                                 :alt="file.name"
                                 loading="lazy"
                                 decoding="async"
                                 class="w-full h-full object-cover"
                                 x-show="file.preview && !file.uploading"
                                 x-effect="() => { if (file.preview && !file.uploading) file.classList?.add('lazy-loaded') }">
                            <img :src="file.preview"
                                 :alt="file.name"
                                 loading="lazy"
                                 decoding="async"
                                 class="w-full h-full object-cover opacity-50"
                                 x-show="file.uploading"
                                 style="filter: blur(10px);">
                        </template>
                        <template x-if="!file.type.startsWith('image/')">
                            <div class="flex flex-col items-center text-gray-400">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                          d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                </svg>
                                <span class="text-xs mt-1" x-text="file.name.slice(0, 10) + '...'"></span>
                            </div>
                        </template>

                        <div x-show="file.uploading" class="absolute inset-0 bg-black/40 flex items-center justify-center">
                            <div class="w-8 h-8 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
                        </div>

                        <div x-show="file.progress < 100 && file.uploading" class="absolute bottom-0 left-0 right-0 h-1 bg-gray-200">
                            <div x-show="file.progress > 0"
                                 x-bind:style="'width: ' + file.progress + '%'"
                                 class="h-full bg-blue-500 transition-all duration-300 rounded-r"></div>
                        </div>
                    </div>

                    <div class="px-2 py-1 flex items-center justify-between">
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-medium text-gray-700 truncate" x-text="file.name"></p>
                            <p class="text-xs text-gray-400" x-text="formatFileSize(file.size)"></p>
                        </div>
                    </div>

                    <button type="button"
                            @click="removeFile(index)"
                            class="absolute top-1 end-1 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-200 hover:bg-red-600 focus:opacity-100 shadow-md"
                            aria-label="Remove {{ file.name }}">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>

                    <div x-show="file.error" class="absolute inset-0 bg-red-100/90 flex items-center justify-center rounded-lg">
                        <p class="text-xs text-red-600 text-center px-2" x-text="file.error"></p>
                    </div>

                    <template x-if="file.uploadedUrl">
                        <div class="absolute top-1 start-1 bg-green-500 text-white text-xs px-1.5 py-0.5 rounded-full">
                            Done
                        </div>
                    </template>
                </div>
            </template>
        </div>
    </div>

    <div x-show="errorMessage" class="mt-3 text-sm text-red-600" x-text="errorMessage" x-cloak></div>

    <div x-show="uploadingCount > 0" class="mt-3 flex items-center gap-2">
        <div class="w-4 h-4 border-2 border-blue-500 border-t-transparent rounded-full animate-spin"></div>
        <span class="text-sm text-blue-600" x-text="uploadingCount + ' file(s) uploading...'"></span>
    </div>

    <div x-show="uploadComplete" class="mt-3 upload-complete">
        <div class="rounded-lg bg-green-50 p-3">
            <p class="text-sm text-green-700" x-text="uploadCompleteMessage"></p>
            <div class="mt-2 flex flex-wrap gap-2" x-show="uploadedFiles.length > 0">
                <template x-for="(file, index) in uploadedFiles" :key="index">
                    <a :href="file.url"
                       :target="_blank"
                       class="inline-flex items-center gap-1 text-xs text-blue-600 hover:underline bg-white px-2 py-1 rounded border">
                        <img :src="file.thumbnail" class="w-6 h-6 object-cover rounded" loading="lazy" x-show="file.type.startsWith('image/')">
                        <span x-text="file.original_name"></span>
                    </a>
                </template>
            </div>
        </div>
    </div>

    <div x-show="files.length > 0 && !uploading && !uploadComplete" class="mt-3 flex gap-2">
        <button type="button"
                @click="uploadFiles()"
                class="rounded-lg bg-blue-600 px-4 py-2 text-sm text-white hover:bg-blue-700 transition-colors">
            Upload Selected
        </button>
        <button type="button"
                @click="files = []; uploadComplete = false; uploadedFiles = []"
                class="rounded-lg bg-gray-200 px-4 py-2 text-sm text-gray-700 hover:bg-gray-300 transition-colors">
            Clear All
        </button>
    </div>
</div>
