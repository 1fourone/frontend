var type = getCookie("userType");
if(type != "student")
    window.location.href = type + '.html';
var exam = getCookie("currentExam");
if(exam == "")
    window.location.href = 'student.html';

window.onload = function() 
{
    document.getElementById("student").innerHTML += getCookie("dbID");
    document.getElementById("exam").innerHTML += getCookie("currentExam");
};
