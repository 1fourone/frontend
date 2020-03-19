#!/usr/bin/python3
import mysql.connector
import mysql.connector.errors as err
import hashlib
import sys
import yaml
import os
import uuid

db = mysql.connector.connect(
    host = '1fourone.io',
    user = 'webster',
    passwd = '490project',
    database = 'webgrader'
)

# Add an user, and either student/instructor from that user
# Any user, whether instructor or student, needs to belong to (at least) one class.
# Therefore, make sure you call `addClass` at least one time and pass that id here
# Returns (userid, sid, iid) or -1 if failure
def addUser(username, name, password, classID='', type='student'):

    if len(classID) == 0:
        return -1

    # First add the user
    try:
        cursor = db.cursor()
        sql = "INSERT INTO USER(name, password) VALUES (%s, %s)"
        hash = hashlib.sha256(password.encode()).hexdigest()
        print(hash)
        val = (username, hash)
        cursor.execute(sql, val)
        db.commit()

        # Then add either instructor/student
        sql = "INSERT INTO " + str.upper(type) + "(uname, name, cid) VALUES(%s, %s, %s)"
        cursor.execute(sql, (username, name, classID))
        db.commit()

        # Get ID of the user
        sql = "SELECT id FROM USER WHERE name = %s"
        cursor.execute(sql, (username, ))
        userID = cursor.fetchone()[0]
        
        # Get ID of instructor/student
        sql = "SELECT t.id FROM " + str.upper(type) + " t WHERE t.uname=%s"
        cursor.execute(sql, (username, ))
        targetID = cursor.fetchone()[0]
        
        # Update user
        sql = "UPDATE USER SET " + ('sid' if (type == 'student') else 'iid') + "=%s WHERE id=%s"
        cursor.execute(sql, (targetID, userID))
        db.commit()
        
        if type == 'student':
            return (userID, targetID, '')
        else:
            return (userID, '', targetID)
    except:
        return -1

# Add a class only, no student/instructor
# Returns the class ID of new/existing class, or -1 if unsuccessful
def addClass(name, course, section):
    try:
        # First, create the class entry in `CLASS`.
        cursor = db.cursor()
        sql = "INSERT IGNORE INTO CLASS(name, course, section) VALUES (%s, %s, %s)"
        val = (name, course, section)
        cursor.execute(sql, val)
        db.commit()

        # Get its id and return it
        sql = "SELECT id FROM CLASS WHERE course = %s AND section = %s"
        cond = (course, section)
        cursor.execute(sql, cond)
        classID = cursor.fetchone()[0]
        return classID
    except:
        return -1

# Gets an instructor's id or -1 if doesn't exist
def isInstructorValid(userName):
    try:
        cursor = db.cursor()
        sql = "SELECT id FROM INSTRUCTOR WHERE uname=%s"
        cursor.execute(sql, (userName, ))
        instructorID = cursor.fetchone()[0]
        return instructorID
    except:
        return -1

# Gets a class id or -1 if it doesn't exist
def isClassValid(course, section):
    try:
        cursor = db.cursor()
        sql = "SELECT id FROM CLASS WHERE course=%s AND section=%s"
        cursor.execute(sql, (course, section))
        classID = cursor.fetchone()[0]
        return classID
    except:
        return -1

# Gets a list of students in classID or -1 if invalid
def getStudentsInClass(classID):
    if isClassValid == -1:
        return -1
    
    cursor = db.cursor()
    sql = "SELECT id FROM STUDENT WHERE cid=%s"
    cursor.execute(sql, (classID, ))
    students = cursor.fetchall()
    return students

# Add a question to the test bank
# Needs to be called with a valid creatorID - make sure an instructor exists and pass their ID
# For ease of use, make config be a YAML file
# Returns the new/existing question id or -1 if invalid
def addQuestion(configName):
    try:
        f = open(configName)
        c = yaml.load(f, Loader=yaml.FullLoader)

        # We have the config file open - make sure that creator has a valid creatorID
        instructorID = isInstructorValid(c['creator'])
        if instructorID == -1:
            return -1
        
        difficulties = ['easy', 'medium', 'hard']
        difficulty = difficulties.index(c['difficulty'])

        # Insert into DB
        cursor = db.cursor()
        sql = "INSERT IGNORE INTO QUESTION(prompt, difficulty, topic, creatorID, firstTestCase, firstOutput, secondTestCase, secondOutput) \
                VALUES(%s, %s, %s, %s, %s, %s, %s, %s)"
        val = (c['prompt'], difficulty, c['topic'], instructorID, c['testCases'][0], c['output'][0], c['testCases'][1], c['output'][1])
        cursor.execute(sql, val)
        db.commit()

        # Get the question ID
        sql = "SELECT id FROM QUESTION WHERE prompt=%s"
        cursor.execute(sql, (c['prompt'], ))
        questionID = cursor.fetchone()[0]

        f.close()
        return questionID
    except:
        return -1

# Add an exam to the test bank
# Needs to reference a valid question(s), student(s)
# Pass students by their classID, which must be valid
# Use same config structure as addQuestion
# Returns the SINGLE exam id generated for the class, or -1 if invalid
# NOTE: addExam WILL WORK when called subsequently - this is equivalent to the class getting to take a "new exam" with the "old exam questions"
def addExam(configName):
    try:
        f = open(configName)
        c = yaml.load(f, Loader=yaml.FullLoader)

        # We have the config file open - make sure that the question points to a valid question
        questionIDs = []
        for q in c['questions']:
            if os.path.exists('mid/questions/' + q):
                questionIDs.append(addQuestion('mid/questions/' + q))
            else:
                return -1
        
        # Check that course + section point to a valid class
        classID = isClassValid(c['course'], c['section'])
        if classID == -1:
            return -1

        status = ['released', 'unreleased', 'active']
        st = status.index(c['status'])

        # Get all the students that this exam applies to
        students = getStudentsInClass(classID)
        
        examID = uuid.uuid4().hex

        for s in students:
            # Insert into DB
            cursor = db.cursor()
            studentID = s[0]
            for i in range(len(questionIDs)):
                sql = "INSERT IGNORE INTO EXAM(id, name, qid, sid, status, maxPoints) VALUES(%s, %s, %s, %s, %s, %s)"
                val = (examID, c['name'], questionIDs[i], studentID, st, c['maxPoints'][i])
                cursor.execute(sql, val)
                db.commit()

        f.close()
        return examID
    except:
        return -1

if __name__ == "__main__":
    #classID = addClass('Programming Concepts', 'CS280', '001')
    #print("Got class ID:", classID)
    #ids = addUser(sys.argv[1], sys.argv[2], sys.argv[3], classID, sys.argv[4])

    #print(sys.argv[1] + "'s data:", ids)
    cID = addClass('Roadmap to Computing', 'CS100', '001')
    print("Got class ID:", cID)
    addUser('by53', 'Brooke Yale', "1a3b", cID, 'instructor')
    addUser('bk123', 'Billy Kramer', "abcd", cID, 'student')
    addUser('jt341', 'Jenna Travis', "12ab", cID, 'student')
    qID = addQuestion('mid/questions/example.yaml')
    addQuestion('mid/questions/numbers.yaml')
    print("Got question ID:", qID)
    eID = addExam('mid/exams/example.yaml')
    print("Got exam ID:", eID)