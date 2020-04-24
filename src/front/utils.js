//JS cookie functions from https://www.w3schools.com/js/js_cookies.asp

function getCookie(cname) 
{
    var name = cname + "=";
    var decodedCookie = decodeURIComponent(document.cookie);
    var ca = decodedCookie.split(';');
    for(var i = 0; i <ca.length; i++) 
    {
        var c = ca[i];
        while (c.charAt(0) == ' ')
            c = c.substring(1);

        if (c.indexOf(name) == 0) 
            return c.substring(name.length, c.length);
    }
    return "";
}

function setCookie(cname, cvalue, exdays) 
{
    var d = new Date();
    d.setTime(d.getTime() + (exdays*24*60*60*1000));
    var expires = "expires="+ d.toUTCString();
    document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}

function clearAllCookies()
{
    var d = new Date();
    setCookie("userType", "", d-1000);
    setCookie("userName", "", d-1000);
    setCookie("dbID", "", d-1000);
    setCookie("activeTakeExam", "", d-1000);
    setCookie("activeReviewExam", "", d-1000);
    setCookie("activeClassID", "", d-1000);


}

function renderHeader(userName)
{
    document.getElementById("header-brand").innerHTML = "WebQuiz";
    var nameLabel = document.getElementById("header-name");
    if(userName)
    {
        var btn = document.createElement("button");
        btn.innerHTML = "Log Out";
        btn.setAttribute("onclick", "logOut();");
        document.getElementById("header-logout").appendChild(btn);
        nameLabel.innerHTML = userName;
    }
    else
        nameLabel.innerHTML = "Not logged in.";
}

function logOut()
{
    clearAllCookies();
    window.location.href = 'login.html';
}