# Beta Specification

Program needs to be able to do the following:

[D] - Identification - Student home page different than Instructor home page
[W] - Instructor adds a question to question bank (write a function ..)
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
- Mid's `login.php` modifies the credentials to store a `hashed_password` and sends this to Back's login.php in the form
```json
{
    "name": "ma353",
    "hashed_password": "89a7e6eabbc4c9477277ec9b246c6417dc352e69418bf3ef4d75e9c19bbbedd6"
}
```
- Back's `login.php` uses the credentials to query the `USER` table for either a valid student or instructor id. Once that valid id is found, either `STUDENT` or `INSTRUCTOR` table are queried to get the personal details for the respective homepage `instructor.html` or `student.html`.



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

| id (PK) | name                 | course | section |
|---------|----------------------|--------|---------|
| a593... | Roadmap to Computing | CS100  | 001     |
| a592... | Roadmap to Computing | CS100  | 002     |
| ...     | ...                  | ...    | ...     |

A `CLASS` has a `name`, a `course`, and a `section`.
There can be 1+ `section`s of a `course`.