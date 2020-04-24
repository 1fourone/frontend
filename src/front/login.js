/* Check whether user is not already logged in, if so redirect */
var _uname = getCookie("userName");
var _utype = getCookie("userType");
var _uid = getCookie("dbID");

if(_utype == "student" || _utype == "instructor")
    window.location.href = _utype + ".html";

/* Now that user is authorized to see the page, render header */
window.onload = function() 
{
    renderHeader(_uname);
}

/* attemptLogin() - called when user attempts to log in */
function attemptLogin() 
{
    var uname = document.getElementById("uname").value;
    var pw = document.getElementById("pw").value;

    var xhr = new XMLHttpRequest();
    xhr.open("POST", 'login.php', true);

    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded"); //We're sending JSON data in a string
    xhr.onreadystatechange = function() 
    {
        if (this.readyState === XMLHttpRequest.DONE && this.status === 200) 
        {
            console.log(this.responseText);
            response = JSON.parse(this.responseText);
            if(response['result'] == "success") 
            {
                response['name'] = uname;
                loadHomePageForUser(response);
            }
            else
                document.getElementById("error-label").style.visibility = "visible";
        }
    };

    xhr.send("credentials=" + JSON.stringify({name: uname, plain_password: pw})); //send the JSON
}

/* loadHomePageForUser(credentials) - sets up cookies and redirects user to appropriate home page */
function loadHomePageForUser(credentials) 
{
    console.log("loading credentials for user!");
    document.cookie = "userType=" + credentials['type'] + "; path=/";
    document.cookie = "userName=" + credentials['name'] + "; path=/";
    document.cookie = "dbID="     + credentials['id'] + "; path=/";
    window.location.href = credentials['type'] + '.html';
}