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

    /* add tab functionality to text areas on all pages 
    credit: https://stackoverflow.com/questions/6637341/use-tab-to-indent-in-textarea
    */
    
    var textareas = document.getElementsByTagName('textarea');
    var count = textareas.length;
    for(var i=0;i<count;i++){
        textareas[i].onkeydown = function(e){
            if(e.keyCode==9 || e.which==9){
                e.preventDefault();
                var s = this.selectionStart;
                this.value = this.value.substring(0,this.selectionStart) + "\t" + this.value.substring(this.selectionEnd);
                this.selectionEnd = s+1; 
            }
        }
    }
}

/* validateInput - perform input validation on all fields before inserting the new question */
function validateInput()
{
    let errorLabel = document.getElementById("error-label");
    let prompt = document.getElementById("prompt");
    let functionSignature = document.getElementById("function-signature");
    let topic = document.getElementById("topic");
    let firstTestCase = document.getElementById("first-test-case");
    let firstOutput = document.getElementById("first-output");
    let secondTestCase = document.getElementById("second-test-case");
    let secondOutput = document.getElementById("second-output");
    

    if(prompt.value.length == 0 || prompt.value.length > 128)
        errorLabel.innerHTML = "The prompt must be nonempty and less than or equal to 128 characters.";
    else if(functionSignature.value.length == 0 || functionSignature.value.length > 64)
        errorLabel.innerHTML = "The function signature must be nonempty and less than or equal to 64 characters.";
    else if(prompt.value.split(" ").includes(functionSignature.value) == false)
        errorLabel.innerHTML = "The function signature must be exactly the same as the one in the prompt.\n(No spaces between arguments)";
    else if(topic.value.length == 0 || topic.value.length > 32)
        errorLabel.innerHTML = "The topic must be nonempty and less than or equal to 32 characters.";
    else if(firstTestCase.value.length == 0 || firstTestCase.value.length > 64)
        errorLabel.innerHTML = "The first test case must be nonempty and less than or equal to 64 characters.";
    else if(firstTestCase.value.indexOf('"') != -1)
        errorLabel.innerHTML = "Test cases and outputs for strings cannot contain double quotes. Use only single quotes.";
    else if(firstOutput.value.length == 0 || firstOutput.value.length > 64)
        errorLabel.innerHTML = "The first output must be nonempty and less than or equal to 64 characters.";
    else if(firstOutput.value.indexOf('"') != -1)
        errorLabel.innerHTML = "Test cases and outputs for strings cannot contain double quotes. Use only single quotes.";
    else if(secondTestCase.value.length == 0 || secondTestCase.value.length > 64)
        errorLabel.innerHTML = "The second test case must be nonempty and less than or equal to 64 characters.";
    else if(secondTestCase.value.indexOf('"') != -1)
        errorLabel.innerHTML = "Test cases and outputs for strings cannot contain double quotes. Use only single quotes.";
    else if(secondOutput.value.length == 0 || secondOutput.value.length > 64)
        errorLabel.innerHTML = "The second output must be nonempty and less than or equal to 64 characters.";
    else if(secondOutput.value.indexOf('"') != -1)
        errorLabel.innerHTML = "Test cases and outputs for strings cannot contain double quotes. Use only single quotes.";
    else
        attemptQuestionCreation();
}

/* attemptQuestionCreation - attempts to create a question in the question bank */
function attemptQuestionCreation()
{
    let errorLabel = document.getElementById("error-label");
    let prompt = document.getElementById("prompt");
    let difficulty = document.getElementById("difficulty");
    let functionSignature = document.getElementById("function-signature");
    let topic = document.getElementById("topic");
    let firstTestCase = document.getElementById("first-test-case");
    let firstOutput = document.getElementById("first-output");
    let secondTestCase = document.getElementById("second-test-case");
    let secondOutput = document.getElementById("second-output");

    var question = {
        "prompt" : prompt.value,
        "functionSignature": functionSignature.value,
        "difficulty": difficulty.selectedIndex,
        "topic": topic.value,
        "firstTestCase": firstTestCase.value.replace(/'/g,"\\\'"),
        "firstOutput": firstOutput.value.replace(/'/g,"\\\'"),
        "secondTestCase": secondTestCase.value.replace(/'/g,"\\\'"),
        "secondOutput": secondOutput.value.replace(/'/g,"\\\'"),
        "creatorID": _uid
    };

    //console.log(question);
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
                errorLabel.innerHTML = "That question could not be added, make sure that you are not entering a question that is already on the question bank.";
        }
    };

    xhr.send("data=question&question=" + JSON.stringify(question)); //send the JSON
    
}