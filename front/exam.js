var type = getCookie("userType");
if(type != "student")
    window.location.href = type + '.html';
var exam = getCookie("currentExam");
if(exam == "")
    window.location.href = 'student.html';

window.onload = function() 
{
    document.getElementById("student").innerHTML += getCookie("dbID");
    document.getElementById("exam").innerHTML += getCookie("currentExam");
    this.getExam();
};

function getExam() 
{
    var xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function() 
    {
        if (this.readyState === XMLHttpRequest.DONE && this.status === 200) 
        {
            //console.log(this.responseText);
            questions = JSON.parse(this.responseText);
            for(let i = 0; i < questions.length; i++)
                createExamQuestionElement(questions[i], i);
        }
    };
    xhr.open("GET", 'data.php?value=exam&id=' + getCookie('currentExam') + '&student=' + getCookie('dbID'), true);
    xhr.send();
}

function createExamQuestionElement(question, id)
{
    var el = document.getElementById("q"+id);
    if(el == null)
    {
        var container = document.createElement("div");
        container.setAttribute("id", "q"+id);
        container.setAttribute("class", "question");
        //container.setAttribute("draggable", "true");
        var title = document.createElement("h2");
        title.innerHTML = "Question #" + (id + 1) + " (" + question['maxPoints'] + " points)";
        var submissionArea = document.createElement("textarea");
        submissionArea.setAttribute("id", "s"+id);
        submissionArea.setAttribute("class", "submissionArea");
        var label = document.createTextNode(question['prompt']);
        container.appendChild(title);
        container.appendChild(label);
        container.appendChild(submissionArea);

        document.body.insertBefore(container, document.getElementById('end'));
    }
}

function submitExam()
{
    if(confirm("You are about to submit your exam. Make sure that you're satisfied with all your answers since you will not be able to change them after this point.\n\nDo you want to proceed?"))
        console.log("Submitting the exam!");
    else
        console.log("Cancelled submission.");
}