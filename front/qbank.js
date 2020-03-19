//Redirect non-instructors to login
if(getCookie("userType") != "instructor")
    window.location.href = 'login.html';

var questions;

function getQuestions() 
{
    var xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function() 
    {
        if (this.readyState === XMLHttpRequest.DONE && this.status === 200) 
        {
            questions = JSON.parse(this.responseText);
            //questions.sort((a, b) => b['difficulty'] > a['difficulty']);
            for(let i = 0; i < questions.length; i++)
                createQuestionElement(questions[i], i);
        }
    };
    xhr.open("GET", 'data.php?value=qbank', true);
    xhr.send();
}

function createQuestionElement(question, id)
{
    var el = document.getElementById("q"+id);
    if(el == null)
    {
        var container = document.createElement("div");
        container.setAttribute("id", "q"+id);
        container.setAttribute("class", "question");
        //container.setAttribute("draggable", "true");
        var label = document.createTextNode(question['prompt']);
        container.appendChild(label);

        document.body.insertBefore(container, document.getElementById('end'));
    }
}