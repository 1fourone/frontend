/* Check whether user is instructor; if not, redirect */
var _uname = getCookie("userName");
var _utype = getCookie("userType");
var _uid = getCookie("dbID");

if(_utype != "instructor")
    window.location.href = "login.html";


const topics = [
    "Arithmetic",
    "Tuples",
    "Strings",
    "Loops",
    "Lists",
    "Dictionary",
    "Conditionals"
];

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
    /*
    let errorLabel = document.getElementById("error-label");
    let prompt = document.getElementById("prompt");
    let functionName = document.getElementById("function-name");
    let topic = document.getElementById("topic");
    let firstTestCase = document.getElementById("first-test-case");
    let firstOutput = document.getElementById("first-output");
    let secondTestCase = document.getElementById("second-test-case");
    let secondOutput = document.getElementById("second-output");
    
    if(prompt.value.length == 0 || prompt.value.length > 128)
        errorLabel.innerHTML = "The prompt must be nonempty and less than or equal to 128 characters.";
    else if(functionName.value.length == 0 || functionName.value.length > 64)
        errorLabel.innerHTML = "The function name must be nonempty and less than or equal to 64 characters.";
    else if(topic.value.length == 0 || topic.value.length > 32)
        errorLabel.innerHTML = "The topic must be nonempty and less than or equal to 32 characters.";
    else if(firstTestCase == null || firstTestCase.value.length == 0 || firstTestCase.value.length > 64)
        errorLabel.innerHTML = "The first test case must be nonempty and less than or equal to 64 characters.";
    else if(firstOutput == null || firstOutput.value.length == 0 || firstOutput.value.length > 64)
        errorLabel.innerHTML = "The first output must be nonempty and less than or equal to 64 characters.";
    else if(secondTestCase == null || secondTestCase.value.length == 0 || secondTestCase.value.length > 64)
        errorLabel.innerHTML = "The second test case must be nonempty and less than or equal to 64 characters.";
    else if(secondOutput == null || secondOutput.value.length == 0 || secondOutput.value.length > 64)
        errorLabel.innerHTML = "The second output must be nonempty and less than or equal to 64 characters.";
    else
    */
    attemptQuestionCreation();
   
}

/* Credit: https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/encodeURIComponent */
function customEncode(str) {
    /* encode tabs and newlines */
    str = str.replace(/\n/g, "\\n");
    str = str.replace(/\t/g, "\\t");

    return encodeURIComponent(str).replace(/[!'()*]/g, function(c) {
      return '%' + c.charCodeAt(0).toString(16);
    });
}

/* attemptQuestionCreation - attempts to create a question in the question bank */
function attemptQuestionCreation()
{
    let errorLabel = document.getElementById("error-label");
    let prompt = document.getElementById("prompt");
    let difficulty = document.getElementById("difficulty");
    let functionName = document.getElementById("function-name");
    let constraintName = document.getElementById("constraint-name");
    let topic = document.getElementById("topic");
    let outputs = document.getElementsByClassName("tc-output");
    let inputs = document.getElementsByClassName("tc-input");
    let firstTestCase = customEncode(inputs[0].value);
    let firstOutput = customEncode(outputs[0].value);
    let secondTestCase = customEncode(inputs[1].value);
    let secondOutput = customEncode(outputs[1].value);
    let thirdTestCase = customEncode(inputs[2].value);
    let thirdOutput = customEncode(outputs[2].value);
    let fourthTestCase = customEncode(inputs[3].value);
    let fourthOutput = customEncode(outputs[3].value);
    let fifthTestCase = customEncode(inputs[4].value);
    let fifthOutput = customEncode(outputs[4].value);
    let sixthTestCase = customEncode(inputs[5].value);
    let sixthOutput = customEncode(outputs[5].value);

    var question = {
        "prompt" : prompt.value,
        "functionName": functionName.value,
        "difficulty": difficulty.selectedIndex,
        "topic": topics[topic.selectedIndex],
        "constraintName": constraintName.value,
        "firstTestCase": firstTestCase,
        "firstOutput": firstOutput,
        "secondTestCase": secondTestCase,
        "secondOutput": secondOutput,
        "thirdTestCase": thirdTestCase,
        "thirdOutput": thirdOutput,
        "fourthTestCase": fourthTestCase,
        "fourthOutput": fourthOutput,
        "fifthTestCase": fifthTestCase,
        "fifthOutput": fifthOutput,
        "sixthTestCase": sixthTestCase,
        "sixthOutput": sixthOutput,
        "creatorID": _uid
    };

    console.log(question);
    //try to insert the valid question into database 
    
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