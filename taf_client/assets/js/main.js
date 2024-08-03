// functions
function post_request_function(route, params, call_back) {
    var formdata = new FormData()
    for (const key in params) {
        formdata.append(key, params[key]);
    }
    loading = true;
    var base_url = "http://localhost/armatan/e_tax_backend/v2.0/";
    var xhr = new XMLHttpRequest();
    xhr.open("POST", base_url + route, true);
    xhr.send(formdata);

    xhr.onreadystatechange = function () {
        loading = false;
        if (this.readyState != 4) return;

        if (this.status == 200) {
            var data = JSON.parse(this.responseText);
            // console.log(data);
            call_back(data)
        } else {
            alert("Erreur inconnu! Merci de vérifier votre connexion. Il se pourrait que la page demandée soit indisponible")
        }
    };
}
function post_request_function_with_token(route, params, call_back) {
    var formdata = new FormData()
    for (const key in params) {
        formdata.append(key, params[key]);
    }
    loading = true;
    var base_url = "http://localhost/armatan/e_tax_backend/v2.0/";
    var xhr = new XMLHttpRequest();
    xhr.open("POST", base_url + route, true);
    xhr.setRequestHeader("Authorization", "Bearer "+token);
    xhr.send(formdata);
    xhr.onreadystatechange = function () {
        loading = false;
        if (this.readyState != 4) return;

        if (this.status == 200) {
            var data = JSON.parse(this.responseText);
            // console.log(data);
            call_back(data)
        } else {
            alert("Erreur inconnu! Merci de vérifier votre connexion. Il se pourrait que la page demandée soit indisponible")
        }
    };
}

function taf_test_auth(){
    post_request_function_with_token(
        "taf_auth/auth_test.php",
        {},
        (data) => {
            if (data["status"]) {
                console.log(data)
                alert("Notre session de connexion est toujours valide")
            } else {
                console.log("Erreur interne ",data)
                alert("Vous n'êtes plus connecté")
            }
        }
    );
}

// traitements
const loginButton = document.querySelector('#taf_valide_button');
const form = document.forms[0];
let token=null;

loginButton.addEventListener('click', async (e) => {
    e.preventDefault();

    post_request_function(
        "taf_auth/auth.php",
        {
            email: form.taf_email_input.value,
            password:form.taf_password_input.value
        },
        (data) => {
            if (data["status"]) {
                console.log(data)
                token=data.data
            } else {
                console.log("Erreur interne ",data)
            }
        }
    );
});
