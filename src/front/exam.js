/* Check whether user is instructor; if not, redirect */
var _uname = getCookie("userName");
var _utype = getCookie("userType");
var _uid = getCookie("dbID");
var _eid = getCookie("activeTakeExam");

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
            examInfoList = JSON.parse(this.responseText);
            //examInfoList = Array(examInfo);
            renderPageElement('exams');
        }
    };
    xhr.open("GET", 'data.php?data=take&id=' + _uid + "&content=" + _eid, true);
    xhr.send();
}

/* renderPageElement() - will render the specified element(s) on the page */
function renderPageElement(type)
{
    if(type === "exams")
    {   
        let listBox = document.getElementById("question-list-box");

        /* first clear all the elements in the div */
        while(listBox.childElementCount)
            listBox.firstChild.remove();
        
        /* then draw each of the appropriate exam visual elements */
        for(let i=0; i < examInfoList.length; i++)
        {
            /* TODO: add exam elements to right flexbox here */
            let visualExam = document.createElement("div");
            visualExam.setAttribute("id", "vq"+i);
            visualExam.setAttribute("class", "visual-question");
            let p = document.createElement("p");
            p.innerHTML = '<b>Prompt:</b> ' + examInfoList[i]['prompt'];
            let mp = document.createElement("p");
            mp.innerHTML = "<b>Max Points:</b> " + examInfoList[i]['maxPoints'];
            let ta1 = document.createElement("textarea");
            ta1.setAttribute("id", "ta1" + i);
            ta1.setAttribute("cols", "80");
            ta1.setAttribute("rows", "6");
            ta1.setAttribute("onkeydown", "insertTab(this, event);");
            let p1 = document.createElement("p");
            p1.innerHTML = "<b>Student Submission</b>";

            visualExam.appendChild(mp);
            visualExam.appendChild(p);
            visualExam.appendChild(p1);
            visualExam.appendChild(ta1);
        
            listBox.appendChild(visualExam);
        }
    }
}

function validateInput()
{
    let submissionsBox = document.getElementById("question-list-box");
    let errorLabel = document.getElementById("error-label");
    var updateInfo = [];

    errorLabel.innerHTML = "";

    for(let i=0; i < submissionsBox.childElementCount; i++)
    {
        var input = customEncode(document.getElementById("ta1" + i).value);
        /* is valid */
        //console.log(resultingPoints);
        //console.log("EID: " + _eid + "\tQID: " + examInfoList[i]['qid'] + "\tSID: " + examInfoList[i]['sid']);
        var examUpdate = {
            "id": _eid,
            "qid": examInfoList[i]['qid'],
            "sid": examInfoList[i]['sid'],
            "submissionText": input
        };
        updateInfo.push(examUpdate);
    }

    console.log("UPDATE INFO: " + updateInfo);
    if(errorLabel.innerHTML == "")
    {
        /* no errors, submit feedback */
        submitSubmission(updateInfo);
    }
    
}

function submitSubmission(updateInfo)
{  
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
            if(this.responseText == 'success')
                window.location.href = 'student.html';
            else
                document.getElementById("error-label").innerHTML = "There was an error taking the exam.";
        }
    };

    xhr.send("data=submit&id=" + _eid + "&content=" + JSON.stringify(updateInfo)); //send the JSON
}

/* credit: https://sumtips.com/snippets/javascript/tab-in-textarea/#js */
function insertTab(o, e)
{		
	var kC = e.keyCode ? e.keyCode : e.charCode ? e.charCode : e.which;
	if (kC == 9 && !e.shiftKey && !e.ctrlKey && !e.altKey)
	{
		var oS = o.scrollTop;
		if (o.setSelectionRange)
		{
			var sS = o.selectionStart;	
			var sE = o.selectionEnd;
			o.value = o.value.substring(0, sS) + "\t" + o.value.substr(sE);
			o.setSelectionRange(sS + 1, sS + 1);
			o.focus();
		}
		else if (o.createTextRange)
		{
			document.selection.createRange().text = "\t";
			e.returnValue = false;
		}
		o.scrollTop = oS;
		if (e.preventDefault)
		{
			e.preventDefault();
		}
		return false;
	}
	return true;
}

/* Credit: https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/encodeURIComponent */
function customEncode(str) {
    /* encode tabs and newlines */
    //str = str.replace(/\n/g, "\\\\n");
    //str = str.replace(/\t/g, "\\\\t");

    return encodeURIComponent(str).replace(/[!'()*]/g, function(c) {
      return '%' + c.charCodeAt(0).toString(16);
    });
}
