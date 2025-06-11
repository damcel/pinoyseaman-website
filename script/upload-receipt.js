const uploadArea = document.getElementById('uploadArea');
    const fileInput = document.getElementById('screenshotUpload');
    const preview = document.getElementById('uploadPreview');

    // File input change (normal upload)
    fileInput.addEventListener('change', function(event) {
        const file = event.target.files[0];
        if (file && file.type.startsWith('image/')) {
        previewImage(file);
        } else {
        preview.innerHTML = '<p class="text-danger">Please select a valid image file.</p>';
        }
    });

    // Handle pasted image
    uploadArea.addEventListener('paste', function(event) {
        const items = event.clipboardData.items;
        for (let i = 0; i < items.length; i++) {
        const item = items[i];
        if (item.type.indexOf('image') !== -1) {
            const file = item.getAsFile();
            previewImage(file);
            break;
        }
        }
    });

    function previewImage(file) {
        const reader = new FileReader();
        reader.onload = function(e) {
        preview.innerHTML = `
            <div>
            <img src="${e.target.result}" style="max-width: 100%; height: auto;" alt="Preview">
            <button type="button" class="btn btn-sm btn-danger mt-2" id="removeImageBtn">
                <i class="fa-solid fa-xmark"></i> Remove
            </button>
            </div>
        `;
        document.getElementById('removeImageBtn').addEventListener('click', removeImage);
        };
        reader.readAsDataURL(file);
    }

    function removeImage() {
        fileInput.value = '';
        preview.innerHTML = 'No image uploaded yet';
    }