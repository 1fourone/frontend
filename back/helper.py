#!/usr/bin/python3
import mysql.connector
import mysql.connector.errors as err
import hashlib
import sys

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

if __name__ == "__main__":
    classID = addClass('Programming Concepts', 'CS280', '001')
    print("Got class ID:", classID)
    ids = addUser(sys.argv[1], sys.argv[2], sys.argv[3], classID, sys.argv[4])

    print(sys.argv[1] + "'s data:", ids)