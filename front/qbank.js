/* Check whether user is instructor; if not, redirect */
var _uname = getCookie("userName");
var _utype = getCookie("userType");
var _uid = getCookie("dbID");

if(_utype != "instructor")
    window.location.href = "login.html";

/* Now that user is authorized to see the page, render header */
window.onload = function() 
{
    renderHeader(_uname);
    getPageRenderData();
}

let questionList = null; /* list of questions */


/* getPageRenderData() - will collect necessary class and exam information */
function getPageRenderData()
{
    console.log("called!");
    var xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function() 
    {
        if (this.readyState === XMLHttpRequest.DONE && this.status === 200) 
        {
            /* Received the questionList array */
            questionList = JSON.parse(this.responseText);
            renderPageElement('questions');
        }
    };
    xhr.open("GET", 'data.php?data=bank', true);
    xhr.send();
}

/* renderPageElement() - will render the specified element(s) on the page */
function renderPageElement(type)
{
    if(type === "questions")
    {   
        let listBox = document.getElementById("question-list-box");
        const difficulties = ["easy", "medium", "hard"];

        /* first clear all the elements in the div */
        while(listBox.childElementCount)
            listBox.firstChild.remove();
        
        /* then draw each of the appropriate exam visual elements */
        for(let i=0; i < questionList.length; i++)
        {
            /* TODO: add exam elements to right flexbox here */
            let visualExam = document.createElement("button");
            visualExam.setAttribute("id", "vq"+i);
            visualExam.setAttribute("class", "visual-question");
            visualExam.setAttribute("draggable", "true");
            visualExam.setAttribute("ondragstart", "drag(event);");
            visualExam.innerHTML = 'Prompt: ' + questionList[i]['prompt'] + 
                                    '<br>Difficulty: ' + difficulties[questionList[i]['difficulty']] +
                                    '<br>Creator: ' + questionList[i]['creatorName'] +
                                    '<br>Topic: ' + questionList[i]['topic'];
            listBox.appendChild(visualExam);
        }
    }
}

/* setActiveClassID - sets the variable once an element is clicked on the class list */
function setActiveClassID(index)
{
    activeClassID = index;
    renderPageElement("exams");
}

/* setActiveExamID - sets the active exam ID to the clicked exam on the right flexbox */
function reviewExam(index)
{
    /* TODO: set cookie and redirect to 'vexam.html` */
    console.log("You're now viewing exam: " + homeInfo[1][index]['id']);
}

/* checks to see whether a drop was done to an external page or to itself */
function checkDrop(ev)
{
    ev.preventDefault();
    var data = ev.dataTransfer.getData("text");
    ev.target.appendChild(document.getElementById(data));
}

/* called when a new drag starts */
function drag(ev)
{
    ev.dataTransfer.setData("text", ev.target.id);
}