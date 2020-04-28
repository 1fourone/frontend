/* Check whether user is student; if not, redirect */
var _uname = getCookie("userName");
var _utype = getCookie("userType");
var _uid = getCookie("dbID");

if(_utype != "student")
    window.location.href = "login.html";

/* Now that user is authorized to see the page, render header */
window.onload = function() 
{
    renderHeader(_uname);
    getPageRenderData();
}

let activeClassID = -1; /* the active ID of the class to render exams for */
let activeExamType = -1; /* the type of exams to render for the active class */
let activeExamID = -1; /* the db ID for the selected exam */
let homeInfo = null; /* the homeInfo array to store information */


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
            //console.log(this.responseText);
            homeInfo = JSON.parse(this.responseText);
            renderPageElement('classes');
            renderPageElement('exams');
        }
    };
    xhr.open("GET", 'data.php?data=home&student=' + _uid, true);
    xhr.send();
}

/* renderPageElement() - will render the specified element(s) on the page */
function renderPageElement(type)
{
    if(type === "classes")
    {
        let classList = homeInfo[0];
        for(let i=0; i < classList.length; i++)
        {
            let opt = document.createElement("option");
            opt.innerHTML = classList[i]['course'] + '-' + classList[i]['section'];
            opt.setAttribute("value", i);
            opt.setAttribute("onclick", "setActiveClassID(" + i +");");
            document.getElementById("class-select").add(opt);
        }
    }
    else if(type === "exams")
    {
        let errorLabel = document.getElementById("error-label");
        let listBox = document.getElementById("exam-list-box");

        if(activeClassID == -1)
        {
            errorLabel.innerHTML = "Please select a valid class.";
            listBox.style.visibility = "hidden";
        }
        else if(activeExamType == -1)
        {
            errorLabel.innerHTML = "Please select a valid exam type.";
            listBox.style.visibility = "hidden";
        }
        else 
        {
            var examList;
            if(activeExamType != 1)
                examList = homeInfo[1].filter(exam => exam['course'] == homeInfo[0][activeClassID]['course'] && exam['section'] == homeInfo[0][activeClassID]['section']).filter(exam => exam['status'] == activeExamType);
            else 
                examList = homeInfo[1].filter(exam => exam['course'] == homeInfo[0][activeClassID]['course'] && exam['section'] == homeInfo[0][activeClassID]['section']).filter(exam => exam['status'] == 1 || exam['status'] == 2 || exam['status'] == 3);
            
            if(examList.length == 0)
            {
                errorLabel.innerHTML = "You have no exams of this type.";
                listBox.style.visibility = "hidden";
            }
            else
            {
                errorLabel.innerHTML = "";
                listBox.style.visibility = "visible";
            }

            /* first clear all the elements in the div */
            while(listBox.childElementCount)
                listBox.firstChild.remove();
            
            /* then draw each of the appropriate exam visual elements */
            for(let i=0; i < examList.length; i++)
            {
                /* TODO: add exam elements to right flexbox here */
                let visualExam = document.createElement("button");
                visualExam.setAttribute("id", "ve"+i);
                visualExam.setAttribute("class", "visual-exam");
                visualExam.setAttribute("onclick", "setActiveExamID(" + i + ");");
                visualExam.innerHTML = examList[i]['name'];
                listBox.appendChild(visualExam);
            }
        }
    }
}

/* setActiveClassID - sets the variable once an element is clicked on the class list */
function setActiveClassID(index)
{
    activeClassID = index;
    renderPageElement("exams");
}

/* setActiveExamType - sets the variable once an element is clicked on the left flexbox */
function setActiveExamType(status)
{
    activeExamID = -1;
    activeExamType = status;
    renderPageElement("exams");
}

/* setActiveExamID - sets the active exam ID to the clicked exam on the right flexbox */
function setActiveExamID(index)
{
    //The true index of an exam in the array list is interpreted through exam type (active, unreleased, released)
    // A A A U U R R R
    //The 3rd active exam is 2 = 0 + 2
    //The 2nd unreleased exam is 4 = 3 + 1
    //The 2nd released exam is 6 = 3 + 2 + 1
    var padding = 0;
    if(activeExamType >= 4)
        padding = 0;
    else if(padding >= 1)
        padding = homeInfo[1].filter((a) => a['status'] == 4).length;
    else
        padding = homeInfo[1].filter((a) => a['status'] == 4 || a['status'] == 3 || a['status'] == 2 || a['status'] == 1).length;
    activeExamID = "\'" + homeInfo[1][padding + index]['id'] + "\'";
    
}

/* evaluateStudentChoice - called when the student clicks on Go To Exam button */
function evaluateStudentChoice()
{
    if(activeExamID == -1)
        document.getElementById("error-label").innerHTML = "Please select a valid exam first."
    else if(activeExamType == 1)
        console.log("You cannot access that exam until it is released by your instructor.");
    else if(activeExamType == 4)
    {
        if(confirm("You are about to visit an active exam.\nYou cannot leave/resume an active exam once you've opened it, so be sure that you're ready.\n\nAre you ready?"))
        {
            console.log("You are now taking exam " + activeExamID);
            setCookie("activeTakeExam", activeExamID, 1);
            window.location.href = 'exam.html';
            //@TODO: set cookies, redirect to take exam
        }
    }
    else
    {
        console.log("You are now reviewing exam " + activeExamID);
        setCookie("activeReviewExam", activeExamID, 1);
        window.location.href = 'result.html';
        //@TODO: set cookies, redirect to review exam
    }
}