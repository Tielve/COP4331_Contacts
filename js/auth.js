// get login/register buttons and forms
const loginButton = document.getElementById("show-login-button");
const registerButton = document.getElementById("show-register-button");
const loginForm = document.getElementById("login-form");
const registerForm = document.getElementById("register-form");

function showLoginForm() {
  loginForm.hidden = false; 
  registerForm.hidden = true; 

  loginButton.setAttribute("aria-pressed", "true");
  registerButton.setAttribute("aria-pressed", "false");
 
  // sets the users cursor in the text box
  document.getElementById("login-username").focus();
}

function showRegisterForm() {
  registerForm.hidden = false;
  loginForm.hidden = true; 

  loginButton.setAttribute("aria-pressed", "false");
  registerButton.setAttribute("aria-pressed", "true");

  document.getElementById("register-username").focus();
}

// switches forms when a button is clicked
loginButton.addEventListener("click", showLoginForm);
registerButton.addEventListener("click", showRegisterForm);

loginForm.addEventListener("submit", async (event) => {
  event.preventDefault(); // prevents username/password from showing in URL

  const username = document.getElementById("login-username").value;
  const password = document.getElementById("login-password").value;

  try {
    const response = await fetch("http://192.241.156.39/API/login.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "Accept": "application/json" 
      },
      body: JSON.stringify ({
        username: username,
        pw: password
      })
    });

    const data = await response.json();
    if(!response.ok) {
      console.log(data.error);
      return;
    }

    console.log("Login Succesful: ", data);
    // redirect user to contacts page
    // window.location.href = "contacts.html";
  } catch (error) {
    console.error("Login failed: ", error);
  }
});

registerForm.addEventListener("submit", async (event) => {
  event.preventDefault(); 

  const username = document.getElementById("register-username").value;
  const password = document.getElementById("register-password").value;

  try {
    const response = await fetch("http://192.241.156.39/API/addAcct.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "Accept": "application/json"
      },
      body: JSON.stringify({
        username: username,
        pw: password
      })
    });

    const data = await response.json();

    if (!response.ok || data.error) {
      console.error("Registration failed:", data.error);
      return;
    }

    console.log("Registration successful:", data);
    registerForm.reset();
    showLoginForm(); 
  } catch (error) {
    console.error("Registration failed:", error);
  }

});