/* Check whether user is instructor; if not, redirect */
var _uname = getCookie("userName");
var _utype = getCookie("userType");
var _uid = getCookie("dbID");

if(_utype != "instructor")
    window.location.href = "login.html";

/* Now that user is authorized to see the page, render header */
window.onload = function() 
{
    if(this.parent == this)
        renderHeader(_uname);
    getPageRenderData();
}

var questionList = null; /* list of questions */
const difficulties = ["easy", "medium", "hard"];
var filteredQuestions = [];

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
            //console.log(this.responseText);
            questionList = JSON.parse(this.responseText);
            console.log("DBG QLIST: ");
            console.log(questionList);
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

        filter();
        /* createQuestionListVBE(questionList); */

        /*
        /* first clear all the elements in the div 
        while(listBox.childElementCount)
            listBox.firstChild.remove();
        
        /* then draw each of the appropriate exam visual elements 
        for(let i=0; i < questionList.length; i++)
        {
            /* TODO: add exam elements to right flexbox here 
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
            */
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
    console.log("Dropped on " + ev.target);
    ev.target.appendChild(document.getElementById(data));
}

/* called when a new drag starts */
function drag(ev)
{
    console.log("Started drag on qbank");
    ev.dataTransfer.setData("text", ev.target.id);
}

function createQuestionVBE(question, index, isDraggable=true) {
    var wrapper = document.createElement("div");
    wrapper.setAttribute("class", "question visual-question");
    wrapper.setAttribute("id", "q-" + index);
    wrapper.setAttribute("draggable", isDraggable);
    wrapper.setAttribute("ondragstart", "drag(event);");

    var table = document.createElement("table");

    /* Populate table with question object data */
    var firstRow = document.createElement("tr");

    /* First row has creator and difficulty */
    var creator = document.createElement("td");
    creator.innerHTML = "👤 " + question["creatorName"];
    firstRow.appendChild(creator);
    var difficulty = document.createElement("td");
    difficulty.innerHTML = "🧠 " + difficulties[question["difficulty"]].padEnd(6);
    firstRow.appendChild(difficulty);

    table.appendChild(firstRow);

    /* Second row has topic and constraint */
    var secondRow = document.createElement("tr");

    var creator = document.createElement("td");
    creator.innerHTML = "📚 " + question["topic"];
    secondRow.appendChild(creator);
    var difficulty = document.createElement("td");
    difficulty.innerHTML = "🔒 " + question["constraintName"];
    secondRow.appendChild(difficulty);

    table.appendChild(secondRow);

    /* Third row has the prompt */
    var thirdRow = document.createElement("tr");
    thirdRow.setAttribute("class", "question-prompt");

    var prompt = document.createElement("td");
    prompt.innerHTML = "🛈 " + question["prompt"];
    thirdRow.appendChild(prompt);

    table.appendChild(thirdRow);

    wrapper.appendChild(table);
    return wrapper;
}

function createQuestionListVBE(questions=[], isDraggable=true, trueIDs=[]) {
    var wrapper = document.getElementById("question-list-box");
    while(wrapper.childElementCount)
            wrapper.firstChild.remove();

    for(let i=0; i < questions.length; i++) {
        console.log("WANNADIE " + questions[i]['id']);
        wrapper.appendChild(createQuestionVBE(questions[i], trueIDs[i], isDraggable));
    }
}

function filter() {
    /* triggered from changing text on search box, or (un)selected difficulties */
    filteredQuestions = questionList;
    var words = document.getElementById("sb-keywords").value.split(" ");
    filteredQuestions = filteredQuestions.filter(question => words.every(el => question["prompt"].includes(el)));
    console.log("FILTERS");
    console.log(filteredQuestions);

    var d = document.getElementById("ls-difficulty");
    var t = document.getElementById("ls-topic");
    d = Array.prototype.map.call(d.selectedOptions, function(x){ return x.value });
    t = Array.prototype.map.call(t.selectedOptions, function(x){ return x.value });

    filteredQuestions = filteredQuestions.filter(question => d.some(el => question["difficulty"] == difficulties.indexOf(el)));
    console.log(filteredQuestions);

    filteredQuestions = filteredQuestions.filter(question => t.some(el => question["topic"] == el));
    console.log(filteredQuestions);
    

    // want to find all the indices of things in filteredQuestions that have an ["id"] that's in questionList
    var trueIDs = [];
    var finalQuestions = [];
    filteredQuestions.forEach(fq => {
        console.log("OBJ BELOW");
        console.log(questionList.filter(q => q["id"] == fq["id"])[0]);
        console.log("IDX: " + questionList.indexOf(questionList.filter(q => q["id"] == fq["id"])[0]));
        console.log(fq["id"]);
        var trueID = questionList.indexOf(questionList.filter(q => q["id"] == fq["id"])[0]);
        if(parent.document.getElementById("q-" + trueID) == null) {
            finalQuestions.push(fq);
            trueIDs.push(trueID);
        }
    });
    console.log("TRUE IDS: " + trueIDs);

    createQuestionListVBE(finalQuestions, true, trueIDs);
}