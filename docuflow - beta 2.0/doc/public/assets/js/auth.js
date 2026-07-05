document.getElementById('policy').onclick = function (e) {
    e.preventDefault();
    document.querySelector('.overlay').classList.remove('model-show');
    document.querySelector('.model-box').classList.remove('model-show');
}
function hideModel() {
    document.querySelector('.overlay').classList.add('model-show');
    document.querySelector('.model-box').classList.add('model-show');
    document.querySelector('.form-check-input').checked = (false);
}

function hideModelAndAcceprt() {
    hideModel();
    document.querySelector('.form-check-input').checked = (true);
}

App.returnYear();

function showSuccessModal() {
    const successModal = document.getElementById('successModal');
    const progress = document.querySelector('[class="progress"]');
    const check = document.querySelector('[class="check"]');
    successModal.style.cssText = "display: flex";
    progress.style.cssText = "animation: spin .9s forwards;";
    check.style.cssText = "animation: spin 2s forwards;";

    setTimeout(()=> location.replace("/doc/public/pages/dashboard/dashboard.php"), 1000);
}

document.querySelector('form').addEventListener('submit', function(event) {
    event.preventDefault();
    // Declaring Variables
    const email = document.getElementById('exampleInputEmail1');
    const password = document.getElementById('exampleInputPassword1');
    const checkbox = document.querySelector('input[type="checkbox"]');
    passRgx = /^(?!.*[\s`|/\\'"]{1})(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{6,35}$/g
    emRgx = /^(?!.*[._-]{2})[a-zA-Z](([a-zA-Z0-9._-]+)?[a-zA-Z0-9]+)?@(?=[a-zA-Z]+)([\w-._]+)?[a-zA-Z]+\.[a-zA-Z]+$/g;

// create a vlaidate function input
    function validateInput(input, type='', rgx) {
        if (type === 'email') {
            if (input.value === '' || !rgx.test(input.value)) {
                input.classList.add('error-input');
                document.getElementById('emailHelp').classList.add('error-message');
                document.getElementById('emailHelp').textContent = translations[currentLang].typeValidEmail;
                return false
            } else {
                input.classList.remove('error-input');
                document.getElementById('emailHelp').classList.remove('error-message');
                document.getElementById('emailHelp').textContent = translations[currentLang].emailHelpText;
                return true
            }

        }else if (type === 'password') {
            if (input.value === '' || !rgx.test(input.value)) {
                input.classList.add('error-input');
                document.getElementById('passwordHelp').classList.add('error-message');
                document.getElementById('passwordHelp').textContent = translations[currentLang].passwordRequirements;
                return false;
            } else {
                input.classList.remove('error-input');
                document.getElementById('passwordHelp').classList.remove('error-message');
                document.getElementById('passwordHelp').textContent = '';
                return true;
            }
        }else{
            console.log('place full all argument')
            return false;
        }
    }

    // Check Checkbox and Send Data to Server

if (validateInput(email, 'email', emRgx) && validateInput(password, 'password', passRgx)) {
    if (!checkbox.checked) {
        checkbox.classList.add('error-checkbox')
        return false;
    }
    document.querySelector('button[type="submit"]').setAttribute('disabled', '');
    let loginData = {
        email: email.value,
        password: password.value,
        agreet: checkbox.checked,
    };
        // data Variable
        
        const url = '/doc/api/index.php';
        const data = new FormData();
        data.append('email', email.value);
        data.append('password', password.value);
        data.append('agreet', checkbox.checked);
        
        // Send Data to Server
        fetch(url, {
            method: 'POST',
            body: data,
        }).then(res => (res.ok === true && res.status == 200) ? res.json() : false)
        .then(response => {
             document.querySelector('button[type="submit"]').removeAttribute('disabled');
            if (response.status === 'success'){
                showSuccessModal();
                // return false
            }
            if (response.status === 'error'){
                email.classList.add('error-input');
                document.getElementById('emailHelp').classList.add('error-message');
                document.getElementById('emailHelp').textContent = response.message;
                return false

            }
        })
        .catch(error => {
            document.querySelector('button[type="submit"]').removeAttribute('disabled');
            console.error('Login error:', error);
        })
    }
});