document.getElementById('img_upload').addEventListener('change', function() {
    var file = this.files[0];
    if (file) {
        var reader = new FileReader();
        reader.onload = function(event) {
            var image = document.createElement('img');
            image.src = event.target.result;
            image.style.maxWidth = '100%';
            document.getElementById('image-preview').innerHTML = ''; 
            document.getElementById('image-preview').appendChild(image);
        };
        reader.readAsDataURL(file);
    }
});

const myModal = document.getElementById('myModal')
const myInput = document.getElementById('myInput')

myModal.addEventListener('shown.bs.modal', () => {
  myInput.focus()
})