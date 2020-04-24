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

let homeInfo = null;
let activeClassID = -1;
let examQuestionIDs = [];

/* getPageRenderData() - will collect necessary class and exam information */
function getPageRenderData()
{
    var xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function() 
    {
        console.log("CALLED!");
        if (this.readyState === XMLHttpRequest.DONE && this.status === 200) 
        {
            /* Received the HomeInfo array */
            homeInfo = JSON.parse(this.responseText);
            renderPageElement('classes');
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
}

/* setActiveClassID - sets the variable once an element is clicked on the class list */
function setActiveClassID(index)
{
    activeClassID = homeInfo[0][index]['id'];
}

/* checks to see whether a drop was done to an external page or to itself */
function checkDrop(ev)
{
    console.log("Checking drop!");
    ev.preventDefault();
    var data = ev.dataTransfer.getData("text");
    console.log("TARGET: " + ev.target);
    console.log("TARGET CLASS: " + ev.target.className + " | TARGET ID: " + ev.target.id + " | TARGET TAG NAME: " + ev.target.tagName);
    console.log("DATA (ID): " + data);
    if(ev.target.className == "visual-question" || ev.target.id == "exam-list-box" || ev.target.tagName == "TD" || ev.target.tagName == "TR")
    {
        var moved = document.getElementById("qbank").contentDocument.getElementById(data);
        addToExamListVBE(moved.id);
        /*
        moved.innerHTML += '<br>Points:'
        var pointsLabel = document.createElement("input");
        pointsLabel.setAttribute("id", "p"+moved.id);
        moved.appendChild(pointsLabel);
        document.getElementById("exam-list-box").appendChild(moved);
        */
    }
}

/* called when a new drag starts */
function drag(ev)
{
    console.log("Starting drop!");
    ev.dataTransfer.setData("text", ev.target.id);
}

function allowDrop(ev) 
{
    ev.preventDefault();
}

function validateInput()
{
    var errorLabel = document.getElementById("error-label");
    var name = document.getElementById("name");

    if(activeClassID == -1)
        errorLabel.innerHTML = "Please select a valid class first.";
    else if(name.value.length == 0 || name.value.length > 64)
        errorLabel.innerHTML = "Exam name must be nonempty and less than or equal to 64 characters.";
    else
        attemptDispatchExam(name.value);
}

function attemptDispatchExam(name)
{
    var examQuestionList = document.getElementById("exam-list-box");
    var eqList = [];
    var qList = document.getElementById("qbank").contentWindow.questionList;
    for(let i=0; i < examQuestionList.childElementCount; i++)
    {
        /* TODO: create the exam items to send here */
        console.log(examQuestionList.children[i].id.slice(3,));
        var eq = {"id": qList[examQuestionList.children[i].id.slice(3,)]['id'],
                  "maxPoints": document.getElementById("pq-"+examQuestionList.children[i].id.slice(3,)).value,
                  "name": name,
                  "cid": activeClassID.toString()
                };
        eqList.push(eq);
    }

    /* try to insert the valid question into database */
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
                document.getElementById("error-label").innerHTML = "That exam could not be added, make sure that you are not entering the name of an exam that already exists for this class.";
        }
    };

    xhr.send("data=exam&exam=" + JSON.stringify(eqList)); //send the JSON
}


function createExamVBE(qID=None, index) {
    var wrapper = document.createElement("div");
    wrapper.setAttribute("class", "exam");
    wrapper.setAttribute("id", "e" + qID);

    var table = document.createElement("table");
    var tr = document.createElement("tr");

    //tr.appendChild(document.getElementById(qID));
    //document.getElementById("qbank").contentDocument.getElementById(data);
    console.log("QID: " + qID);
    tr.appendChild(document.getElementById("qbank").contentDocument.getElementById(qID));
    var points = document.createElement("td");
    var ta = document.createElement("input");
    ta.setAttribute("class", "exam-question-points");
    ta.setAttribute("id", "p" + index);
    ta.setAttribute("maxlength", 3);
    /* can access the points array by selecting by class name then .value */

    points.appendChild(ta);
    tr.appendChild(points);
    table.appendChild(tr);

    wrapper.appendChild(table);
    return wrapper;
}

function addToExamListVBE(qID) {
    var wrapper = document.getElementById("exam-list-box");
    /*
    while(wrapper.childElementCount)
            wrapper.firstChild.remove();
    */
    wrapper.appendChild(createExamVBE(qID, qID));

}