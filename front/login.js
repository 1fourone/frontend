//JS for login-related code
var type = getCookie("userType");
if(type != "")
    window.location.href = type + '.html';

function attemptLogin() 
{
    var uname = document.getElementById("uname").value;
    var pw = document.getElementById("pw").value;

    var xhr = new XMLHttpRequest();
    xhr.open("POST", 'http://1fourone.io/webgrader/front/login.php', true);

    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded"); //We're sending JSON data in a string
    xhr.onreadystatechange = function() 
    {
        if (this.readyState === XMLHttpRequest.DONE && this.status === 200) 
        {
            response = JSON.parse(this.responseText);
            if(response['result'] == "success") 
            {
                response['name'] = uname;
                loadHomePageForUser(response);
            }
            else
            alert("Invalid credentials");
        }
    };

    xhr.send("credentials=" + JSON.stringify({name: uname, plain_password: pw})); //send the JSON
}

function loadHomePageForUser(credentials) 
{
    console.log("loading credentials for user!");
    document.cookie = "userType=" + credentials['type'] + "; path=/";
    document.cookie = "userName=" + credentials['name'] + "; path=/";
    document.cookie = "dbID="     + credentials['id'] + "; path=/";
    window.location.href = credentials['type'] + '.html';
}