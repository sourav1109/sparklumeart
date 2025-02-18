// JavaScript for image modal
const modal = document.getElementById("myModal");
const modalImage = document.getElementById("modalImage");
const modalCaption = document.getElementById("modalCaption");
const images = document.querySelectorAll(".image img");
const captions = document.querySelectorAll(".caption");
const prevBtn = document.getElementById("prev");
const nextBtn = document.getElementById("next");
const closeBtn = document.querySelector(".close");
let currentIndex = 0;

// Open modal on image click
images.forEach((image, index) => {
    image.addEventListener("click", () => {
        modal.style.display = "block";
        modalImage.src = image.src;
        currentIndex = index;
        updateCaption();
    });
});

// Close modal
closeBtn.addEventListener("click", () => {
    modal.style.display = "none";
});

// Change image in modal
function changeImage(direction) {
    currentIndex += direction;
    if (currentIndex < 0) {
        currentIndex = images.length - 1;
    } else if (currentIndex >= images.length) {
        currentIndex = 0;
    }
    modalImage.src = images[currentIndex].src;
    updateCaption();
}

// Function to update the caption in the modal
function updateCaption() {
    modalCaption.textContent = captions[currentIndex].textContent;
}

// Keyboard navigation for modal
document.addEventListener("keydown", (e) => {
    if (modal.style.display === "block") {
        if (e.key === "ArrowLeft") {
            changeImage(-1);
        } else if (e.key === "ArrowRight") {
            changeImage(1);
        }
    }
});

// Previous and Next buttons
prevBtn.addEventListener("click", () => {
    changeImage(-1);
});

nextBtn.addEventListener("click", () => {
    changeImage(1);
});
