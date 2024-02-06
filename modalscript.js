const showButton = document.getElementById("showDialog");
const loginDialog = document.getElementById("loginDialog");


const loginForm = loginDialog.querySelector("form")
const loginCancel = loginDialog.querySelector(".cancelbtn")
const loginConfirm = loginDialog.querySelector("#confirmBtn")

const registerShow = loginDialog.querySelector("#registerBtn")
const registerDialog = document.getElementById("registerDialog")
const registerCancel = registerDialog.querySelector(".cancelbtn")
const registerForm = registerDialog.querySelector("form")
const registerToLogin = registerDialog.querySelector("#registerToLogin")
// //warnings
// let emailError = document.getElementById('emailError');
// let passwordError = document.getElementById('passwordError');
// let confirmPasswordError = document.getElementById('confirmPasswordError');


// // Function to open the modal
function openModal(modal) {
    modal.style.display = 'block';
    }

// Function to close the modal
function closeModal(modal) {
    modal.style.display = "none";
}

// Event listener for the showButton to open the loginDialog
showButton.addEventListener("click", () => {
        let formData = new FormData();
        formData.append("action", "checkcookie");
        fetch(window.location.origin + "/api/userlogin.php", {
                method: "POST",
                body: formData,
                credentials: 'include' // Include cookies in the request
        })
                .then(response => response.json()) // Parse response as JSON
                .then(data => {
                        if(data.status){
                                window.location.href = "index.php";
                        }else{
                                openModal(loginDialog);
                        }
                })
                .catch(error => {
                        openModal(loginDialog);
                        console.log("Error:", error);
                })
    
});

// Event listener for the loginCancel button to close the loginDialog
loginCancel.addEventListener("click", () => {
    closeModal(loginDialog);
});

loginForm.addEventListener('submit', function(event) {
        event.preventDefault()

        let formElements = event.target.elements;
        // Access form fields by their names
        let email = formElements["email"].value;
        let password = formElements["password"].value;
        let remember = formElements["remember"].checked;

        // let post = true;


        // Get the current URL path
        // const currentPath = window.location.pathname;
        // const newPath = currentPath.substring(0, currentPath.lastIndexOf('/'));
        // Create a new FormData object
        let formData = new FormData();
        formData.append("action", "login");
        formData.append("email", email);
        formData.append("password", password);
        if(remember){
                formData.append("action-remember", "true");
        }else{
                formData.append("action-remember", "false");
        }
        console.log(window.location.origin + "/api/userlogin.php")
        fetch(window.location.origin + "/api/userlogin.php", {
                method: "POST",
                body: formData,
                credentials: 'include' // Include cookies in the request
        })
                .then(response => response.json()) // Parse response as JSON
                .then(data => {
                        alert(data.message);
                        if(data.status){
                                window.location.href = "index.php";
                        }
                })
                .catch(error => {
                        console.log("Error:", error);
                })




        });
    



registerShow.addEventListener("click", () => {
        console.log("registerShow");
    closeModal(loginDialog);
    openModal(registerDialog);
});

registerToLogin.addEventListener("click", () => {
    closeModal(registerDialog);
    openModal(loginDialog);
});

registerCancel.addEventListener("click", () => {
    closeModal(registerDialog);
});


registerForm.addEventListener('submit', function(event) {
        event.preventDefault();

        let formElements = event.target.elements;
        console.log(formElements);
        // Access form fields by their names
        let email = formElements["email"].value;
        let password = formElements["password"].value;
        let confirmPassword = formElements["confirmPassword"].value;

        // Regular expression patterns for email and password validation
        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        const passwordPattern = /^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/;

        let post = true; //is ready to post?
        // Check if email is valid
        if (!emailPattern.test(email)) {
                console.log("Neplatný email");
                // Add warning above the email input
                emailError.textContent = "Prosím zadejte platný email.";
                post = false;
        } else {
                // Remove any existing warning above the email input
                emailError.textContent = "";
        }

        // Check if password is valid
        if (!passwordPattern.test(password)) {
                console.log("Neplatné heslo. Heslo musí obsahovat alespoň 8 znaků, včetně jednoho písmene a jednoho čísla.");
                // Add warning above the password input
                passwordError.textContent = "Neplatné heslo. Heslo musí obsahovat alespoň 8 znaků, včetně jednoho písmene a jednoho čísla.";
                post = false;
        } else {
                // Remove any existing warning above the password input
                passwordError.textContent = "";
        }

        // Check if passwords match
        if (password !== confirmPassword) {
                console.log("Hesla se neshodují");
                // Add warning above the confirmPassword input
                confirmPasswordError.textContent = "Hesla se neshodují";
                post = false;
        } else {
                // Remove any existing warning above the confirmPassword input
                confirmPasswordError.textContent = "";
        }

        if (!post) {
                return;
        }


        // Get the current URL path
        const currentPath = window.location.pathname;
        const newPath = currentPath.substring(0, currentPath.lastIndexOf('/'));

        // Create a new FormData object
        const formData = new FormData();
        formData.append("action", "register");
        formData.append("email", email);
        formData.append("password", password);
        formData.append("confirmPassword", confirmPassword);
        console.log(newPath + "/api/register.php")
        fetch(window.location.origin + "/api/userlogin.php", {
                method: "POST",
                body: formData
        })
                .then(response => response.json()) // Parse response as JSON
                .then(data => {
                        alert(data.message);
                })
                .catch(error => {
                        console.log("Error:", error);
                })
        });


        // if (isset($_COOKIE['rememberme'])) {
        //         list($userID, $token) = explode(':', $_COOKIE['rememberme']);
        //         $hashedToken = hash('sha256', $token);
            
        //         // Look for a matching token in your database
        //         // Replace this with your actual database code
        //         $stmt = $db->query('SELECT * FROM user_tokens WHERE user_id = ? AND token = ?', [$userID, $hashedToken]);
        //         $userToken = $stmt->fetch();
            
        //         if ($userToken) {
        //             // The token is valid, log the user in
        //             session_start();
        //             $_SESSION['user'] = $userID;
        //         }
        //     }