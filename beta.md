# Beta Specification

Program needs to be able to do the following:

[D] - Identification - Student home page different than Instructor home page
[D] - Instructor adds a question to question bank (write a function ..)
[W] - Instructor selects a question for exam
[W] - Student takes the exam
[W] - Instructor starts autograding (can tweak scores, add comments, release scores)
[W] - Students review results (can only do this once done their score is released)

*Legend*: D(esigned), I(mplemented), T(esting), R(eady), W(aiting)

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


## (Adding to) Question Bank

#### Overview
An instructor should be able to add a question of the form "write a function ..." to the question bank. Basically, allow an instructor to set some values for a "question", and create a question and store it. These would eventually be shown to the instructor when creating an exam.

#### Technical
- `qbank.html` allows instructor to click a button `Add` to add a question to the bank. 
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



## Schema
---
`USER` - contains information about a user (can be instructor/student)
- `id` - UUID to represent each distinct user (primary key)
- `sid` - UUID to represent each distinct student (foreign key to `STUDENT`.`id`)
- `pid` - UUID to represent each distinct instructor (foreign key to `INSTRUCTOR`.`id`)

| id (PK) | sid (FK) | pid (FK) |
|---------|----------|----------|
| 89a7... | 95s3...  | NULL     |
| 95q4... | NULL     | 43s8...  |
| ...     | ...      | ...      |

A `USER` can be either a `STUDENT` or an `INSTRUCTOR`.

---
`STUDENT` - contains information about a student
- `id` - UUID to represent each distinct student (primary key)
- `name` - the student's name, is not necessarily unique
- `cid` - UUID to represent each distinct class (primary key, foreign key to `CLASS`.`id`)

| id (PK) | name      | cid (PK, FK) |
|---------|-----------|--------------|
| 95s3... | Billy Joe | a593...      |
| 5s41... | Jenna Doe | a592...      |
| ...     | ...       | ...          |

A `STUDENT` has a `name` and is a part of 1+ `CLASS`es.
`STUDENT`s with the same `cid` belong to the same `CLASS`.
Multiple entries with same `id` but different `cid` represent all of that `STUDENT`s `CLASS`es.

---
`INSTRUCTOR` - contains information about an instructor
- `id` - UUID to represent each distinct instructor (primary key)
- `name` - the instructor's name, not necessarily unique
- `cid` - UUID to represent each distinct class (primary key, foreign key to `CLASS`.`id`)

| id (PK) | name       | cid (PK, FK) |
|---------|------------|--------------|
| 43s8... | James Kent | a593...      |
| a9s5... | Mindy Craw | a592...      |
| ...     | ...        | ...          |

An `INTRUCTOR` has a `name` and teaches 1+ `CLASS`es.
A `CLASS` can only be taught by 1 `INSTRUCTOR`.
Multiple entries with same `id` and different `cid` represent all of that `INSTRUCTOR`s `CLASS`es. 

---
`CLASS` - contains information about a class
- `id` - UUID to represent each distinct class (primary key)
- `name` - the class name
- `course` - the course name
- `section` - the section number

| id (PK) | name                 | course | section |
|---------|----------------------|--------|---------|
| a593... | Roadmap to Computing | CS100  | 001     |
| a592... | Roadmap to Computing | CS100  | 002     |
| ...     | ...                  | ...    | ...     |

A `CLASS` has a `name`, a `course`, and a `section`.
There can be 1+ `section`s of a `course`.

---
`QUESTION` - contains information about a question
- `id` - UUID to represent each distinct question (primary key)
- `prompt` - instructions/prompt for the question
- `difficulty` - number to represent difficulty (0 = easy, 1 = medium, 2 = hard) (index)
- `topic` - topic a question belongs to (index)
- `creatorID` - UUID to represent the creator of question (index, foreign key to `INSTRUCTOR`.`id`)
- `creationDate` - timestamp of when question was created (index)
- `firstTestCase` - testcase for autograder to grade a future exam submission
- `firstOutput` - expected result for first test case
- `secondTestCase` - testcase for autograder to grade a future exam submission
- `secondOutput` - expected result for second test case

| id (PK) | prompt                                                                       | difficulty (I) | topic (I)    | creatorID (I) | creationDate (I) | firstTestCase | firstOutput | secondTestCase | secondOutput |
|---------|------------------------------------------------------------------------------|----------------|--------------|---------------|------------------|---------------|-------------|----------------|--------------|
| f3s0... | Write a function add(a, b) that adds two numbers and returns the result.     | 0              | Functions    | 43s8...       | 1584283994       | 1,5           | 6           | -3,15          | 12           |
| a5sl... | Write a function isLeapYear(year) that returns whether year is a  leap year. | 1              | Conditionals | a9s5...       | 1584254553       | 2020          | True        | 2019           | False        |
| ...     | ...                                                                          | ...            | ...          | ...           | ...              | ...           | ...         | ...            | ...          |

A `QUESTION` has a `prompt` with instructions on what to do.
A `QUESTION` has a `firstTestCase` and `secondTestCase` with their respective `firstOutput` and `secondOutput`.
The `QUESTION` bank can be sorted/filtered by `difficulty`, `topic`, `creatorID`, and `creationDate`.

---
`EXAM` - contains information about an exam
- `id` - UUID to represent each distinct exam (primary key)
- `qid` - UUID to represent each distinct question (primary key, foreign key to `QUESTION`.`id`)
- `sid` - UUID to represent each distinct student (primary key, foreign key to `STUDENT`.`id`)
- `status` - number to represent state of exam (0 = past released, 1 = past unreleased, 2 = active)
- `date` - timestamp of when exam was assigned
- `maxPoints` - number of points the question is worth
- `submissionText` - answer that the student wrote
- `autoFeedback` - feedback by the autograding
- `professorFeedback` - manual feedback by professor
- `pointsReceived` - number of points question was given by the autograder/overriden by the instructor

| id (PK) | qid (PK) | sid (PK) | status | date       | maxPoints | submissionText                  | autoFeedback | professorFeedback | pointsReceived |
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
