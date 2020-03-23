## DB Schema

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
- `functionSignature` - the function signature stated in the prompt
- `difficulty` - number to represent difficulty (0 = easy, 1 = medium, 2 = hard)
- `topic` - topic a question belongs to
- `creatorID` - UUID to represent the creator of question (foreign key to `INSTRUCTOR`.`id`)
- `creationDate` - timestamp of when question was created
- `firstTestCase` - testcase for autograder to grade a future exam submission
- `firstOutput` - expected result for first test case
- `secondTestCase` - testcase for autograder to grade a future exam submission
- `secondOutput` - expected result for second test case

| id (PK) | prompt (U)                                                                   | functionSignature | difficulty | topic        | creatorID (FK) | creationDate | firstTestCase | firstOutput | secondTestCase | secondOutput |
|---------|------------------------------------------------------------------------------|-------------------|------------|--------------|----------------|--------------|---------------|-------------|----------------|--------------|
| f3s0... | Write a function add(a, b) that adds two numbers and returns the result.     | add(a, b)         | 0          | Functions    | 43s8...        | 1584283994   | 1,5           | 6           | -3,15          | 12           |
| a5sl... | Write a function isLeapYear(year) that returns whether year is a  leap year. | isLeapYear(year)  | 1          | Conditionals | a9s5...        | 1584254553   | 2020          | True        | 2019           | False        |
| ...     | ...                                                                          | ...               | ...        | ...          | ...            | ...          | ...           | ...         | ...            | ...          |

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
- `status` - number to represent state of exam (0 = past released, 1 = past unreleased, 2 = awaiting feedback, 3 = awaiting grade, 4 = active)
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