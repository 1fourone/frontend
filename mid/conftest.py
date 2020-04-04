import pytest
import json

def getQuestions(fileName):
    file = open(fileName)
    questions = json.load(file)['questions']
    file.close()

    return questions

def pytest_generate_tests(metafunc):
    if "q" in metafunc.fixturenames:
        questions = getQuestions('questions.json')
        names = {questions[i]['name'] for i in range(len(questions))}
        metafunc.parametrize("q", questions, ids=names)

'''
Grader receives a question's input in the following form
{
    "prompt": "Write a function named lastChar that takes parameter s gets the last character of s and prints the result",
    "functionName": "lastChar",
    "parameters": ["s"],
    "constraint": "print",
    "studentInput": "def lastChar(s):\n\treturn s[-1]",
    "testCases": [
        ["'hello'", "'o'"],
        ["'samba'", "'a'"],
        ["'123'", "'3'"]
    ],
    "expectedOutput": {
        "name": 0,
        "constraint": 0,
        "colon": 0,
        "tests": [
            0, 0, 0
        ]
    }
}

Grader should check for the following scenarios:
1) Function name
2) Constraint followed
3) No colon at end of first line
4) Test case 1
5) Test case 2
6) (Optional) Test case 3-6

The output for a question would be of the form
{
	"name": 0,
	"constraint": 2,
	"colon": 1,
	"tests": [
		0, 3, 2
	]
}
all of above fields have their respective points lost
tests contains the array of points lost for each test case
here, 1st test case lost no points, 2nd lost 3, 3rd lost 2

Grader will automatically give a (rounded down) version for each item.
For example, if the question is worth 10 points, and only 3 tests, then:
TODO: how to split X evenly amongst N items
- name
- constraint
- colon
- tests


Grader is responsible for then "packaging" the above question output
for all questions as an array, and encoding that as JSON string 
`autoFeedback` for the DB.

Mid's data communicator for the exam panel (autograde/feedback/release)
would then use this `autoFeedback` along exam id, question id, student id
to update `status` and `autoFeedback` accordingly.
'''