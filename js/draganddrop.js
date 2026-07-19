const dropZone = document.getElementById("dropZone");
const fileInput = document.getElementById("postMediaFile");
const preview = document.getElementById("preview");
document.documentElement.classList.add("js");
// Nur wenn JS läuft
fileInput.style.display = "none";
dropZone.style.display = "block";


// Klick öffnet Dateiauswahl
dropZone.addEventListener("click", () => fileInput.click());

// Drag over Styling
dropZone.addEventListener("dragover", (e) => {
    e.preventDefault();
    dropZone.classList.add("dragover");
});

// Drag leave
dropZone.addEventListener("dragleave", () => {
    dropZone.classList.remove("dragover");
});

// Datei droppen
dropZone.addEventListener("drop", (e) => {
    e.preventDefault();
    dropZone.classList.remove("dragover");

    const files = e.dataTransfer.files;
    if (files.length) {
        fileInput.files = files; // wichtig für PHP Upload
        showPreview(files[0]);
    }
});

// Wenn Datei normal ausgewählt wird
fileInput.addEventListener("change", () => {
    if (fileInput.files.length) {
        showPreview(fileInput.files[0]);
    }
});

// Preview
function showPreview(file) {
    const reader = new FileReader();

    reader.onload = function (e) {
        preview.innerHTML = `<img src="${e.target.result}" alt="Preview">`;
    };

    reader.readAsDataURL(file);
}