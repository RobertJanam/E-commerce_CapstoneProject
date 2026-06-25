let fileQueue = [];

// Auto fadeout banner timer
const flash = document.getElementById('flashNotification');
if (flash) {
    setTimeout(() => {
        flash.style.transition = "opacity 0.5s ease";
        flash.style.opacity = "0";
        setTimeout(() => flash.remove(), 500);
    }, 4000);
}

function triggerFileInput() {
    if (fileQueue.length >= 3) {
        alert("Maximum of 3 images are allowed");
        return;
    }
    document.getElementById('hiddenFileInput').click();
}

function handleFileSelection(event) {
    const selectedFiles = Array.from(event.target.files);

    for (let file of selectedFiles) {
        if (fileQueue.length >= 3) break;

        if (file.type.includes('video')) {
            alert("Validation Error: Video files are not allowed.");
            continue;
        }

        fileQueue.push(file);
    }

    renderPreviews();
    synchronizeFormInput();
}

function removeFileFromQueue(index) {
    fileQueue.splice(index, 1);
    renderPreviews();
    synchronizeFormInput();
}

function replaceFileInQueue(index) {
    const replacerInput = document.createElement('input');
    replacerInput.type = 'file';
    replacerInput.accept = 'image/*';
    replacerInput.onchange = (e) => {
        const targetFile = e.target.files[0];
        if (targetFile) {
            if (targetFile.type.includes('video')) {
                alert("Validation Error: Video files are not allowed.");
                return;
            }
            fileQueue[index] = targetFile;
            renderPreviews();
            synchronizeFormInput();
        }
    };
    replacerInput.click();
}

function renderPreviews() {
    const container = document.getElementById('previewContainer');
    container.innerHTML = "";

    fileQueue.forEach((file, index) => {
        const reader = new FileReader();
        const thumbnailFrame = document.createElement('div');
        thumbnailFrame.className = 'preview-thumbnail-frame';

        reader.onload = function(e) {
            thumbnailFrame.innerHTML = `
                <img src="${e.target.result}" alt="Preview Asset Track">
                <div class="thumbnail-action-overlay">
                    <button type="button" class="overlay-action-btn btn-replace-overlay" onclick="replaceFileInQueue(${index})">Replace</button>
                    <button type="button" class="overlay-action-btn btn-remove-overlay" onclick="removeFileFromQueue(${index})">Remove</button>
                </div>
            `;
        }
        reader.readAsDataURL(file);
        container.appendChild(thumbnailFrame);
    });
}

function synchronizeFormInput() {
    const inputElement = document.getElementById('hiddenFileInput');
    const dataTransfer = new DataTransfer();

    fileQueue.forEach(file => {
        dataTransfer.items.add(file);
    });

    inputElement.files = dataTransfer.files;
}