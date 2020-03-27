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

let activeClassID = -1; /* the active ID of the class to render exams for */
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
            homeInfo = JSON.parse(this.responseText);
            renderPageElement('classes');
            renderPageElement('exams');
        }
    };
    xhr.open("GET", 'data.php?data=home&instructor=' + _uid, true);
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
        else 
        {
            let examList = homeInfo[1].filter(exam => exam['course'] == homeInfo[0][activeClassID]['course'] && exam['section'] == homeInfo[0][activeClassID]['section']);
        
            if(examList.length == 0)
            {
                errorLabel.innerHTML = "You have no exams on this class.";
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
                visualExam.setAttribute("onclick", "reviewExam(" + i + ");");
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

/* setActiveExamID - sets the active exam ID to the clicked exam on the right flexbox */
function reviewExam(index)
{
    setCookie("activeReviewExam", homeInfo[1][index]['id'], 100);
    document.location.href = 'vexam.html';
    //console.log("You're now viewing exam: " + homeInfo[1][index]['id']);
}