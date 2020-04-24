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
            examInfoList = JSON.parse(this.responseText);
            renderPageElement('exams');

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

/* renderPageElement() - will render the specified element(s) on the page */
function renderPageElement(type)
{
    if(type === "exams")
    {   
        
        let listBox = document.getElementById("submission-list-box");

        /*
        /* first clear all the elements in the div
        while(listBox.childElementCount)
            listBox.firstChild.remove();
        
        /* then draw each of the appropriate exam visual elements 
        for(let i=0; i < examInfoList.length; i++)
        {
            /* TODO: add exam elements to right flexbox here 
            let visualExam = document.createElement("div");
            visualExam.setAttribute("id", "vq"+i);
            visualExam.setAttribute("class", "visual-question");
            let p = document.createElement("p");
            p.innerHTML = '<b>Prompt:</b> ' + examInfoList[i]['prompt'];
            let ta1 = document.createElement("textarea");
            ta1.setAttribute("id", "ta1" + i);
            ta1.setAttribute("readOnly", "");
            ta1.value = (examInfoList[i]['submissionText'] == null) ? '' : examInfoList[i]['submissionText'].slice(1, examInfoList[i]['submissionText'].length-1);
            ta1.setAttribute("cols", "80");
            ta1.setAttribute("rows", "6");
            let p1 = document.createElement("p");
            p1.innerHTML = "<b>Student Submission</b>";
            let p2 = document.createElement("p");
            p2.innerHTML = "<b>Grader Feedback</b>";
            let ta2 = document.createElement("textarea");
            ta2.setAttribute("id", "ta2" + i);
            ta2.setAttribute("readOnly", "");
            ta2.setAttribute("cols", "80");
            ta2.setAttribute("rows", "6");
            /* get the auto feedback and present it in a nice way 
            let feedback = '';
            let autoFeedback = JSON.parse(examInfoList[i]['autoFeedback']);
            //console.log(autoFeedback);
            if(autoFeedback['firstPassed'] == "false")
                feedback += 'Program did not pass the first testcase (-' + parseFloat(autoFeedback['pointsLost'][0]).toFixed(2) + ')\n';
            if(autoFeedback['secondPassed'] == "false")
                feedback += 'Program did not pass the second testcase (-' + parseFloat(autoFeedback['pointsLost'][1]).toFixed(2) + ')\n';
            if(autoFeedback['ranSuccessfully'] == "false")
                feedback += 'Program did not run successfully (-' + parseFloat(autoFeedback['pointsLost'][2]).toFixed(2) + ')\n';
            if(autoFeedback['correctSignature'] == "false")
                feedback += 'Program did not have the correct function signature (-' + parseFloat(autoFeedback['pointsLost'][3]).toFixed(2) + ')\n';
            if(autoFeedback['hasReturn'] == "false")
                feedback += 'Program did not return from function (-' + parseFloat(autoFeedback['pointsLost'][4]).toFixed(2) + ')\n';

            feedback = (feedback == '') ? 'none' : feedback;
            ta2.value = "Points Lost:\n" + feedback;

            let p3 = document.createElement("p");
            p3.innerHTML = "<b>Max Points:</b> " + examInfoList[i]['maxPoints'] + "<br><b>Total Points Lost:</b> " + (examInfo[i]['maxPoints'] - examInfo[i]['pointsReceived'])'<br><b>Instructor Feedback:</b> ';

            let ta3 = document.createElement("textarea");
            ta3.setAttribute("id", "ta3" + i);
            ta3.setAttribute("cols", "80");
            ta3.setAttribute("rows", "6");
            
            let p4 = document.createElement("p");
            p4.innerHTML = "<b>Override Points Lost</b>";
            
            let override = document.createElement("input");
            override.setAttribute("id", "o"+i);


            visualExam.appendChild(p);
            visualExam.appendChild(p1);
            visualExam.appendChild(ta1);
            visualExam.appendChild(p2);
            visualExam.appendChild(ta2);
            visualExam.appendChild(p3);
            visualExam.appendChild(ta3);
            visualExam.appendChild(p4);
            visualExam.appendChild(override);

            listBox.appendChild(visualExam);
            
        }
        */
    }
}

function validateInput(studentID=0)
{
    
    let submissionsBox = document.getElementById("submission-list-box");
    let errorLabel = document.getElementById("error-label");
    var updateInfo = [];

    errorLabel.innerHTML = "";

    for(let i=0; i < submissionsBox.childElementCount; i++)
    {
        let comment = document.getElementById('comment-'+i).value;
        let instructorFeedback = 1;
        let overridePoints = document.getElementsByClassName("points-" + i);
        let oldPoints = document.getElementsByClassName("old-points-" + i);
        //console.log(overridePoints);
        let points = [];
        let total = 0;
        for(let j = 0; j < overridePoints.length; j++) {
            points.push( (overridePoints[j].value != "") ? parseInt(overridePoints[j].value) :  parseInt(oldPoints[j].innerHTML));
            total += points[j];
        }
        console.log(points, total);

        var ifeedback = {
            points: points,
            comment: comment
        };

        console.log(total, examsList[studentID][i]["maxPoints"]);
        if(total > examsList[studentID][i]["maxPoints"])
            errorLabel.innerHTML = "You cannot have a question have a greater amount of points lost than it's worth.";
        else 
        {
            /* Instructor feedback:
            {
                "points": [1, 2, 3..],
                "comment": "You did good!"
            }*/
            //console.log(resultingPoints);
            //console.log("EID: " + _eid + "\tQID: " + examInfoList[i]['qid'] + "\tSID: " + examInfoList[i]['sid']);
            //JUST INTS NOW
            var examUpdate = {
                "id": _eid,
                "qid": examInfoList[i]['qid'],
                "sid": examInfoList[i]['sid'],
                "instructorFeedback": ifeedback,
                "pointsReceived": examInfoList[i]['maxPoints'] - total
            };
            updateInfo.push(examUpdate);
        }
       
    }

    if(errorLabel.innerHTML == "")
    {
        /* no errors, submit feedback */
        submitFeedback(updateInfo);
    }
    
}

function submitFeedback(updateInfo)
{  
    console.log("attempting to submit feedback for exam ID " + _eid);
    console.log(JSON.stringify(updateInfo));
    /* try to insert the valid question into database */
    var xhr = new XMLHttpRequest();
    xhr.open("POST", 'data.php', true);

    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded"); //We're sending JSON data in a string
    xhr.onreadystatechange = function() 
    {
        if (this.readyState === XMLHttpRequest.DONE && this.status === 200) 
        {
            console.log(this.responseText);
            if(this.responseText == 'success') {
                //window.location.href = 'instructor.html';
            }
            else
                document.getElementById("error-label").innerHTML = "There was an error submitting the feedback.";
        }
    };

    xhr.send("data=exams&id=" + _eid + "&content=" + JSON.stringify(updateInfo)); //send the JSON
    
}

/*
for(i=0; < N; i++)
    createReviewQuestionVBE(i, STUD_ID)
*/

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
    td1.innerHTML = "Constraint";
    td2 = document.createElement("td");
    td2.innerHTML = (autoFeedback["constraintName"] == 0) ? "Passed" : "Failed";
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
        td1.innerHTML = "Test Case " + (i+1);
        td2 = document.createElement("td");
        td2.innerHTML = (autoFeedback["tests"][i] == 0) ? "Passed" : "Failed";
        td3 = document.createElement("td");
        td3.setAttribute("class", "old-points-" + index);
        td3.innerHTML = autoFeedback["tests"][i];
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
}