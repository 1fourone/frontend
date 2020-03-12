CREATE TABLE `webgrader`.`class` 
( 
    `uuid` CHAR(37) NOT NULL , 
    `name` VARCHAR(64) NOT NULL , 
    `section` TINYINT UNSIGNED NOT NULL,
    `class` CHAR(5) NOT NULL,

    PRIMARY KEY (`uuid`)
) ENGINE = InnoDB;

CREATE TABLE `webgrader`.`professor` 
( 
    `uuid` CHAR(37) NOT NULL , 
    `name` VARCHAR(64) NOT NULL , 
    `cid` CHAR(37) NOT NULL,

    PRIMARY KEY (`uuid`, `cid`),
    FOREIGN KEY (`cid`)
        REFERENCES `webgrader`.`class`(uuid)
) ENGINE = InnoDB;

CREATE TABLE `webgrader`.`student` 
( 
    `uuid` CHAR(37) NOT NULL , 
    `name` VARCHAR(64) NOT NULL , 
    `cid` CHAR(37) NOT NULL,

    PRIMARY KEY (`uuid`, `cid`),
    FOREIGN KEY (`cid`)
        REFERENCES `webgrader`.`class`(uuid)
) ENGINE = InnoDB;

CREATE TABLE `webgrader`.`question` 
( 
    `uuid` CHAR(37) NOT NULL , 
    `text` VARCHAR(512) NOT NULL , 
    `difficulty` TINYINT NOT NULL,
    `topic` VARCHAR(64) NOT NULL,
    `creatorID` VARCHAR(37) NOT NULL,
    `creationDate` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (`uuid`),
    FOREIGN KEY (`creatorID`)
        REFERENCES `webgrader`.`professor`(uuid)
) ENGINE = InnoDB;

CREATE TABLE `webgrader`.`examquestion` 
( 
    `uuid` CHAR(37) NOT NULL , 
    `qid` CHAR(37) NOT NULL , 
    `maxPoints` TINYINT NOT NULL,
    
    PRIMARY KEY (`uuid`, `qid`),
    FOREIGN KEY (`qid`)
        REFERENCES `webgrader`.`question`(uuid)
) ENGINE = InnoDB;

CREATE TABLE `webgrader`.`examanswer` 
( 
    `uuid` CHAR(37) NOT NULL , 
    `eqid` CHAR(37) NOT NULL , 
    `pointsReceived` TINYINT NOT NULL,
    `submissionText` VARCHAR(256) NOT NULL,
    `professorFeedback` VARCHAR(128),
    `autoFeedback` VARCHAR(256) NOT NULL,
    `studentID` CHAR(37) NOT NULL,
    `status` TINYINT NOT NULL,
    
    PRIMARY KEY (`uuid`, `eqid`),
    FOREIGN KEY (`eqid`)
        REFERENCES `webgrader`.`examquestion`(uuid),
    FOREIGN KEY (`studentID`)
        REFERENCES `webgrader`.`student`(uuid)
) ENGINE = InnoDB;

CREATE TABLE `webgrader`.`user` 
( 
    `name` VARCHAR(16) NOT NULL,
    `profID` CHAR(37),
    `studID` CHAR(37),
    
    PRIMARY KEY (`name`),
    FOREIGN KEY (`profID`)
        REFERENCES `webgrader`.`professor`(uuid),
    FOREIGN KEY (`studID`)
        REFERENCES `webgrader`.`student`(uuid)
) ENGINE = InnoDB;
