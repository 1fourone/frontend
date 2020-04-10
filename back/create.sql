-- create.sql
--  Contains creation code for tables
--  Assumes mariadb MySQL implementation

-- CLASS table
CREATE TABLE `webgrader`.`CLASS` (
    `id` CHAR(36) DEFAULT "garbage" NOT NULL,
    `name` VARCHAR(32) NOT NULL,
    `course` VARCHAR(7) NOT NULL,
    `section` CHAR(3) NOT NULL,

    PRIMARY KEY(`id`),
    UNIQUE `unique`(`course`, `section`)
) ENGINE = InnoDB;

-- USER table
CREATE TABLE `webgrader`.`USER` (
    `id` CHAR(36) DEFAULT "garbage" NOT NULL,
    `name` VARCHAR(7) UNIQUE NOT NULL,
    `password` CHAR(64) NOT NULL,
    `sid` CHAR(37) DEFAULT NULL,
    `iid` CHAR(37) DEFAULT NULL,

    PRIMARY KEY(`id`)
    -- ADD THE FOREIGN KEYS FOR SID AND IID ONCE TABLES DEFINED
) ENGINE = InnoDB;


-- INSTRUCTOR table
CREATE TABLE `webgrader`.`INSTRUCTOR` (
    `id` CHAR(36) DEFAULT "garbage" NOT NULL,
    `uname` CHAR(36) NOT NULL,
    `name` VARCHAR(32) NOT NULL,
    `cid` CHAR(37) NOT NULL,

    PRIMARY KEY(`id`),
    FOREIGN KEY(`cid`)
        REFERENCES `webgrader`.`CLASS`(id),
    FOREIGN KEY(`uname`)
        REFERENCES `webgrader`.`USER`(name)
) ENGINE = InnoDB;

-- STUDENT table
CREATE TABLE `webgrader`.`STUDENT` (
    `id` CHAR(36) DEFAULT "garbage" NOT NULL,
    `uname` CHAR(36) NOT NULL,
    `name` VARCHAR(32) NOT NULL,
    `cid` CHAR(37) NOT NULL,

    PRIMARY KEY(`id`),
    FOREIGN KEY(`cid`)
        REFERENCES `webgrader`.`CLASS`(id),
    FOREIGN KEY(`uname`)
        REFERENCES `webgrader`.`USER`(name)
) ENGINE = InnoDB;

-- add SID and IID foreign keys to USER here
ALTER TABLE USER
ADD FOREIGN KEY (`sid`) REFERENCES `webgrader`.`STUDENT`(id),
ADD FOREIGN KEY (`iid`) REFERENCES `webgrader`.`INSTRUCTOR`(id);

-- QUESTION TABLE
CREATE TABLE `webgrader`.`QUESTION` (
    `id` CHAR(36) DEFAULT "garbage" NOT NULL,
    `prompt` VARCHAR(128) NOT NULL,
    `functionName` VARCHAR(64) NOT NULL,
    `parameters` VARCHAR(64), NOT NULL,
    `difficulty` TINYINT NOT NULL,
    `topic` VARCHAR(32) NOT NULL,
    `constraintName` VARCHAR(32) NOT NULL,
    `creatorID` CHAR(36) NOT NULL,
    `creationDate` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `firstTestCase` VARCHAR(64) NOT NULL,
    `firstOutput` VARCHAR(64) NOT NULL,
    `secondTestCase` VARCHAR(64) NOT NULL,
    `secondOutput` VARCHAR(64) NOT NULL,
    `thirdTestCase` VARCHAR(64) NULL,
    `thirdOutput` VARCHAR(64) NULL,
    `fourthTestCase` VARCHAR(64) NULL,
    `fourthOutput` VARCHAR(64) NULL,
    `fifthTestCase` VARCHAR(64) NULL,
    `fifthOutput` VARCHAR(64) NULL,
    `sixthTestCase` VARCHAR(64) NULL,
    `sixthOutput` VARCHAR(64) NULL,

    PRIMARY KEY(`id`),
    FOREIGN KEY(`creatorID`)
        REFERENCES `webgrader`.`INSTRUCTOR`(id),
    UNIQUE `unique`(`prompt`)
) ENGINE = InnoDB;

-- EXAM TABLE
-- need to supply its ID since there would be problems
CREATE TABLE `webgrader`.`EXAM` (
    `id` CHAR(36) NOT NULL,
    `name` VARCHAR(64) NOT NULL,
    `qid` CHAR(36) NOT NULL,
    `sid` CHAR(36) NOT NULL,
    `cid` CHAR(36) NOT NULL,
    `status` TINYINT NOT NULL,
    `date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `maxPoints` FLOAT NOT NULL,
    `submissionText` VARCHAR(512) DEFAULT NULL,
    `autoFeedback` VARCHAR(256) DEFAULT NULL,
    `instructorFeedback` VARCHAR(256) DEFAULT NULL,
    `pointsReceived` FLOAT DEFAULT NULL,

    PRIMARY KEY(`id`, `qid`, `sid`),
    FOREIGN KEY(`qid`)
        REFERENCES `webgrader`.`QUESTION`(id),
    FOREIGN KEY(`sid`)
        REFERENCES `webgrader`.`STUDENT`(id),
    FOREIGN KEY(`cid`)
        REFERENCES `webgrader`.`CLASS`(id)
) ENGINE = InnoDB;
