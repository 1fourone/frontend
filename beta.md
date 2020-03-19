# Beta Specification

Program needs to be able to do the following:

[PI] - Identification - Student home page different than Instructor home page
[D] - Instructor adds a question to question bank (write a function ..)
[D] - Instructor selects a question for exam
[W] - Student takes the exam
[W] - Instructor starts autograding (can tweak scores, add comments, release scores)
[W] - Students review results (can only do this once done their score is released)

There are some common subtasks that are important to the above features' functionality:

[CC] - Rendering the question bank `qbank.html`
[W] - Rendering active exams for a student

*Legend*: D(esigned), P(artially)I(mplemented), T(esting), C(osmetic)C(hanges), R(eady), W(aiting)

## Identification

#### Overview
On a similar login page to one on Alpha release, allow for users to login to either as a faculty or as a student, and to be taken to their home pages.

#### Technical
- `index.php` reads a `name` and `plain_password` from input fields and sends it to `login.php` as `Credentials`
```json 
{
    "name": "ma353",
    "plain_password": "billyab351sE%^"
}
```

- Front's `login.php` forwards this information to Mid's `login.php` as is
- Mid's `login.php` modifies the credentials to store a `hashed_password` and sends this to Back's `login.php` in the form
```json
{
    "name": "ma353",
    "hashed_password": "89a7e6eabbc4c9477277ec9b246c6417dc352e69418bf3ef4d75e9c19bbbedd6"
}
```
- Back's `login.php` uses the credentials to query the `USER` table for either a valid student or instructor id. Once that valid id is found, either `STUDENT` or `INSTRUCTOR` table are queried to get the personal details for the respective homepage `instructor.php` or `student.php`.
```json
{
    "type": "instructor",
    "result": "success",
    "id": "1a35..."
}
```

## Rendering the Question Bank

#### Overview
An instructor should be able to view all the questions in the question bank. Each question will have a visual representation with its information (prompt, difficulty, etc), and they will all be rendered as items that can be dragged around (used later in exam creation). 

#### Technical
- `qbank.html`'s `qbank.js` sends a `GET` request to `data.php` with `value=qbank` to indicate that it wants to get the question bank data.
- Front's `data.php` receives the request and passes it along to Mid's `data.php`.
- Mid's `data.php` receives the request and passes it along to Back's `data.php`.
- Back's `data.php` queries all question information.
    - Each question is added as an object to an array
    - The resulting array is returned as a JSON string
- Mid's `data.php` receives the JSON string and passes it along
- Front's `data.php` receives the JSON string and passes it back to `qbank.js`
- `qbank.js` loops through the array (optionally applying any filtering/sorting) and prints each question as its visual representation
    - it also assigns the containing div element to a `question` class and to a `q#` id, where # is the index of the question in the array.
    - `qbank.css` is responsible for specifying how each question should be rendered.

## Adding to Question Bank

#### Overview
An instructor should be able to add a question of the form "write a function ..." to the question bank. Basically, allow an instructor to set some values for a "question", and create a question and store it. These would eventually be shown to the instructor when creating an exam.

#### Technical
- `qadd.html` takes input from the instructor for `prompt`, `difficulty`, `topic`, `firstTestCase`, `firstOutput`, `secondTestCase`, `secondOutput` of the question.
- Instructor clicks on `Add Question` button.
- `qadd.html` sends a `POST` request to `data.php` with `value=(data)` where data is a JSON string of all the input received from the instructor.
- Front's `data.php` receives the request and passes it along to Mid's `data.php`.
- Mid's `data.php` receives the request and passes it along to Back's `data.php`.
- Back's `data.php` performs an insertion query with the question data provided, and returns the result
```json
{
    "result": "success"
}
```
- Mid's `data.php` receives the result and passes it along to Front's `data.php`.
- Front's `data.php` receives the result and passes it along to `qadd.html`.
- `qadd.html` does one of two things based on result
    - if success, redirect to `instructor.html`
    - if failure, print error message

## Instructor Prepares Exam

#### Overview
An instructor should be able to select questions from the question bank to create an exam. Questions can be searched/filtered by difficulty, topic, creator, and date. When the instructor selects a question, he/she must assign a number of points that the question is worth on the exam. Once the instructor is done adding questions and assigning them points, the instructor can select to what class to assign the exam to. This would make it such that all students on that class have the exam as an active exam and are able to take it from their home page.

The general "flow" would be as follows:
1) Instructor drags and drops questions from the question bank view to the exam view.
2) Optionally, instructor can drag back items to not include them on the test.
3) Instructor assigns points for each question by entering a number on the box to the right of the question.
4) Instructor selects one of his/her class from a dropdown list.
5) Instructor clicks on either `Dispatch Exam` or `Cancel`.
    - `Dispatch Exam` will go through with exam creation
    - `Cancel` will take them back to `instructor.html`


#### Technical

- `eadd.html` renders `qbank.html`'s content in a view on the right half of the screen.
- `eadd.html` contains empty `<div>`s that `qbank.html`'s questions can be dragged back-and-forth.
    - bank view -> exam view
        - question is placed before a placeholder empty container on exam view
    - exam view -> bank view
        - container of the now moved question is deleted, making room for more questions
- `eadd.html` meanwhile sends a `GET` request to `data.php` with `value=classes&instructor=(id)` where id is the instructor's id.
- Front's `data.php` passes the request along to Mid's `data.php`.
- Mid's `data.php` passes the request along to Back's `data.php`.
- Back's `data.php` performs a selection query to get all the info for classes that the instructor is in
    - Each class is added as an object to an array
    - The resulting array is returned as a JSON string
- Mid's `data.php` passes the response along to Front's `data.php`.
- Front's `data.php` passes the response along to Front's `eadd.html`.
- `eadd.html` displays all class data in the dropdown list for instructor to select.
- `eadd.html` sends a `POST` request to `data.php` with `value=exam&class=(id)&questions=(qdata)` where id is the class id for the selected class and qdata is an array of question ID and points assigned for each question.
- Front's `data.php` receives the request and passes it along to Mid's `data.php`.
- Mid's `data.php` receives the request and passes it along to Back's `data.php`.
- Back's `data.php` adds the exam for all students involved and returns the result.
    - Performs insertion query to create exam for each student in the class with the questions and points in qdata.
    - *NOTE:* query should either **fail or pass for everyone**, no exceptions. This will most likely mean using one transaction with each student's query inside of it.
```json
{
    "result": "success"
}
```
- Mid's `data.php` receives the result and passes it along to Front's `data.php`.
- Front's `data.php` receives the result and passes it along to `eadd.html`.
- Based on result
    - if successful, instructor is redirected to `instructor.html`.
    - otherwise, print error message.

## Rendering a Student's Home Page

#### Overview 
Upon login, a student's home page should be populated with active, unreleased, and released exams. Active exams should be clickable - once the student clicks the exam, they will begin taking it.
Unreleased exams should not be clickable - the student can only see that they took these exams but can't review them yet.
Released exams should be clickable - once the student clicks the exam, they will begin reviewing it.


#### Technical
- `student.html` sends a `GET` request to `data.php` with `value=exams&student=(sid)` where sid is the student id.
- Front's `data.php` receives the request and passes it along to Mid's `data.php`.
- Mid's `data.php` receives the request and passes it along to Back's `data.php`.
- Back's `data.php` performs 3 different selection queries.
    - The first query gets all the data for active exams 
    - The second query gets all the data for unreleased exams
    - The third query gets all the data for released exams
    Each of these is put into an array, and returned.
- Mid's `data.php` receives the response and passes it along to Front's `data.php`.
- Front's `data.php` receives the response and passes it along to `student.js`, who then draws each type of exam in a visual representation.
    - each exam is drawn as a part of its own type's class and has an id for its index in the type's array.
    - `student.css` is responsible for determining how each exam type is rendered.

## Student Takes Exam

#### Overview
A student should be able to take an active exam. On his/her homepage the student would have a list of active exams to take at the top of the screen, and would click on a `Take Exam` button to take the exam itself. The exam would be shown to the student and he/she would have the option to enter their submission and click a `Submit Exam` button when done. The student cannot cancel an exam once it has started; if they exit the webpage their answers would be interpreted as the final submission and saved.

#### Technical
(Not complete)
- `student.html` renders a list of active exams for the student (subtask)
- clicked exam ID is passed from `student.html` to `exam.html` (cookie?)
- `exam.html` sends a `GET` request to `data.php` with `value=exam&id=(eid)&student=(sid)` where eid is the clicked exam ID and sid is the student ID.
- Front's `data.php` receives the request and passes it along to Mid's `data.php`.
- Mid's `data.php` receives the request and passes it along to Back's `data.php`.
- Back's `data.php` performs a selection query to get all the relevant question data for questions in the exam for student and returns them as a JSON string containing the array of question data.
```json
{ 
    [
        { "qid": "13a2...", "prompt": "Write a function ...", "maxPoints": 10 },
        { "qid": "5ba2...", "prompt": "Write a function ...", "maxPoints": 3 }
    ]
}
```
- Mid's `data.php` receives the response and forwards it to Front's `data.php`.
- Front's `data.php` receives the response and forwards it to `exam.html`.
- `exam.js` now renders each exam question for the student from the array in its visual representation.
    - it also assigns the containing div element to a `question` class and to a `q#` id, where # is the index of the question in the array.
    - `exam.css` is responsible for stating the visual representation of each question 
- Students answer each question and click `Submit Exam`.

## Schema
---
`USER` - contains information about a user (can be instructor/student)
- `id` - UUID to represent each distinct user (primary key)
- `name` - username for each distinct user (unique)
- `password` - the (hashed) password of each distinct user
- `sid` - UUID to represent each distinct student (foreign key to `STUDENT`.`id`)
- `iid` - UUID to represent each distinct instructor (foreign key to `INSTRUCTOR`.`id`)

| id (PK) | name (U) | password   | sid (FK) | iid (FK) |
|---------|----------|------------|----------|----------|
| 89a7... | bj531    | 89a7e6e... | 95s3...  | NULL     |
| 95q4... | jd432    | k531sr3... | NULL     | 43s8...  |
| ...     | ...      | ...        | ...      | ...      |

A `USER` can be either a `STUDENT` or an `INSTRUCTOR`.

---
`STUDENT` - contains information about a student
- `id` - UUID to represent each distinct student (primary key)
- `uname` - a student's distinct username (foreign key to `USER`.`id`)
- `name` - the student's name, is not necessarily unique (unique with cid)
- `cid` - UUID to represent each distinct class (primary key, foreign key to `CLASS`.`id`) (unique with cid)

| id (PK) | uname (FK) | name      | cid (PK, FK) |
|---------|------------|-----------|--------------|
| 95s3... | bj531      | Billy Joe | a593...      |
| 5s41... | jd432      | Jenna Doe | a592...      |
| ...     | ...        | ...       | ...          |

A `STUDENT` has a `name` and is a part of 1+ `CLASS`es.
`STUDENT`s with the same `cid` belong to the same `CLASS`.
Multiple entries with same `id` but different `cid` represent all of that `STUDENT`s `CLASS`es.

---
`INSTRUCTOR` - contains information about an instructor
- `id` - UUID to represent each distinct instructor (primary key)
- `uname` - an instructor's distinct username (foreign key to `USER`.`id`)
- `name` - the instructor's name, not necessarily unique
- `cid` - UUID to represent each distinct class (primary key, foreign key to `CLASS`.`id`)

| id (PK) | uname (FK) | name       | cid (PK, FK) |
|---------|------------|------------|--------------|
| 43s8... | jk93       | James Kent | a593...      |
| a9s5... | mc351      | Mindy Craw | a592...      |
| ...     | ...        | ...        | ...          |

An `INTRUCTOR` has a `name` and teaches 1+ `CLASS`es.
A `CLASS` can only be taught by 1 `INSTRUCTOR`.
Multiple entries with same `id` and different `cid` represent all of that `INSTRUCTOR`s `CLASS`es. 

---
`CLASS` - contains information about a class
- `id` - UUID to represent each distinct class (primary key)
- `name` - the class name
- `course` - the course name (unique with section)
- `section` - the section number (unique with course)

| id (PK) | name                 | course | section |
|---------|----------------------|--------|---------|
| a593... | Roadmap to Computing | CS100  | 001     |
| a592... | Roadmap to Computing | CS100  | 002     |
| ...     | ...                  | ...    | ...     |

A `CLASS` has a `name`, a `course`, and a `section`.
There can be 1+ `section`s of a `course`.
There cannot be 1+ entry with the same `course` and `section`.

---
`QUESTION` - contains information about a question
- `id` - UUID to represent each distinct question (primary key)
- `prompt` - instructions/prompt for the question (unique)
- `difficulty` - number to represent difficulty (0 = easy, 1 = medium, 2 = hard)
- `topic` - topic a question belongs to
- `creatorID` - UUID to represent the creator of question (foreign key to `INSTRUCTOR`.`id`)
- `creationDate` - timestamp of when question was created
- `firstTestCase` - testcase for autograder to grade a future exam submission
- `firstOutput` - expected result for first test case
- `secondTestCase` - testcase for autograder to grade a future exam submission
- `secondOutput` - expected result for second test case

| id (PK) | prompt (U)                                                                     | difficulty | topic    | creatorID (FK)| creationDate | firstTestCase | firstOutput | secondTestCase | secondOutput |
|---------|------------------------------------------------------------------------------|----------------|--------------|---------------|------------------|---------------|-------------|----------------|--------------|
| f3s0... | Write a function add(a, b) that adds two numbers and returns the result.     | 0              | Functions    | 43s8...       | 1584283994       | 1,5           | 6           | -3,15          | 12           |
| a5sl... | Write a function isLeapYear(year) that returns whether year is a  leap year. | 1              | Conditionals | a9s5...       | 1584254553       | 2020          | True        | 2019           | False        |
| ...     | ...                                                                          | ...            | ...          | ...           | ...              | ...           | ...         | ...            | ...          |

A `QUESTION` has a `prompt` with instructions on what to do.
A `QUESTION` has a `firstTestCase` and `secondTestCase` with their respective `firstOutput` and `secondOutput`.
The `QUESTION` bank can be sorted/filtered by `difficulty`, `topic`, `creatorID`, and `creationDate` (this is done by JS on "raw" data, *not* SQL).
A `QUESTION`s prompt has to be unique - you cannot have more than one question with the same prompt.

---
`EXAM` - contains information about an exam
- `id` - UUID to represent each distinct exam (primary key)
- `name` - the name of the exam
- `qid` - UUID to represent each distinct question (primary key, foreign key to `QUESTION`.`id`)
- `sid` - UUID to represent each distinct student (primary key, foreign key to `STUDENT`.`id`)
- `cid` - UUID to represent each distinct class (foreign key to `CLASS`.`id`)
- `status` - number to represent state of exam (0 = past released, 1 = past unreleased, 2 = active)
- `date` - timestamp of when exam was assigned
- `maxPoints` - number of points the question is worth
- `submissionText` - answer that the student wrote
- `autoFeedback` - feedback by the autograding
- `instructorFeedback` - manual feedback by instructor
- `pointsReceived` - number of points question was given by the autograder/overriden by the instructor

| id (PK) | name         | qid (PK, FK) | sid (PK, FK) | cid (FK) | status | date       | maxPoints | submissionText                  | autoFeedback | instructorFeedback | pointsReceived |
|---------|--------------|--------------|--------------|----------|--------|------------|-----------|---------------------------------|--------------|--------------------|----------------|
| b5k3... | First Common | f3s0...      | 95s3...      | a593...  | 0      | 1584283995 | 2         | def add(a, b):     return a + b | 6            | Great job!         | 2              |
| q95s... | Quiz #1      | a5sl...      | 5s41...      | a592...  | 2      | 1584254554 | 5         | NULL                            | NULL         | NULL               | 0              |
| ...     | ...          | ...          | ...          |          | ...    | ...        | ...       | ...                             | ...          | ...                | ...            |


An `EXAM` is made up of `QUESTION`s, and is assigned on a `date`.
Multiple entries with the same `id` and `sid` but different `qid` represent the student `EXAM` submission.
Multiple entries with the same `id` and `qid` but different `sid` represent the same `QUESTION` but in different `STUDENT`s for the same `EXAM`.
An `EXAM` with a `status = 0` can be displayed to the `STUDENT`.
An `EXAM` with a `status = 1` cannot be displayed to the `STUDENT`.
An `EXAM` with a `status = 2` needs to be taken by the `STUDENT`.
There cannot be an exam with the same `id`, `qid`, and `sid`.