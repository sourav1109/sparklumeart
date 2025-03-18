<?php include('backend/config.php'); ?>
<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="assets/css/style-home.css">
        <title>Your Artistic Website</title>
    </head>
    <body>
        <div class="background">
            <img src="background8.jpg" alt="Background Image" >
        </div>
        <header>
            <nav>
                <ul>
                    <br>
                    <li><a href="home.php">Home</a></li>
                <li><a href="gallery.php">Art Gallery</a></li>
                <li><a href="upload.php">Upload</a></li>
                <li><a href="sell.php">Art Shop</a></li>
                <li><a href="psychology.php">🎨 Art & Soul Quest</a></li>
                <li><a href="about.html">About Us</a></li>
                <li><a href="login.php">login</a></li>
                

                        </li>
                </ul>
            </nav>
        </header>
        <section id="name-tagline">
            <div class="logo">
                <img src="Logo-2.png" alt="Your Logo" style="border-radius: 150%; width: 100px;">
                <br>
                <div style=" padding: 10px; display: inline-block;">
                    

                    <h1 style="font-size: 36px; font-weight: bold; color: rgb(7, 16, 99); text-shadow: 
                    -2px -2px 0 #f4f0f0, 2px -2px 0 #f0ebeb, -2px 2px 0 #f3f0f0, 2px 2px 0 #eeeaea; background-clip: text; -webkit-background-clip: text;">
                      Sparklume
                    </h1>
                  </div>
            </div>
            <br>
            <br>
        <script>
            
            
            // JavaScript to add the "visible" class to logo and heading
document.addEventListener('DOMContentLoaded', function () {
    var logo = document.querySelector('.logo img');
    var heading = document.querySelector('#name-tagline h1');

    // Add the "visible" class after a delay
    setTimeout(function () {
        logo.classList.add('visible');
        heading.classList.add('visible');
    }, 1000); // Adjust the delay as needed
});

        </script>
    
        
        
        <section id="purpose" style="background-color:rgba(0,0,0,0.7) ;">
            <div class="promo">
                <br>
                <p>Elevate your allure with a captivating portrait. Our talented artist will unveil your inner charm, creating a timeless masterpiece that mirrors your unique essence. Let your portrait tell your story in a single frame, a captivating glimpse into your world.</p>
                <br>
                <p class="blink" >40% OFF: Commission Your Affordable Portrait Sketch</p>
                <br>
                <a href="upload.php" class="upload-button">Upload Now</a>
            </div>
        </section>
        <script>
            // JavaScript to add the "visible" class to elements within #purpose
document.addEventListener('DOMContentLoaded', function () {
    var promoElements = document.querySelectorAll('#purpose .promo p, #purpose .blink, #purpose .upload-button');

    // Add the "visible" class after a delay
    setTimeout(function () {
        promoElements.forEach(function (element) {
            element.classList.add('visible');
        });
    }, 1000); // Adjust the delay as needed
});

        </script>
        
        <br>
        <br>
       
            <center>
            <p class="heading" style="color:rgb(7, 16, 99);text-decoration: underline;">My Featured Masterpieces</p></center>
            <br>
            <br>

        
            <div class="gallery">
                <div class="image">
                    <img src="f.jpg" alt="Atmaroti
                    (Oil Painting)" style="padding-bottom: 5px;">
                    <div class="caption">Atmaroti
                        (Oil Painting)</div>
                </div>
                <div class="image">
                    <img src="w.jpg" alt="A Mat Seller
                    ( Water color )">
                    <div class="caption">A Mat Seller <p>( Water color )</p></div>
                </div>
                <div class="image">
                    <img src="q.jpg" alt="The Old Man(Water color)">
                    <div class="caption">The Old Man<p>(Water color)</p></div>
                </div>
                <div class="image">
                    <img src="o.jpg" alt="Nature ( Water color )">
                    <div class="caption">Nature <p>( Water color )</p></div>
                </div>
                
  
                </div>
        
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    var gallery = document.querySelector('.gallery');
                    var masonry = new Masonry(gallery, {
                        itemSelector: '.image',
                        columnWidth: '.image',
                        gutter: 20, // Adjust the spacing between items
                    });
                });
            </script>
            <script>
               // Function to check if an element is in the viewport
function isElementInViewport(element) {
    var rect = element.getBoundingClientRect();
    return (
        rect.top >= 0 &&
        rect.left >= 0 &&
        rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
        rect.right <= (window.innerWidth || document.documentElement.clientWidth)
    );
}

// Function to handle adding the animation class
function handleAnimation() {
    var paintings = document.querySelectorAll('.image');

    paintings.forEach(function(painting) {
        if (isElementInViewport(painting)) {
            painting.classList.add('animate');
        }
    });
}

// Add a scroll event listener to trigger the animation
window.addEventListener('scroll', handleAnimation);

// Trigger the animation immediately when the page loads
window.addEventListener('DOMContentLoaded', handleAnimation);

            </script>
          
            
        
        <!-- Add more painting divs with data-index attribute for each image -->
  

    <!-- The modal to display images -->
    <div id="myModal" class="modal">
        <span class="close" id="closeBtn">&times;</span>
        <img class="modal-content" id="modalImage">
        <div id="caption"></div>
        <button id="prevBtn">Previous</button>
        <button id="nextBtn">Next</button>
    </div>

    <script >
        // JavaScript for image gallery
    var modal = document.getElementById("myModal");
    var modalImg = document.getElementById("modalImage");
    var captionText = document.getElementById("caption");
    var prevBtn = document.getElementById("prevBtn");
    var nextBtn = document.getElementById("nextBtn");

    var paintings = document.querySelectorAll(".image img");
    var currentIndex = 0;

    paintings.forEach(function (painting, index) {
        painting.addEventListener("click", function () {
            modal.style.display = "block";
            modalImg.src = painting.src;
            captionText.innerHTML = painting.alt;
            currentIndex = index;
        });
    });

    function showImage(index) {
        if (index >= 0 && index < paintings.length) {
            modalImg.src = paintings[index].src;
            captionText.innerHTML = paintings[index].alt;
            currentIndex = index;
        }
    }

    prevBtn.addEventListener("click", function () {
        showImage(currentIndex - 1);
    });

    nextBtn.addEventListener("click", function () {
        showImage(currentIndex + 1);
    });

    document.getElementById("closeBtn").addEventListener("click", function () {
        modal.style.display = "none";
    });

    document.addEventListener("keydown", function (event) {
        if (modal.style.display === "block") {
            if (event.key === "ArrowLeft") {
                showImage(currentIndex - 1);
            } else if (event.key === "ArrowRight") {
                showImage(currentIndex + 1);
            } else if (event.key === "Escape") {
                modal.style.display = "none";
            }
        }
    });


    </script>
    <br>
    <div style="max-width: 400px; margin: 0 auto; padding: 20px; border: 1px solid #ccc; background-color:rgba(15, 15, 15, 0.5);border-radius: 10px;">
    <div>
        <h2 style="font-size: 24px; margin: 16px 0;">Portrait Price Calculator</h2>
        <label for="portrait-type" style="font-weight: bold;">Select Portrait Type:</label>
        <select id="portrait-type" name="portrait-type" style="padding: 4px; margin: 4px;">
            <option value="human">Human Portrait</option>
            <option value="pet">Pet Portrait</option>
            <option value="combined">Combined Portrait</option>
        </select>


        <!-- Human Portrait Options -->
        <div id="human-options">
            <label for="portrait-category" style="font-weight: bold;">Select Category:</label>
            <select id="portrait-category" name="portrait-category" style="padding: 4px; margin: 4px;">
                <option value="single">Single Portrait</option>
                <option value="family">Family Portrait</option>
            </select>
            <br>

            <!-- Additional input for family portrait -->
            <div id="family-input" style="display: none;">
                <label for="no-of-characters" style="font-weight: bold;">Number of Characters:</label>
                <input type="number" id="no-of-characters" name="no-of-characters" value="1" style="padding: 4px; margin: 4px;">
                <br>
            </div>

            <label for="portrait-type-human" style="font-weight: bold;">Portrait Type:</label>
            <select id="portrait-type-human" name="portrait-type-human" style="padding: 4px; margin: 4px;">
                <option value="full">Full</option>
                <option value="half">Half</option>
            </select>
        </div>

        <!-- Pet Portrait Options -->
        <div id="pet-options" style="display: none;">
            <label for="portrait-category-pet" style="font-weight: bold;">Select Category:</label>
            <select id="portrait-category-pet" name="portrait-category-pet" style="padding: 4px; margin: 4px;">
                <option value="single">Single Pet Portrait</option>
                <option value="multiple">Multiple Pet Portrait</option>
            </select>
            <br>

            <!-- Additional input for multiple pet portrait -->
            <div id="multiple-pets-input" style="display: none;">
                <label for="no-of-pets" style="font-weight: bold;">Number of Pets:</label>
                <input type="number" id="no-of-pets" name="no-of-pets" value="1" style="padding: 4px; margin: 4px;">
                <br>
            </div>

            <label for="portrait-type-pet" style="font-weight: bold;">Portrait Type:</label>
            <select id="portrait-type-pet" name="portrait-type-pet" style="padding: 4px; margin: 4px;">
                <option value="full">Full</option>
                <option value="half">Half</option>
            </select>
        </div>

        <!-- Combined Portrait Options -->
        <div id="combined-options" style="display: none;">
            <label for="no-of-characters-combined" style="font-weight: bold;">Number of Characters:</label>
            <input type="number" id="no-of-characters-combined" name="no-of-characters-combined" value="1" style="padding: 4px; margin: 4px;">
            <br>

            <label for="no-of-pets-combined" style="font-weight: bold;">Number of Pets:</label>
            <input type="number" id="no-of-pets-combined" name="no-of-pets-combined" value="1" style="padding: 4px; margin: 4px;">
            <br>

            <label for="portrait-type-combined" style="font-weight: bold;">Portrait Type:</label>
            <select id="portrait-type-combined" name="portrait-type-combined" style="padding: 4px; margin: 4px;">
                <option value="full">Full</option>
                <option value="half">Half</option>
            </select>
        </div>

        <label for="background-type" style="font-weight: bold;">Background Type:</label>
        <select id="background-type" name="background-type" style="padding: 4px; margin: 4px;">
            <option value="with-background">With Background</option>
            <option value="without-background">Without Background</option>
        </select>
        <br>

        <label for="paper-size" style="font-weight: bold;">Paper Size:</label>
        <select id="paper-size" name="paper-size" style="padding: 4px; margin: 4px;">
            <option value="A3">A3</option>
            <option value="A4">A4</option>
        </select>
        <br>

        <label for="paper-type" style="font-weight: bold;">Paper Type:</label>
        <select id="paper-type" name="paper-type" style="padding: 4px; margin: 4px;">
            <option value="acid-free">Acid-Free Paper</option>
            <option value="normal">Normal Paper</option>
        </select>
        <br>

        <button id="calculate-button" style="padding: 8px; margin: 8px; background-color: #007BFF; color: #fff; border: none; cursor: pointer; transition: background-color 0.3s ease-in-out;">
            Calculate Price
        </button>
    </div>


    <!-- Display the final price and breakdown -->
    <div id="price-result"  style="max-width: 400px; margin: 0 auto; padding: 20px; border: 1px solid #ccc; background-color:rgba(24, 22, 22, 0.5);border-radius: 10px;">
        <h3 style="font-size: 18px; margin: 16px 0;">Price Details:</h3>
        <p id="price-breakdown" style="margin: 8px 0;"></p>
        <h3 style="font-size: 18px; margin: 16px 0;">Total Price:</h3>
        <p id="final-price" style="font-weight: bold; font-size: 24px; margin: 8px 0;">0 Rs</p>
    </div>
    </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Function to calculate the price
            function calculatePrice() {
                // Get selected values
                const portraitType = document.getElementById('portrait-type').value;
                const portraitCategory = document.getElementById('portrait-category').value;
                const noOfCharacters = parseInt(document.getElementById('no-of-characters').value);
                const portraitTypeHuman = document.getElementById('portrait-type-human').value;
                const portraitCategoryPet = document.getElementById('portrait-category-pet').value;
                const noOfPets = parseInt(document.getElementById('no-of-pets').value);
                const portraitTypePet = document.getElementById('portrait-type-pet').value;
                const noOfCharactersCombined = parseInt(document.getElementById('no-of-characters-combined').value);
                const noOfPetsCombined = parseInt(document.getElementById('no-of-pets-combined').value);
                const portraitTypeCombined = document.getElementById('portrait-type-combined').value;
                const backgroundType = document.getElementById('background-type').value;
                const paperSize = document.getElementById('paper-size').value;
                const paperType = document.getElementById('paper-type').value;

                // Calculate base price
                let basePrice = 0;
                let price = 0;

                if (portraitType === 'human') {
                    if (portraitCategory === 'single') {
                        basePrice = 749;
                        price=basePrice;

                    } else if (portraitCategory === 'family') {
                        basePrice = 600 * noOfCharacters;
                        price=basePrice;
                    }

                    if (portraitTypeHuman === 'full') {
                        basePrice += 300;
                    }
                } else if (portraitType === 'pet') {
                    if (portraitCategoryPet === 'single') {
                        basePrice = 1099;
                        price=basePrice;
                    } else if (portraitCategoryPet === 'multiple') {
                        basePrice = 999 * noOfPets;
                        price=basePrice;
                    }

                    if (portraitTypePet === 'full') {
                        basePrice += 400;
                    }
                } else if (portraitType === 'combined') {
                    basePrice = 999 * noOfPetsCombined + 600 * noOfCharactersCombined;
                    price=basePrice;

                    if (portraitTypeCombined === 'full') {
                        basePrice = 1399* noOfPetsCombined + 750 * noOfCharactersCombined;
                        price=basePrice;
                        
                    }
                }

                // Calculate additional prices based on selections
                if (backgroundType === 'with-background') {
                    basePrice += 400;
                }

                if (paperSize === 'A3') {
                    basePrice += 100;
                    price+=100;
                }

                if (paperType === 'acid-free') {
                    basePrice += 80;
                }

                // Calculate packing and shipping charges (assumed price)
                const packingAndShippingCharge = 0; // Assumed value, adjust as needed

                // Calculate the final price with packing and shipping
                const finalPrice = basePrice + packingAndShippingCharge;

                // Update the final price with the price breakdown format
                const finalPriceElement = document.getElementById('final-price');
                finalPriceElement.textContent = `Price: ${finalPrice} Rs`;

                // Display the price breakdown
                const priceBreakdown = document.getElementById('price-breakdown');

    let portraitTypePrice = 0;
    let combinedPortraitPrice = 0; // Set your desired price for combined portraits here

    if (portraitType === 'human') {
        portraitTypePrice = portraitTypeHuman === 'full' ? 300 : 0;
    } else if (portraitType === 'pet') {
        portraitTypePrice = portraitTypePet === 'full' ? 400 : 0;
    } else if (portraitType === 'combined') {
        portraitTypePrice = combinedPortraitPrice;
    }

    priceBreakdown.innerHTML = `
        <li>Page Size: ${paperSize}: ${price} Rs</li>
        <li>${portraitType === 'combined' ? 'Portrait Type: combined: ' : `Portrait Type: ${portraitType}: `}${portraitTypePrice} Rs</li>
        <li>Background Type: ${backgroundType === 'with-background' ? 'With Background' : 'Without Background'}: ${backgroundType === 'with-background' ? 400 : 0} Rs</li>
        <li>Paper Type: ${paperType === 'acid-free' ? 'Acid-Free Paper' : 'Normal Paper'}: ${paperType === 'acid-free' ? 80 : 0} Rs</li>
        <br>
        <br>
        <b>Note: This is an assumed price. The actual price may vary based on image complexity. Packing and Shipping Charge varies according to your address.</b>
    `;


                // Show the price result
                const priceResult = document.getElementById('price-result');
                priceResult.style.display = 'block';
            }

            // Add event listener to the calculate button
            const calculateButton = document.getElementById('calculate-button');
            calculateButton.addEventListener('click', calculatePrice);

            // Add event listener to the portrait type select
            const portraitTypeSelect = document.getElementById('portrait-type');
            portraitTypeSelect.addEventListener('change', function () {
                const selectedOption = portraitTypeSelect.value;
                const humanOptions = document.getElementById('human-options');
                const petOptions = document.getElementById('pet-options');
                const combinedOptions = document.getElementById('combined-options');

                // Hide all options first
                humanOptions.style.display = 'none';
                petOptions.style.display = 'none';
                combinedOptions.style.display = 'none';

                // Show the selected options
                if (selectedOption === 'human') {
                    humanOptions.style.display = 'block';
                } else if (selectedOption === 'pet') {
                    petOptions.style.display = 'block';
                } else if (selectedOption === 'combined') {
                    combinedOptions.style.display = 'block';
                }
            });

            // Add event listener to the portrait category select in human options
            const portraitCategorySelect = document.getElementById('portrait-category');
            portraitCategorySelect.addEventListener('change', function () {
                const selectedCategory = portraitCategorySelect.value;
                const familyInput = document.getElementById('family-input');

                if (selectedCategory === 'family') {
                    familyInput.style.display = 'block';
                } else {
                    familyInput.style.display = 'none';
                }
            });

            // Add event listener to the portrait category select in pet options
            const portraitCategoryPetSelect = document.getElementById('portrait-category-pet');
            portraitCategoryPetSelect.addEventListener('change', function () {
                const selectedCategory = portraitCategoryPetSelect.value;
                const multiplePetsInput = document.getElementById('multiple-pets-input');

                if (selectedCategory === 'multiple') {
                    multiplePetsInput.style.display = 'block';
                } else {
                    multiplePetsInput.style.display = 'none';
                }
            });
        });
    </script>

    <br>
    <br>
    <section id="painter-info">
        <div class="image">
            <img src="dipa.jpg" alt="Painter Image">
        </div>
        <div class="achievements">
            <Center>
            <h2>ABOUT ME</h2>
        </Center>
            <ul>
                
                
                <li>
                    Unveiling Personal Stories:
                    Dipanwita Kundu, not just an artist but a storyteller, draws inspiration from the tapestry of life, weaving personal narratives into each creation. Every artwork has a tale to tell, and as you browse through the gallery, you'll find yourself immersed in a symphony of stories waiting to be discovered.
                </li>
                
                <!-- Add more achievements as needed -->
            </ul>
        </div>
        <script>
            // Function to check if an element is in the viewport
function isInViewport(element) {
    const rect = element.getBoundingClientRect();
    return (
        rect.top >= 0 &&
        rect.bottom <= (window.innerHeight || document.documentElement.clientHeight)
    );
}

// Function to handle the scroll animation
function handleScrollAnimation() {
    const painterInfoSection = document.getElementById('painter-info');

    if (isInViewport(painterInfoSection)) {
        painterInfoSection.classList.add('visible');
        window.removeEventListener('scroll', handleScrollAnimation);
    }
}

// Listen for scroll events
window.addEventListener('scroll', handleScrollAnimation);

// Trigger the animation on page load if the element is already in the viewport
handleScrollAnimation();

        </script>
    </section>
    <br>

        

    <center>
    <p class="blink" style=" font-size: 35px;
    color: rgb(253, 252, 251);
    font-family: 'Times New Roman', Times, serif;
    text-decoration: double;
    letter-spacing: 2px;
    background-color: rgba(0, 0, 0, 0.5);
    box-shadow: 0 0 2px rgba(0, 0, 0, 0.5);">40% OFF. Grab the oppurtunity now. Give us a change to draw your dream potrait in such a low budget.
        
        <br>
        CLICK ON THE UPLOAD BUTTON ABOVE
        
        <PRE>                                                  </PRE>
    </p>
    </center>
    <section class="sell-overview" style="background-color:rgba(253, 252, 251,0.5); color: black;font-size:22px;border-radius: 10px;">
        <h2 style="color: black;">Sell Your Art</h2>
        <p style="color: black;font-size:25px;">If you're an artist looking to sell your artwork, Sparklume is the perfect platform for you. Showcase your talent and reach a global audience of art enthusiasts.</p>
        <p style="color: black;font-size:25px;">Benefits of selling with us:</p>
        <ul>
            <li>Exposure to a diverse and engaged community of art lovers.</li>
            <li>Easy and secure payment processing for your sales.</li>
            <li>Customizable artist profile to showcase your portfolio.</li>
            <li>Promotion of your artwork through our marketing channels.</li>
        </ul>
        <p style="color: black;">Join our community of artists today and start selling your creations!</p>
        <a href="sellpage.html" class="sell-button">Get Started</a> <!-- Adjust the href attribute to your actual sell page -->
    </section>

    <section class="artist-gallery">
        <div class="artwork">
            <img src="frn1.jpg" alt="Artwork 1 "style="height: 300px;" >
            <div class="overlay">
                <a href="sell.php">Shop Now</a>
            </div>
        </div>
        <div class="artwork" >
            <img src="frn2.jpg" alt="Artwork 2"  style="height: 300px;">
            <div class="overlay">
                <a href="sell.php">Shop Now</a>
            </div>
        </div>
        <div class="artwork">
            <img src="frn3.jpg" alt="Artwork 3"style="height: 300px;">
            <div class="overlay">
                <a href="sell.php">Shop Now</a>
            </div>
        </div>
        <div class="artwork">
            <img src="frn4.jpg" alt="Artwork 4"style="height: 300px;">
            <div class="overlay">
                <a href="sell.php">Shop Now</a>
            </div>
        </div>
    </section>

    <section class="faq">
        <h2>Frequently Asked Questions (FAQ)</h2>
        
        <div class="faq-item">
            <h3>1. How do I commission a portrait sketch?</h3>
            <p>To commission a portrait sketch, click on the "Upload Now" button on our homepage. Follow the instructions to provide details about your request, such as size, style, and any specific preferences you have. Once you've submitted your request, our artist will get in touch with you to discuss the details further.</p>
        </div>

        <div class="faq-item">
            <h3>2. What is the cost of commissioning a portrait sketch?</h3>
            <p>Our pricing varies depending on the size, complexity, and style of the portrait sketch you desire. You can expect to receive a personalized quote after submitting your commission request. Keep an eye out for special offers and discounts, such as our current 40% OFF promotion!</p>
        </div>

        <div class="faq-item">
            <h3>3. Can I see examples of the artist's previous work?</h3>
            <p>Yes, you can explore our Art Gallery section to view a selection of our artist's previous sketches and artworks. This will give you an idea of their style and capabilities.</p>
        </div>

        <div class="faq-item">
            <h3>4. How long does it take to complete a commissioned sketch?</h3>
            <p>The time required for completion depends on the complexity and size of the sketch, as well as the artist's current workload. Typically, sketches can take anywhere from a few days to a few weeks. The artist will provide you with a more accurate estimate during the commissioning process.</p>
        </div>

        <div class="faq-item">
            <h3>5. What payment methods do you accept?</h3>
            <p>We accept a variety of payment methods, including major credit cards, PayPal, and bank transfers. Our artist will provide you with payment details and options when you receive your quote.</p>
        </div>

        <div class="faq-item">
            <h3>6. Can I request revisions to the sketch?</h3>
            <p>Yes, we want to ensure that you are completely satisfied with your portrait sketch. After receiving the initial sketch, you can discuss any desired revisions with the artist. Please note that minor revisions are usually included in the cost.No changes will be made after the item is delivered</p>
        </div>

        <div class="faq-item">
            <h3>7. How will I receive the final sketch?</h3>
            <p>Once the sketch is complete and any revisions have been made, the final artwork will be delivered by post in the address.The delivery and the packing cost will be charged from the customer.</p>
        </div>

        <div class="faq-item">
            <h3>8. What if I have more questions or need assistance?</h3>
            <p>If you have any further questions or need assistance, please don't hesitate to contact us. You can use the "Contact Us" option in the navigation bar, and our friendly team will be happy to help you.</p>
        </div>
    </section>
    <br>

    <footer>
            <p style="text-align: center;font-size: 20px; padding: 10px;background-color: rgba(0,0,0,0.5);border: rgba(0,0,0,0.5);border-radius: 10px;">
                
                
                &copy; 2023 Your Website Name. All rights reserved.</p>

    </footer>
    <div>
        <pre>                                                                         

        </pre>
    </div>

    </body>
    </html>
