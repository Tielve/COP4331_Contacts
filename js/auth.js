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