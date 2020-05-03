/* Check whether user is instructor; if not, redirect */
var _uname = getCookie("userName");
var _utype = getCookie("userType");
var _uid = getCookie("dbID");
var _eid = getCookie("activeReviewExam");

if(_utype != "student")
    window.location.href = "login.html";
if(_eid == "")
    window.location.href = "student.html";

/* Now that user is authorized to see the page, render header */
window.onload = function() 
{
    if(this.parent == this)
        renderHeader(_uname);
    getPageRenderData();
}

var examInfoList = null; /* list of exam info */
var examsList = [];
var autoFeedbacks = [];


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
            console.log(this.responseText);
            examsList = [];
            examInfoList = JSON.parse(this.responseText);

            let currentID = examInfoList[0]["sid"];
            let curr = [];
            examInfoList.forEach(el => {
                console.log(el["sid"] == currentID);
                if(el["sid"] == currentID) 
                    curr.push(el);
                else {
                    examsList.push(curr);
                    curr = [el];
                    currentID = el["sid"];
                }
                console.log(curr);
            });
            examsList.push(curr);
            createReviewQuestionListVBE(0);
        }
    };
    xhr.open("GET", 'data.php?data=exams&type=student&id=' + _eid, true);
    xhr.send();
}

function getTestCaseString(index, output = false) 
{
    switch(index)
    {
        case 0: return (output) ? "firstOutput" : "firstTestCase";
        case 1: return (output) ? "secondOutput" : "secondTestCase";
        case 2: return (output) ? "thirdOutput" : "thirdTestCase";
        case 3: return (output) ? "fourthOutput" : "fourthTestCase";
        case 4: return (output) ? "fifthOutput" : "fifthTestCase";
        case 5: return (output) ? "sixthOutput" : "sixthTestCase";
    }
    return -1;
}

function createReviewQuestionVBE(index, studentID) {
    var wrapper = document.createElement("div");
    wrapper.setAttribute("class", "question visual-question");
    wrapper.setAttribute("id", "q-" + index);


    var autoFeedback = JSON.parse(examsList[studentID][index]['autoFeedback']);

    var table = document.createElement("table");

    /* Populate table with question object data */
    var firstRow = document.createElement("tr");

    /* First row has max points/received points*/
    firstRow.appendChild(document.createElement("td"));
    var points = document.createElement("td");
    points.setAttribute("class", "pointsDisplay");
    points.innerHTML = "⭐ " + examsList[studentID][index]["pointsReceived"] + " / " + examsList[studentID][index]["maxPoints"];
    firstRow.appendChild(points);

    table.appendChild(firstRow);

    /* Second row has prompt */
    var secondRow = document.createElement("tr");

    var prompt = document.createElement("td");
    prompt.innerHTML = "🛈 " + examsList[studentID][index]["prompt"];
    secondRow.appendChild(prompt);
    table.appendChild(secondRow);

    /* Third row has submission */
    var thirdRow = document.createElement("tr");
    var submission = document.createElement("td");
    var ta = document.createElement("textarea");
    ta.innerHTML = decodeURIComponent(examsList[studentID][index]["submissionText"]);
    ta.setAttribute("class", "submission");
    ta.setAttribute("disabled", "");
    submission.appendChild(ta);
    thirdRow.appendChild(submission);
    table.appendChild(thirdRow);
    wrapper.appendChild(table);

    var outputTable = document.createElement("table");
    var instructor = JSON.parse(examsList[studentID][index]["instructorFeedback"]);
    
    var th = document.createElement("tr");
    var td1 = document.createElement("td");
    td1.innerHTML = "<b>Item</b>";
    var td2 = document.createElement("td");
    td2.innerHTML = "<b>Result</b>";
    var td3 = document.createElement("td");
    td3.innerHTML = "<b>Points Lost</b>";
    var td4 = document.createElement("td");
    td4.innerHTML = "<b>Points Override</b>";
    th.appendChild(td1);
    th.appendChild(td2);
    th.appendChild(td3);
    th.appendChild(td4);
    table.appendChild(th);

    /* function name */
    var tr = document.createElement("tr");
    td1 = document.createElement("td");
    td1.innerHTML = "Function Name";
    td2 = document.createElement("td");
    td2.innerHTML = (autoFeedback["name"] == 0) ? "Passed" : "Failed";
    td2.style.color = (autoFeedback["name"] == 0) ? "#3eb53e" : "#c83a3a";
    td3 = document.createElement("td");
    td3.innerHTML = autoFeedback["name"];
    td3.setAttribute("class", "old-points-" + index);
    td4 = document.createElement("td");
    var override = document.createElement("input");
    override.setAttribute("class", "points points-" + index);
    override.value = instructor["points"][0];
    override.setAttribute("disabled", "");
    td4.append(override);
    tr.appendChild(td1);
    tr.appendChild(td2);
    tr.appendChild(td3);
    tr.appendChild(td4);
    table.appendChild(tr);

    /* colon */
    var tr = document.createElement("tr");
    td1 = document.createElement("td");
    td1.innerHTML = "Colon";
    td2 = document.createElement("td");
    td2.innerHTML = (autoFeedback["colon"] == 0) ? "Passed" : "Failed";
    td2.style.color = (autoFeedback["colon"] == 0) ? "#3eb53e" : "#c83a3a";
    td3 = document.createElement("td");
    td3.innerHTML = autoFeedback["colon"];
    td3.setAttribute("class", "old-points-" + index);
    td4 = document.createElement("td");
    var override = document.createElement("input");
    override.setAttribute("class", "points points-" + index);
    override.value = instructor["points"][1];
    override.setAttribute("disabled", "");
    td4.append(override);
    tr.appendChild(td1);
    tr.appendChild(td2);
    tr.appendChild(td3);
    tr.appendChild(td4);
    table.appendChild(tr);

    /* constraint */
    var tr = document.createElement("tr");
    td1 = document.createElement("td");
    td1.innerHTML = "Constraint (" + examsList[studentID][index]['constraintName'] + ")";
    td2 = document.createElement("td");
    td2.innerHTML = (autoFeedback["constraintName"] == 0) ? "Passed" : "Failed";
    td2.style.color = (autoFeedback["constraintName"] == 0) ? "#3eb53e" : "#c83a3a";
    td3 = document.createElement("td");
    td3.setAttribute("class", "old-points-" + index);
    td3.innerHTML = autoFeedback["constraintName"];
    td4 = document.createElement("td");
    var override = document.createElement("input");
    override.setAttribute("class", "points points-" + index);
    override.value = instructor["points"][2];
    override.setAttribute("disabled", "");
    td4.append(override);
    tr.appendChild(td1);
    tr.appendChild(td2);
    tr.appendChild(td3);
    tr.appendChild(td4);
    table.appendChild(tr);

    

    var tests = autoFeedback['tests'];
    console.log(tests.length);
    for(let i=0; i < tests.length; i++) {
        var tr = document.createElement("tr");
        td1 = document.createElement("td");
        let re = /(\[[-?\S*\.?\S*, "\']+\])|(\{[-?\S\.?, :"\']+\})|(\([-?\S\.?, "\']+\))|(["\'].*["\'])|([-+]?\d+\.?\d*e?[-+]?)/gm;

        //prepare input
        var decodedInput = decodeURIComponent(examsList[studentID][index][getTestCaseString(i, false)]);
        var input = "";
        if(decodedInput.match(re) === null) {
            input = decodedInput;
        }
        else {
            decodedInput = (decodedInput.match(re) === null) ? decodedInput : decodedInput.match(re);
            for(let j = 0; j < decodedInput.length; j++) {
                if(j != decodedInput.length -1)
                    input += decodedInput[j] + ", ";
                else
                    input += decodedInput[j];
            }
        }

        //prepare output
        var decodedOutput = decodeURIComponent(examsList[studentID][index][getTestCaseString(i, true)]);
        var output = "";
        if(decodedOutput.match(re) === null) {
            output = decodedOutput;
        }
        else {
            decodedOutput = (decodedOutput.match(re) === null) ? decodedOutput : decodedOutput.match(re);
            for(let j = 0; j < decodedOutput.length; j++) {
                if(j != decodedOutput.length -1)
                    output += decodedOutput[j] + ", ";
                else
                    output += decodedOutput[j];
            }
        }
        
        td1.innerHTML = "<b>Test Case:</b> Ran " + examsList[studentID][index]['functionName'] + "(" + input + ")";
        td1.innerHTML += " : expected " + output;
        td1.innerHTML += " → got " + tests[i]['result'];
        td2 = document.createElement("td");
        td2.innerHTML = (tests[i]["lost"] == 0) ? "Passed" : "Failed";
        td2.style.color = (tests[i]["lost"] == 0) ? "#3eb53e" : "#c83a3a";
        td3 = document.createElement("td");
        td3.setAttribute("class", "old-points-" + index);
        td3.innerHTML = tests[i]["lost"];
        td4 = document.createElement("td");
        var override = document.createElement("input");
        override.setAttribute("class", "points points-" + index)
        override.value = instructor["points"][i + 3];
        override.setAttribute("disabled", "");
        td4.append(override);
        tr.appendChild(td1);
        tr.appendChild(td2);
        tr.appendChild(td3);
        tr.appendChild(td4);
        table.appendChild(tr);
    }

    /* comment */
    var tr = document.createElement("tr");
    var p = document.createElement("p");
    p.innerHTML = "<b>Instructor Comment</b>: ";
    var comment = document.createElement("textarea");
    comment.setAttribute("class", "comment");
    comment.setAttribute("id", "comment-" + index);
    comment.value = instructor["comment"];
    comment.setAttribute("disabled", "");
    tr.appendChild(p);
    tr.appendChild(comment);
    table.appendChild(tr);

    wrapper.appendChild(outputTable);
    /*

    /* Third row has the prompt
    var thirdRow = document.createElement("tr");
    thirdRow.setAttribute("class", "question-prompt");

    var prompt = document.createElement("td");
    prompt.innerHTML = "🛈 " + question["prompt"];
    thirdRow.appendChild(prompt);

    table.appendChild(thirdRow);
    */
    
    return wrapper;
}

function createReviewQuestionListVBE(studentID) {
    var wrapper = document.getElementById("feedback-list-box");
    while(wrapper.childElementCount)
            wrapper.firstChild.remove();

    for(let i=0; i < examsList[0].length; i++) {
        wrapper.appendChild(createReviewQuestionVBE(i, studentID));
    }
    showResults();
}

function showResults() {
    var wrapper = document.getElementById("post-eval");
    var p = document.createElement("p");

    var totalGot = examsList[0]
        .map( v => parseInt(v["pointsReceived"]) )
        .reduce( (sum, current) => sum + current, 0);

    var totalMax = examsList[0]
    .map( v => parseInt(v["maxPoints"]) )
    .reduce( (sum, current) => sum + current, 0);
    p.innerHTML = "⭐ " + totalGot + "/" + totalMax + '<br>';
    p.innerHTML += ((totalGot / totalMax) * 100.0).toFixed(2) + '%';
    wrapper.appendChild(p);
}