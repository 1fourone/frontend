-- create.sql
--  Contains creation code for tables
--  Assumes mariadb MySQL implementation

-- CLASS table
CREATE TABLE `webgrader`.`CLASS` (
    `id` CHAR(36) DEFAULT NULL,
    `name` VARCHAR(32) NOT NULL,
    `course` VARCHAR(7) NOT NULL,
    `section` CHAR(3) NOT NULL,

    PRIMARY KEY(`id`),
    UNIQUE `unique`(`course`, `section`)
) ENGINE = InnoDB;

CREATE TRIGGER on_class_insert
    BEFORE INSERT ON CLASS
    FOR EACH ROW
    SET new.id = uuid();


-- USER table
CREATE TABLE `webgrader`.`USER` (
    `id` CHAR(36) DEFAULT NULL,
    `name` VARCHAR(7) UNIQUE NOT NULL,
    `password` CHAR(64) NOT NULL,
    `sid` CHAR(37) DEFAULT NULL,
    `iid` CHAR(37) DEFAULT NULL,

    PRIMARY KEY(`id`)
    -- ADD THE FOREIGN KEYS FOR SID AND IID ONCE TABLES DEFINED
) ENGINE = InnoDB;


-- INSTRUCTOR table
CREATE TABLE `webgrader`.`INSTRUCTOR` (
    `id` CHAR(36) DEFAULT NULL,
    `uname` CHAR(36) NOT NULL,
    `name` VARCHAR(32) NOT NULL,
    `cid` CHAR(37) NOT NULL,

    PRIMARY KEY(`id`),
    FOREIGN KEY(`cid`)
        REFERENCES `webgrader`.`CLASS`(id),
    FOREIGN KEY(`uname`)
        REFERENCES `webgrader`.`USER`(name)
) ENGINE = InnoDB;

CREATE TRIGGER on_instructor_insert
    BEFORE INSERT ON INSTRUCTOR
    FOR EACH ROW
    SET new.id = uuid();


-- STUDENT table
CREATE TABLE `webgrader`.`STUDENT` (
    `id` CHAR(36) DEFAULT NULL,
    `uname` CHAR(36) NOT NULL,
    `name` VARCHAR(32) NOT NULL,
    `cid` CHAR(37) NOT NULL,

    PRIMARY KEY(`id`),
    FOREIGN KEY(`cid`)
        REFERENCES `webgrader`.`CLASS`(id),
    FOREIGN KEY(`uname`)
        REFERENCES `webgrader`.`USER`(name)
) ENGINE = InnoDB;

CREATE TRIGGER on_student_insert
    BEFORE INSERT ON STUDENT
    FOR EACH ROW
    SET new.id = uuid();

-- add SID and IID foreign keys to USER here
ALTER TABLE USER
ADD FOREIGN KEY (`sid`) REFERENCES `webgrader`.`STUDENT`(id),
ADD FOREIGN KEY (`iid`) REFERENCES `webgrader`.`INSTRUCTOR`(id);


CREATE TRIGGER on_user_insert
    BEFORE INSERT ON USER
    FOR EACH ROW
    SET new.id = uuid();


-- QUESTION TABLE
CREATE TABLE `webgrader`.`QUESTION` (
    `id` CHAR(36) DEFAULT NULL,
    `prompt` VARCHAR(128) NOT NULL,
    `difficulty` TINYINT NOT NULL,
    `topic` VARCHAR(32) NOT NULL,
    `creatorID` CHAR(36) NOT NULL,
    `creationDate` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `firstTestCase` VARCHAR(64) NOT NULL,
    `firstOutput` VARCHAR(64) NOT NULL,
    `secondTestCase` VARCHAR(64) NOT NULL,
    `secondOutput` VARCHAR(64) NOT NULL,

    PRIMARY KEY(`id`),
    FOREIGN KEY(`creatorID`)
        REFERENCES `webgrader`.`INSTRUCTOR`(id),
    UNIQUE `unique`(`prompt`)
) ENGINE = InnoDB;

CREATE TRIGGER on_question_insert
    BEFORE INSERT ON QUESTION
    FOR EACH ROW
    SET new.id = uuid();

-- EXAM TABLE
-- need to supply its ID since there would be problems
CREATE TABLE `webgrader`.`EXAM` (
    `id` CHAR(36) NOT NULL,
    `name` VARCHAR(64) NOT NULL,
    `qid` CHAR(36) NOT NULL,
    `sid` CHAR(36) NOT NULL,
    `status` TINYINT NOT NULL,
    `date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `maxPoints` TINYINT NOT NULL,
    `submissionText` VARCHAR(512) DEFAULT NULL,
    `autoFeedback` VARCHAR(256) DEFAULT NULL,
    `instructorFeedback` VARCHAR(256) DEFAULT NULL,
    `pointsRecevied` TINYINT DEFAULT NULL,

    PRIMARY KEY(`id`),
    FOREIGN KEY(`qid`)
        REFERENCES `webgrader`.`QUESTION`(id),
    FOREIGN KEY(`sid`)
        REFERENCES `webgrader`.`STUDENT`(id),
    UNIQUE `unique`(`id`, `qid`, `sid`)
) ENGINE = InnoDB;