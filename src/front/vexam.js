/* Check whether user is instructor; if not, redirect */
var _uname = getCookie("userName");
var _utype = getCookie("userType");
var _uid = getCookie("dbID");
var _eid = getCookie("activeReviewExam");

if(_utype != "instructor")
    window.location.href = "login.html";
if(_eid == "")
    window.location.href = "instructor.html";

/* Now that user is authorized to see the page, render header */
window.onload = function() 
{
    renderHeader(_uname);
    getPageRenderData();
}

let examInfo = null; /* store exam information */


/* getPageRenderData() - will collect necessary class and exam information */
function getPageRenderData()
{
    console.log("called!");
    var xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function() 
    {
        if (this.readyState === XMLHttpRequest.DONE && this.status === 200) 
        {
            /* Received the HomeInfo array */
            console.log(this.responseText);
            examInfo = JSON.parse(this.responseText);
            document.getElementById("ename").innerHTML = examInfo['name'];
            document.getElementById("cname").innerHTML = examInfo['course'] + ' ' + examInfo['section'];
        }
    };
    xhr.open("GET", 'data.php?data=exam&id=' + _eid, true);
    xhr.send();
}

/* begin autograding */
function beginAutograde()
{
    if(examInfo['status'] < 3)
        document.getElementById("error-label").innerHTML = "This exam has already been autograded.";
    else
    {
        if(examInfo['status'] == 4)
            var r = confirm("Some students haven't submitted their work. Autograding now will treat their submissions as non-existent.\nDo you want to continue?");
        if(r == true || examInfo['status'] == 3)
        {
            /*  send a POST request to mid to start grading
            mid will have to get eid's data it needs from back,
            manipulate it, and then send it back to back for update
            back updates and returns a result which if successful
            means mid passes back the grader */
            var xhr = new XMLHttpRequest();
            xhr.open("POST", 'data.php', true);

            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded"); //We're sending JSON data in a string
            xhr.onreadystatechange = function() 
            {
                if (this.readyState === XMLHttpRequest.DONE && this.status === 200) 
                {
                    console.log(this.responseText);
                    if(this.responseText === "success")
                        window.location.href = 'instructor.html';
                    else
                        document.getElementById("error-label").innerHTML = "DBG: Error with autograde!";
                }
            };

            xhr.send("data=autograde&id=" + _eid); //send the JSON
        }
    }
    
}

/* review a previously released exam */
function reviewExam(index)
{
    console.log("This functionality isn't implemented yet!");
}


function releaseScores()
{
    if(examInfo['status'] > 1)
        document.getElementById("error-label").innerHTML = "This exam has not received feedback yet.";
    else if(examInfo['status'] < 1)
        document.getElementById("error-label").innerHTML = "This exam has already been released.";
    else
    {
        var xhr = new XMLHttpRequest();
        xhr.open("POST", 'data.php', true);

        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded"); //We're sending JSON data in a string
        xhr.onreadystatechange = function() 
        {
            if (this.readyState === XMLHttpRequest.DONE && this.status === 200) 
            {
                console.log(this.responseText);
                if(this.responseText == 'success')
                    window.location.href = 'instructor.html';
                else
                    document.getElementById("error-label").innerHTML = "There was an error releasing the results.";
            }
        };

        xhr.send("data=release&id=" + _eid); //send the JSON
    }
}

/* begin feedback */
function beginFeedback()
{
    if(examInfo['status'] > 2)
        document.getElementById("error-label").innerHTML = "This exam has not been autograded yet.";
    /*
    else if(examInfo['status'] < 2)
        document.getElementById("error-label").innerHTML = "This exam has already received feedback.";
    */
    else
    {
        window.location.href = 'efeedback.html';
    }
}