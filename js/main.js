document.getElementById('img_upload').addEventListener('change', function() {
    var file = this.files[0];
    if (file) {
        var reader = new FileReader();
        reader.onload = function(event) {
            var image = document.createElement('img');
            image.src = event.target.result;
            image.style.maxWidth = '100%'; // Adjust image size if needed
            document.getElementById('image-preview').innerHTML = ''; // Clear previous image if any
            document.getElementById('image-preview').appendChild(image);
        };
        reader.readAsDataURL(file);
    }
});