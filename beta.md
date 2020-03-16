# Beta Specification

Program needs to be able to do the following:

[PI] - Identification - Student home page different than Instructor home page
[D] - Instructor adds a question to question bank (write a function ..)
[W] - Instructor selects a question for exam
[W] - Student takes the exam
[W] - Instructor starts autograding (can tweak scores, add comments, release scores)
[W] - Students review results (can only do this once done their score is released)

There are some common subtasks that are important to the above features' functionality:

[W] - Rendering the question bank `qbank.html`

*Legend*: D(esigned), P(artially)I(mplemented), T(esting), R(eady), W(aiting)

## Identification

#### Overview
On a similar login page to one on Alpha release, allow for users to login to either as a faculty or as a student, and to be taken to their home pages.

#### Technical
- `login.html` reads a `name` and `plain_password` from input fields and sends it to `login.php` as `Credentials`
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
- Back's `login.php` uses the credentials to query the `USER` table for either a valid student or instructor id. Once that valid id is found, either `STUDENT` or `INSTRUCTOR` table are queried to get the personal details for the respective homepage `instructor.html` or `student.html`.
```json
{
    "type": "instructor",
    "result": "success"
}
```

## (Adding to) Question Bank

#### Overview
An instructor should be able to add a question of the form "write a function ..." to the question bank. Basically, allow an instructor to set some values for a "question", and create a question and store it. These would eventually be shown to the instructor when creating an exam.

#### Technical
- `qbank.html` is rendered with the `Add` button.
- Instructor clicks on `Add` button.
- `add.html` takes instructor's input on `prompt`, `difficulty`, `topic`, `firstTestCase`, `firstOutput`, `secondTestCase`, `secondOutput`. 
- When instructor clicks `Add to Bank` button, `add.html` will pass these values to `qbank.php` via a `POST` request.
- Front's `qbank.php` constructs a `Question` and passes it to Mid's `qbank.php` via `POST` request.
```json
{
    "id": "c1a5c028-ba52-4b24-9c43-ec6603222ece",
    "prompt": "Write a function add(a, b) that returns the result of adding both numbers.",
    "difficulty": 0,
    "topic": "Functions",
    "creatorID": "3e9cac74-b6a4-4080-8e61-92a31f603d1a",
    "creationDate": 1584285975,
    "firstTestCase": "-1,5",
    "firstOutput": "4",
    "secondTestCase": "-99,-1",
    "secondOutput": "-100"
}
```
- Mid's `qbank.php` sends the same `Question` to Back's `qbank.php` via `POST` request.
- Back's `qbank.php` performs an insertion to the `QUESTION` database with the `Question` it got to add it to the question bank.
```sql
INSERT INTO QUESTION(`id`, `prompt`, `difficulty`, `topic`, `creatorID`, `creationDate`, `firstTestCase`, `firstOutput`, `secondTestCase`, `secondOutput`)
            VALUES($question->{'id'}, $question->{'prompt'}, $question->{'difficulty'}, $question->{'topic'}, $question->{'creatorID'}, $question->{'creationDate'}, $question->{'firstTestCase'}, $question->{'firstOutput'}, $question->{'secondTestCase'}, $question->{'secondOutput'});
```
- Back's `qbank.php` will receive the result of the query and send it to `mid.php` as a `QueryResult`.
```json
{
    "result": "succeed"
}
```
- Mid's `qbank.php` will receive the `QueryResult` and send it to Front's `qbank.php`.
- Front's `qbank.php` will either display a success/failure message to instructor (popup) and reset `qbank.html`.

## Instructor Prepares Exam

#### Overview
An instructor should be able to select questions from the question bank to create an exam. Questions can be searched/filtered by difficulty, topic, creator, and date. When the instructor selects a question, he/she can assign a number of points that the question is worth on the exam. Once the instructor is done adding questions and assigning them points, the instructor can select to what class to assign the exam to. This would make it such that all students on that class have the exam as an active exam and are able to take it from their home page.

The general "flow" would be as follows:
1) Instructor drags and drops questions from the question bank view to the exam view.
2) Optionally, instructor can drag back items to not include them on the test.
3) Instructor assigns points for each question by entering a number on the box to the right of the question.
4) Instructor selects one of his/her class from a dropdown list.
5) Instructor clicks on either `Dispatch Exam` or `Cancel`.
    - `Dispatch Exam` will go through with exam creation
    - `Cancel` will take them back to `instructor.html`


#### Technical
There's some exam-specific technical challenges, namely:

(Incomplete)
1. Managing IFrames and drag-and-drop functionality
    - From a local copy of question data from `qbank.html`, toggle visibility on question bank view and create/destroy the question element in the exam view.
    - Need to find intuitive way of drag and drop, could take some time.
2. Creating a representation for a `Question` (info banner with test label on right for points)
    - From a specific entry in the local copy of question data from `qbank.html`, create a banner with all necessary info about question and have a label on the right for it where points can be assigned.
    - Have these be in a  class but with distinct container id's so that each question can be accessed separately
3. Getting an instructor's classes, and displaying them on the dropdown list.
    - `GET` request sent all the way to Back's `exam.php` as usual
    - Back performs selection query and returns result
4. Sending the Exam's data over to Mid to be sent to Back.

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
- `id` - UUID to represent each distinct exam (primary key, unique)
- `qid` - UUID to represent each distinct question (primary key, foreign key to `QUESTION`.`id`, unique)
- `sid` - UUID to represent each distinct student (primary key, foreign key to `STUDENT`.`id`, unique)
- `status` - number to represent state of exam (0 = past released, 1 = past unreleased, 2 = active)
- `date` - timestamp of when exam was assigned
- `maxPoints` - number of points the question is worth
- `submissionText` - answer that the student wrote
- `autoFeedback` - feedback by the autograding
- `instructorFeedback` - manual feedback by instructor
- `pointsReceived` - number of points question was given by the autograder/overriden by the instructor

| id (PK, U) | qid (PK, FK, U) | sid (PK, FK, U) | status | date       | maxPoints | submissionText                  | autoFeedback | instructorFeedback | pointsReceived |
|---------|----------|----------|--------|------------|-----------|---------------------------------|--------------|-------------------|----------------|
| b5k3... | f3s0...  | 95s3...  | 0      | 1584283995 | 2         | def add(a, b):     return a + b | 6            | Great job!        | 2              |
| q95s... | a5sl...  | 5s41...  | 2      | 1584254554 | 5         | NULL                            | NULL         | NULL              | 0              |
| ...     | ...      | ...      | ...    | ...        | ...       | ...                             | ...          | ...               | ...            |


An `EXAM` is made up of `QUESTION`s, and is assigned on a `date`.
Multiple entries with the same `id` and `sid` but different `qid` represent the student `EXAM` submission.
Multiple entries with the same `id` and `qid` but different `sid` represent the same `QUESTION` but in different `STUDENT`s for the same `EXAM`.
An `EXAM` with a `status = 0` can be displayed to the `STUDENT`.
An `EXAM` with a `status = 1` cannot be displayed to the `STUDENT`.
An `EXAM` with a `status = 2` needs to be taken by the `STUDENT`.
There cannot be an exam with the same `id`, `qid`, and `sid`.