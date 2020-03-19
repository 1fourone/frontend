var exams;

function getExams() 
{
    var xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function() 
    {
        if (this.readyState === XMLHttpRequest.DONE && this.status === 200) 
        {
            var status = ["active", "released", "unreleased"];
            //console.log(this.responseText);
            exams = JSON.parse(this.responseText);
            
            console.log(status[0]);
            for(let i = 0; i < exams.length; i++)
            {
                for(let j = 0; j < exams[i].length; j++)
                    createExamElement(exams[i][j], j + (j * i), status[i]);
            }
        }
    };
    xhr.open("GET", 'data.php?value=exams&student=' + getCookie('dbID'), true);
    xhr.send();
}

function createExamElement(exam, id, status)
{
    var el = document.getElementById("e"+id);
    if(el == null)
    {
        console.log("creation: ", exam, id, status);
        console.log(document.getElementById(status + '_end'));
        var container = document.createElement("button");
        container.setAttribute("id", "e"+id);
        container.setAttribute("class", "exam");
        container.onclick = function() { loadExam(id); };
        //container.setAttribute("draggable", "true");
        var label = document.createTextNode(exam['name'] + ': ' + exam['course'] + '-' + exam['section']);
        container.appendChild(label);

        document.body.insertBefore(container, document.getElementById(status + '_end'));
    }
}

function loadExam(id)
{
    console.log("Load Exam called with " + id);
    var effectiveID = -1;
    if(id < exams[0].length)
        effectiveID = exams[0][id]['id'];
    else if(id < exams[1].length)
        effectiveID = exams[1][id - exams[0].length]['id'];
    else
        effectiveID = exams[2][id - exams[0].length - exams[1].length]['id'];
    setCookie("currentExam", effectiveID);
    window.location.href = 'exam.html';
}