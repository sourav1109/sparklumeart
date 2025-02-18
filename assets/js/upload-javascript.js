const slidePage = document.querySelector(".slide-page");
const nextBtnFirst = document.querySelector(".firstNext");
const prevBtnSec = document.querySelector(".prev-1");
const nextBtnSec = document.querySelector(".next-1");
const prevBtnThird = document.querySelector(".prev-2");
const nextBtnThird = document.querySelector(".next-2");
const prevBtnFourth = document.querySelector(".prev-3");
const submitBtn = document.querySelector(".submit");
const progressText = document.querySelectorAll(".step p");
const progressCheck = document.querySelectorAll(".step .check");
const bullet = document.querySelectorAll(".step .bullet");
let current = 1;

// Function to check if any field is empty
function checkFieldsEmpty() {
  const fields = document.querySelectorAll('input[type="text"], input[type="tel"], input[type="email"], textarea');
  let isEmpty = false;

  fields.forEach((field) => {
    if (field.value.trim() === '') {
      alert(`${field.name} cannot be empty.`);
      isEmpty = true;
    }
  });

  return isEmpty;
}

// Function to validate file input for specific types
function validateFile() {
  const fileInput = document.querySelector('input[type="file"]');
  const file = fileInput.files[0];
  
  if (file && !['image/jpeg', 'image/png', 'image/gif'].includes(file.type)) {
    alert('Invalid file type. Please upload a JPEG, PNG, or GIF image.');
    return false;
  }
  
  return true;
}

// Function to validate mobile number
function validateMobileNumber() {
  const mobileInput = document.querySelector('input[type="tel"]');
  const mobileNumber = mobileInput.value;

  if (!/^\d{10}$/.test(mobileNumber)) {
    alert('Invalid mobile number. Please enter a 10-digit number.');
    return false;
  }

  return true;
}

// Function to limit text areas to 100 characters
function checkCharacterLimit() {
  const textAreas = document.querySelectorAll('textarea');
  let isWithinLimit = true;

  textAreas.forEach((textarea) => {
    if (textarea.value.length > 100) {
      alert('Text area should not exceed 100 characters.');
      isWithinLimit = false;
    }
  });

  return isWithinLimit;
}

// Limit text area input dynamically
document.querySelectorAll('textarea').forEach(textarea => {
  textarea.addEventListener('input', () => {
    if (textarea.value.length > 100) {
      textarea.value = textarea.value.substring(0, 100);
      alert('Text area should not exceed 100 characters.');
    }
  });
});

// Navigation through steps
nextBtnFirst.addEventListener("click", function(event){
  event.preventDefault();
  slidePage.style.marginLeft = "-25%";
  bullet[current - 1].classList.add("active");
  progressCheck[current - 1].classList.add("active");
  progressText[current - 1].classList.add("active");
  current += 1;
});

nextBtnSec.addEventListener("click", function(event){
  event.preventDefault();
  slidePage.style.marginLeft = "-50%";
  bullet[current - 1].classList.add("active");
  progressCheck[current - 1].classList.add("active");
  progressText[current - 1].classList.add("active");
  current += 1;
});

nextBtnThird.addEventListener("click", function(event){
  event.preventDefault();
  slidePage.style.marginLeft = "-75%";
  bullet[current - 1].classList.add("active");
  progressCheck[current - 1].classList.add("active");
  progressText[current - 1].classList.add("active");
  current += 1;
});

// Event listener for Submit button
submitBtn.addEventListener('click', (event) => {
  event.preventDefault();

  // Perform all validation checks
  const isFieldsEmpty = checkFieldsEmpty();
  const isMobileValid = validateMobileNumber();
  const isFileValid = validateFile();
  const isCharacterLimitValid = checkCharacterLimit();

  // Check if any validation failed
  if (isFieldsEmpty || !isMobileValid || !isFileValid || !isCharacterLimitValid) {
    alert('Please fill out all fields correctly before submitting the form.');
  } else {
    alert('Your form was successfully submitted');
    // Optional: remove location.reload(); if you want actual form submission here
  }
});

prevBtnSec.addEventListener("click", function(event){
  event.preventDefault();
  slidePage.style.marginLeft = "0%";
  bullet[current - 2].classList.remove("active");
  progressCheck[current - 2].classList.remove("active");
  progressText[current - 2].classList.remove("active");
  current -= 1;
});

prevBtnThird.addEventListener("click", function(event){
  event.preventDefault();
  slidePage.style.marginLeft = "-25%";
  bullet[current - 2].classList.remove("active");
  progressCheck[current - 2].classList.remove("active");
  progressText[current - 2].classList.remove("active");
  current -= 1;
});

prevBtnFourth.addEventListener("click", function(event){
  event.preventDefault();
  slidePage.style.marginLeft = "-50%";
  bullet[current - 2].classList.remove("active");
  progressCheck[current - 2].classList.remove("active");
  progressText[current - 2].classList.remove("active");
  current -= 1;
});
