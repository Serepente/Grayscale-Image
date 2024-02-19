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
});

function updateMainImage(imagePath) {
    var mainImage = document.getElementById('mainImage');
    mainImage.src = imagePath;
}

const triggerTabList = document.querySelectorAll('#myTab a')
triggerTabList.forEach(triggerEl => {
  const tabTrigger = new bootstrap.Tab(triggerEl)

  triggerEl.addEventListener('click', event => {
    event.preventDefault()
    tabTrigger.show()
  })
})
