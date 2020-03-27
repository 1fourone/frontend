-- queries.sql
--  Contains all queries used in application
--  Assumes mariadb MySQL implementation

-- LOGIN
$sql = "SELECT id, sid, iid FROM USER WHERE name='$name' AND password='$pw'";

-- STUDENT HOME - GET CLASSES
SELECT c.id, c.course, c.section FROM CLASS c, STUDENT s WHERE s.cid=c.id AND s.id='(sid)';

-- STUDENT HOME - GET EXAMS
SELECT DISTINCT e.id, e.name, e.date, c.course, c.section FROM EXAM e, CLASS c WHERE e.sid='(sid)' AND c.id=e.cid;

-- INSTRUCTOR HOME - GET CLASSES
SELECT c.id, c.course, c.section FROM CLASS c, INSTRUCTOR i WHERE i.cid=c.id AND i.id='(iid)';

-- INSTRUCTOR HOME - GET EXAMS
SELECT DISTINCT e.id, e.name, e.date, c.course, c.section FROM EXAM e, CLASS c WHERE e.cid='f9dd048a-6af7-11ea-bed6-b827eb031409' GROUP BY e.id;

-- INSTRUCTOR GET QUESTION BANK
SELECT q.id, q.prompt, q.difficulty, q.topic AS creatorName, q.creationDate FROM QUESTION q, INSTRUCTOR i WHERE q.creatorID=i.id;

-- INSTRUCTOR PUT IN QUESTION BANK
-- Generate UUID for id in query itself
-- @TODO: Populate remaining data from Question object passed in $_POST['question']
SET @questionID = (SELECT UUID());
INSERT INTO QUESTION(id, prompt, functionSignature, difficulty, topic, creatorID, firstTestCase, firstOutput, secondTestCase, secondOutput) VALUES(@questionID,  ... );

-- INSTRUCTOR CREATE EXAM
-- Generate UUID for id in query itself
-- Populate data with Exam object passed in $_POST['exam']
-- @TODO: update to use exam data
SET @examID = (SELECT UUID());
SET AUTOCOMMIT = 0; -- don't automatically commit every change
-- begin the transaction for every student in the class
START TRANSACTION;

-- add student 1
INSERT INTO EXAM(id, name, qid, sid, cid, status, maxPoints) VALUES(@examID, 'Second Semester Quiz', '26be250f-6af9-11ea-bed6-b827eb031409', 'a28a71d3-6af8-11ea-bed6-b827eb031409', 'f9dd048a-6af7-11ea-bed6-b827eb031409', 2, 5);
INSERT INTO EXAM(id, name, qid, sid, cid, status, maxPoints) VALUES(@examID, 'Second Semester Quiz', '26be9bc5-6af9-11ea-bed6-b827eb031409', 'a28a71d3-6af8-11ea-bed6-b827eb031409', 'f9dd048a-6af7-11ea-bed6-b827eb031409', 2, 10);

-- add student 2
INSERT INTO EXAM(id, name, qid, sid, cid, status, maxPoints) VALUES(@examID, 'Second Semester Quiz', '26be250f-6af9-11ea-bed6-b827eb031409', 'a28addf2-6af8-11ea-bed6-b827eb031409', 'f9dd048a-6af7-11ea-bed6-b827eb031409', 2, 5);
INSERT INTO EXAM(id, name, qid, sid, cid, status, maxPoints) VALUES(@examID, 'Second Semester Quiz', '26be9bc5-6af9-11ea-bed6-b827eb031409', 'a28addf2-6af8-11ea-bed6-b827eb031409', 'f9dd048a-6af7-11ea-bed6-b827eb031409', 2, 10);

-- commit the transaction
COMMIT;


-- INSTRUCTOR GET EXAM DESCRIPTION
SELECT DISTINCT e.name, c.course, c.section FROM EXAM e, CLASS c WHERE e.id = '(id)' AND e.cid=c.id;

-- INSTRUCTOR RELEASE SCORES
UPDATE EXAM 
-- status == 0 is past, released
SET status=0
WHERE id = '(id)';