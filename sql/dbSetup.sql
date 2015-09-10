USE `labtech`;

/* Enable Event Scheduler */
set global event_scheduler = 1;

/* Drop Statements */
DROP VIEW IF EXISTS `v_plugin_lthc_ex_client`;
DROP VIEW IF EXISTS `v_plugin_lthc_ex_computer` ;
DROP VIEW IF EXISTS `v_plugin_lthc_score_client`;
DROP VIEW IF EXISTS `v_plugin_lthc_score_computer`;
DROP TABLE IF EXISTS `plugin_lthc_scores`;
DROP TABLE IF EXISTS `plugin_lthc_scores_computers`;
DROP TABLE IF EXISTS `plugin_lthc_scores_monthly`;
DROP TABLE IF EXISTS `plugin_lthc_scores_weekly`;
DROP EVENT IF EXISTS `ev_lthcUpdate_daily`;
DROP EVENT IF EXISTS `ev_lthcUpdate_monthly`;
DROP EVENT IF EXISTS `ev_lthcUpdate_weekly`;
DROP PROCEDURE IF EXISTS  `sp_plugin_UpdateScores`;

/* Create EDFs */
INSERT INTO `extrafield` (`Form`,`Name`,`Sort`,`NoBreak`,`FType`,`Section`,`UnEditable`,`Collapsed`,`Fill`,`LtGuid`,`IsPassword`,`IsEncrypted`,`IsHidden`,`IsRestricted`,`ViewPermissions`,`EditPermissions`) Values('3','Managed IT - Active','4','0','1','Agent Scope','0','0','|Active Managed IT Clients.|2','f8c01a79-64da-11e2-a1d3-0050569b1d9f','0','0','0','0','',''),('3','Ignore Antivirus','3','0','1','Health Scores Web','0','0','','b10dce69-cb0b-4101-a78d-5f0e6bf1bb4c','0','0','0','0','',''),('3','Ignore Disk','4','0','1','Health Scores Web','0','0','','6a96eb97-4e7a-4fc4-943d-792bcd9f3b34','0','0','0','0','',''),('3','Ignore Intrusion','5','0','1','Health Scores Web','0','0','','f7ea0d63-02c4-4fc1-9f34-f81206160f45','0','0','0','0','',''),('3','Ignore Usability','6','0','1','Health Scores Web','0','0','','ee5ae854-7b83-498f-b05c-c9e6de500111','0','0','0','0','',''),('3','Ignore Services','7','0','1','Health Scores Web','0','0','','d4199909-c1cb-4e72-9cba-c810f1af0959','0','0','0','0','',''),('3','Ignore Updates','8','0','1','Health Scores Web','0','0','','b3ee37dd-f29a-4785-aa54-5e1835e6adeb','0','0','0','0','',''),('3','Ignore Event Log','9','0','1','Health Scores Web','0','0','','8349169f-0044-4c04-a632-5af720af7846','0','0','0','0','',''),('3','Go Live Date','2','0','0','Health Scores Web','0','0','||2','d77eaf53-16dd-482c-9b9b-e5ab76416a3c','0','0','0','0','',''),('1','Ignore Antivirus','3','0','1','Health Scores Web','0','0','','15d39b81-d249-480c-8353-e828caee9a91','0','0','0','0','',''),('1','Ignore Disk','4','0','1','Health Scores Web','0','0','','cbda5781-0b43-4743-ae04-73097ebc273b','0','0','0','0','',''),('1','Ignore Intrusion','5','0','1','Health Scores Web','0','0','','0fe793a6-bf88-421b-9444-9878fd0e5ddd','0','0','0','0','',''),('1','Ignore Usability','6','0','1','Health Scores Web','0','0','','38944fb8-3a5f-4582-b3ed-a689f6aea52f','0','0','0','0','',''),('1','Ignore Services','7','0','1','Health Scores Web','0','0','','6308f820-b819-47e8-8b69-9398ebc85080','0','0','0','0','',''),('1','Ignore Updates','8','0','1','Health Scores Web','0','0','','d2235b11-f9fd-4cb4-888a-a2b8d48fc543','0','0','0','0','',''),('1','Ignore Event Logs','9','0','1','Health Scores Web','0','0','','60aec065-35ee-436a-aaa3-2ed796ccf9b8','0','0','0','0','',''),('3','Exclude Reporting','2','0','1','Health Scores Web','0','0','||2','5099009d-4b68-4c84-b50c-7eb90fbd30f8','0','0','0','0','',''),('3','Exclusion Comments','10','0','0','Health Scores Web','0','0','|Place reason for exclusions here.|2','11afa208-6c87-4486-8ad8-986aef4c283e','0','0','0','0','',''),('1','Exclusion Comments','10','0','0','Health Scores Web','0','0','|Place reason for exclusions here.|2','3ea00e03-6535-4ef8-be05-3f52709b4544','0','0','0','0','',''),('3','Team Assignment','2','0','2','Agent Scope','0','0','None~Blue~Green~Red~Yellow~Orange|Select the CARE Team assignment.|2','b2540ed7-ae19-4c84-a75b-34928e2c3390','0','0','0','1','5','5'),('3','Client Specialist','2','0','2','Agent Scope','0','0','None~Adam Tech~Bob Tech~Charlie Tech||2','bb91e2e0-6d26-4b7a-8fc9-d0464a58ee26','0','0','0','1','5','5');

/* Rebuild v_extradata tables */
CALL v_extradata(3, 'Clients');
CALL v_extradata(1, 'Computers');

/*Create Views */
CREATE VIEW `v_plugin_lthc_ex_client` AS (select `clients`.`ClientID` AS `ClientID`,`clients`.`Name` AS `Client Name`,`v_extradataclients`.`Team Assignment` AS `Team Assignment`,`v_extradataclients`.`Exclude Reporting` AS `Exclude Reporting`,`v_extradataclients`.`Ignore Antivirus` AS `Ignore Antivirus`,`v_extradataclients`.`Ignore Disk` AS `Ignore Disk`,`v_extradataclients`.`Ignore Intrusion` AS `Ignore Intrusion`,`v_extradataclients`.`Ignore Usability` AS `Ignore Usability`,`v_extradataclients`.`Ignore Services` AS `Ignore Services`,`v_extradataclients`.`Ignore Updates` AS `Ignore Updates`,`v_extradataclients`.`Ignore Event Log` AS `Ignore Event Log`,str_to_date(`v_extradataclients`.`Go Live Date`,'%m/%d/%Y') AS `Go Live Date`,`v_extradataclients`.`Exclusion Comments` AS `Exclusion Comments` from (`clients` join `v_extradataclients` on((`clients`.`ClientID` = `v_extradataclients`.`clientid`))) where (((`v_extradataclients`.`Exclude Reporting` or `v_extradataclients`.`Ignore Antivirus` or `v_extradataclients`.`Ignore Disk` or `v_extradataclients`.`Ignore Intrusion` or `v_extradataclients`.`Ignore Usability` or `v_extradataclients`.`Ignore Services` or `v_extradataclients`.`Ignore Updates` or `v_extradataclients`.`Ignore Event Log`) = 1) or (str_to_date(`v_extradataclients`.`Go Live Date`,'%m/%d/%Y') between (now() + interval -(30) day) and now())));
CREATE VIEW `v_plugin_lthc_ex_computer` AS (select `computers`.`ComputerID` AS `ComputerID`,`clients`.`Name` AS `Client Name`,`v_extradataclients`.`Team Assignment` AS `Team Assignment`,`computers`.`Name` AS `Computer Name`,`v_extradatacomputers`.`Ignore Antivirus` AS `Ignore Antivirus`,`v_extradatacomputers`.`Ignore Disk` AS `Ignore Disk`,`v_extradatacomputers`.`Ignore Intrusion` AS `Ignore Intrusion`,`v_extradatacomputers`.`Ignore Usability` AS `Ignore Usability`,`v_extradatacomputers`.`Ignore Services` AS `Ignore Services`,`v_extradatacomputers`.`Ignore Updates` AS `Ignore Updates`,`v_extradatacomputers`.`Ignore Event Logs` AS `Ignore Event Logs`,`v_extradatacomputers`.`Exclusion Comments` AS `Exclusion Comments` from (((`computers` join `v_extradatacomputers` on((`computers`.`ComputerID` = `v_extradatacomputers`.`computerid`))) left join `clients` on((`computers`.`ClientID` = `clients`.`ClientID`))) left join `v_extradataclients` on((`computers`.`ClientID` = `v_extradataclients`.`clientid`))) where (((`v_extradatacomputers`.`Ignore Antivirus` or `v_extradatacomputers`.`Ignore Disk` or `v_extradatacomputers`.`Ignore Intrusion` or `v_extradatacomputers`.`Ignore Usability` or `v_extradatacomputers`.`Ignore Services` or `v_extradatacomputers`.`Ignore Updates` or `v_extradatacomputers`.`Ignore Event Logs`) = 1) or (str_to_date(`v_extradataclients`.`Go Live Date`,'%m/%d/%Y') between (now() + interval -(30) day) and now())));
CREATE VIEW `v_plugin_lthc_score_client` AS (select `clients`.`ClientID` AS `clientid`,`clients`.`Name` AS `Client Name`,`v_extradataclients`.`Team Assignment` AS `Team Assignment`,round(avg(`vxr_healthcheck`.`AVHealth`),1) AS `Antivirus`,round(avg(`vxr_healthcheck`.`DiskHealth`),1) AS `Disk`,round(avg(`vxr_healthcheck`.`IntrusionHealth`),1) AS `Intrusion`,round(avg(`vxr_healthcheck`.`UsabilityHealth`),1) AS `Usability`,round(avg(`vxr_healthcheck`.`ServiceHealth`),1) AS `Services`,round(avg(`vxr_healthcheck`.`UpdateHealth`),1) AS `Updates`,round(avg(`vxr_healthcheck`.`EventHealth`),1) AS `Event Log`,ROUND((COALESCE(ROUND(AVG(`vxr_healthcheck`.`AVHealth`),1),0) + COALESCE(ROUND(AVG(`vxr_healthcheck`.`DiskHealth`),1),0) + COALESCE(ROUND(AVG(`vxr_healthcheck`.`IntrusionHealth`),1),0) + COALESCE(ROUND(AVG(`vxr_healthcheck`.`UsabilityHealth`),1),0) + COALESCE(ROUND(AVG(`vxr_healthcheck`.`ServiceHealth`),1),0) + COALESCE(ROUND(AVG(`vxr_healthcheck`.`UpdateHealth`),1),0) + COALESCE(ROUND(AVG(`vxr_healthcheck`.`EventHealth`),1),0)) / (COALESCE(ROUND(AVG(`vxr_healthcheck`.`AVHealth`),1)/ROUND(AVG(`vxr_healthcheck`.`AVHealth`),1),0) + COALESCE(ROUND(AVG(`vxr_healthcheck`.`DiskHealth`),1)/ROUND(AVG(`vxr_healthcheck`.`DiskHealth`),1),0) +  COALESCE(ROUND(AVG(`vxr_healthcheck`.`IntrusionHealth`),1)/ROUND(AVG(`vxr_healthcheck`.`IntrusionHealth`),1),0) +  COALESCE(ROUND(AVG(`vxr_healthcheck`.`UsabilityHealth`),1)/ROUND(AVG(`vxr_healthcheck`.`UsabilityHealth`),1),0) +  COALESCE(ROUND(AVG(`vxr_healthcheck`.`ServiceHealth`),1)/ROUND(AVG(`vxr_healthcheck`.`ServiceHealth`),1),0) +  COALESCE(ROUND(AVG(`vxr_healthcheck`.`UpdateHealth`),1)/ROUND(AVG(`vxr_healthcheck`.`UpdateHealth`),1),0) +  COALESCE(ROUND(AVG(`vxr_healthcheck`.`EventHealth`),1)/ROUND(AVG(`vxr_healthcheck`.`EventHealth`),1),0)),1) AS `Avg Score`,round(avg(if((`v_extradataclients`.`Ignore Antivirus` = 1),NULL,if((`v_extradatacomputers`.`Ignore Antivirus` = 1),NULL,`vxr_healthcheck`.`AVHealth`))),1) AS `AV Ex`,round(avg(if((`v_extradataclients`.`Ignore Disk` = 1),NULL,if((`v_extradatacomputers`.`Ignore Disk` = 1),NULL,`vxr_healthcheck`.`DiskHealth`))),1) AS `Disk Ex`,round(avg(if((`v_extradataclients`.`Ignore Intrusion` = 1),NULL,if((`v_extradatacomputers`.`Ignore Intrusion` = 1),NULL,`vxr_healthcheck`.`IntrusionHealth`))),1) AS `Intrusion Ex`,round(avg(if((`v_extradataclients`.`Ignore Usability` = 1),NULL,if((`v_extradatacomputers`.`Ignore Usability` = 1),NULL,`vxr_healthcheck`.`UsabilityHealth`))),1) AS `Usability Ex`,round(avg(if((`v_extradataclients`.`Ignore Services` = 1),NULL,if((`v_extradatacomputers`.`Ignore Services` = 1),NULL,`vxr_healthcheck`.`ServiceHealth`))),1) AS `Services Ex`,round(avg(if((`v_extradataclients`.`Ignore Updates` = 1),NULL,if((`v_extradatacomputers`.`Ignore Updates` = 1),NULL,`vxr_healthcheck`.`UpdateHealth`))),1) AS `Updates Ex`,round(avg(if((`v_extradataclients`.`Ignore Event Log` = 1),NULL,if((`v_extradatacomputers`.`Ignore Event Logs` = 1),NULL,`vxr_healthcheck`.`EventHealth`))),1) AS `Event Ex` from (((`vxr_healthcheck` join `clients` on((`vxr_healthcheck`.`ClientID` = `clients`.`ClientID`))) left join `v_extradataclients` on((`vxr_healthcheck`.`ClientID` = `v_extradataclients`.`clientid`))) left join `v_extradatacomputers` on((`vxr_healthcheck`.`ComputerID` = `v_extradatacomputers`.`computerid`))) where (`vxr_healthcheck`.`CheckDate` > (now() + interval -(1) month)) group by `clients`.`Name`);
CREATE VIEW `v_plugin_lthc_score_computer` AS (select `computers`.`ComputerID` AS `computerid`,`computers`.`Name` AS `Computer_Name`,`clients`.`ClientID` AS `clientid`,`clients`.`Name` AS `Client_Name`,`v_extradataclients`.`Team Assignment` AS `Team_Assignment`,round(avg(`vxr_healthcheck`.`AVHealth`),1) AS `Antivirus`,round(avg(`vxr_healthcheck`.`DiskHealth`),1) AS `Disk`,round(avg(`vxr_healthcheck`.`IntrusionHealth`),1) AS `Intrusion`,round(avg(`vxr_healthcheck`.`UsabilityHealth`),1) AS `Usability`,round(avg(`vxr_healthcheck`.`ServiceHealth`),1) AS `Services`,round(avg(`vxr_healthcheck`.`UpdateHealth`),1) AS `Updates`,round(avg(`vxr_healthcheck`.`EventHealth`),1) AS `Event Log`,round((((((((round(avg(`vxr_healthcheck`.`AVHealth`),1) + round(avg(`vxr_healthcheck`.`DiskHealth`),1)) + round(avg(`vxr_healthcheck`.`IntrusionHealth`),1)) + round(avg(`vxr_healthcheck`.`UsabilityHealth`),1)) + round(avg(`vxr_healthcheck`.`ServiceHealth`),1)) + round(avg(`vxr_healthcheck`.`UpdateHealth`),1)) + round(avg(`vxr_healthcheck`.`EventHealth`),1)) / 7),1) AS `Avg Score`,round(avg(if((`v_extradataclients`.`Ignore Antivirus` = 1),NULL,if((`v_extradatacomputers`.`Ignore Antivirus` = 1),NULL,`vxr_healthcheck`.`AVHealth`))),1) AS `AV Ex`,round(avg(if((`v_extradataclients`.`Ignore Disk` = 1),NULL,if((`v_extradatacomputers`.`Ignore Disk` = 1),NULL,`vxr_healthcheck`.`DiskHealth`))),1) AS `Disk Ex`,round(avg(if((`v_extradataclients`.`Ignore Intrusion` = 1),NULL,if((`v_extradatacomputers`.`Ignore Intrusion` = 1),NULL,`vxr_healthcheck`.`IntrusionHealth`))),1) AS `Intrusion Ex`,round(avg(if((`v_extradataclients`.`Ignore Usability` = 1),NULL,if((`v_extradatacomputers`.`Ignore Usability` = 1),NULL,`vxr_healthcheck`.`UsabilityHealth`))),1) AS `Usability Ex`,round(avg(if((`v_extradataclients`.`Ignore Services` = 1),NULL,if((`v_extradatacomputers`.`Ignore Services` = 1),NULL,`vxr_healthcheck`.`ServiceHealth`))),1) AS `Services Ex`,round(avg(if((`v_extradataclients`.`Ignore Updates` = 1),NULL,if((`v_extradatacomputers`.`Ignore Updates` = 1),NULL,`vxr_healthcheck`.`UpdateHealth`))),1) AS `Updates Ex`,round(avg(if((`v_extradataclients`.`Ignore Event Log` = 1),NULL,if((`v_extradatacomputers`.`Ignore Event Logs` = 1),NULL,`vxr_healthcheck`.`EventHealth`))),1) AS `Event Ex` from ((((`vxr_healthcheck` join `clients` on((`vxr_healthcheck`.`ClientID` = `clients`.`ClientID`))) left join `v_extradataclients` on((`vxr_healthcheck`.`ClientID` = `v_extradataclients`.`clientid`))) left join `computers` on((`vxr_healthcheck`.`ComputerID` = `computers`.`ComputerID`))) left join `v_extradatacomputers` on((`vxr_healthcheck`.`ComputerID` = `v_extradatacomputers`.`computerid`))) where (`vxr_healthcheck`.`CheckDate` > (now() + interval -(1) month)) group by `computers`.`Name`);

/* Create Tables */
CREATE TABLE `plugin_lthc_scores` (`ClientID` int(11) NOT NULL, `Client_Name` varchar(50) NOT NULL, `Team_Assignment` longtext NOT NULL, `Antivirus` double(18,1) DEFAULT NULL, `Disk` double(18,1) DEFAULT NULL, `Intrusion` double(18,1) DEFAULT NULL, `Usability` double(18,1) DEFAULT NULL, `Services` double(18,1) DEFAULT NULL, `Updates` double(18,1) DEFAULT NULL, `Event_Log` double(18,1) DEFAULT NULL, `Avg_Score` double(18,1) DEFAULT NULL, `AV_Ex` double(18,1) DEFAULT NULL, `Disk_Ex` double(18,1) DEFAULT NULL, `Intrusion_Ex` double(18,1) DEFAULT NULL, `Usability_Ex` double(18,1) DEFAULT NULL, `Services_Ex` double(18,1) DEFAULT NULL, `Updates_Ex` double(18,1) DEFAULT NULL, `Event_Ex` double(18,1) DEFAULT NULL, PRIMARY KEY (`ClientID`)) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE `plugin_lthc_scores_computers` (`ComputerID` int(11) NOT NULL, `Computer_Name` varchar(50) NOT NULL, `ClientID` int(11) NOT NULL, `Client_Name` varchar(50) NOT NULL, `Team_Assignment` longtext NOT NULL, `Antivirus` double(18,1) DEFAULT NULL, `Disk` double(18,1) DEFAULT NULL, `Intrusion` double(18,1) DEFAULT NULL, `Usability` double(18,1) DEFAULT NULL, `Services` double(18,1) DEFAULT NULL, `Updates` double(18,1) DEFAULT NULL, `Event_Log` double(18,1) DEFAULT NULL, `Avg_Score` double(18,1) DEFAULT NULL, `AV_Ex` double(18,1) DEFAULT NULL, `Disk_Ex` double(18,1) DEFAULT NULL, `Intrusion_Ex` double(18,1) DEFAULT NULL, `Usability_Ex` double(18,1) DEFAULT NULL, `Services_Ex` double(18,1) DEFAULT NULL, `Updates_Ex` double(18,1) DEFAULT NULL, `Event_Ex` double(18,1) DEFAULT NULL, PRIMARY KEY (`ComputerID`)) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE `plugin_lthc_scores_monthly` (`ClientID` int(11) NOT NULL, `Client_Name` varchar(50) NOT NULL, `Team_Assignment` longtext NOT NULL, `Antivirus` double(18,1) DEFAULT NULL, `Disk` double(18,1) DEFAULT NULL, `Intrusion` double(18,1) DEFAULT NULL, `Usability` double(18,1) DEFAULT NULL, `Services` double(18,1) DEFAULT NULL, `Updates` double(18,1) DEFAULT NULL, `Event_Log` double(18,1) DEFAULT NULL, `Avg_Score` double(18,1) DEFAULT NULL, `AV_Ex` double(18,1) DEFAULT NULL, `Disk_Ex` double(18,1) DEFAULT NULL, `Intrusion_Ex` double(18,1) DEFAULT NULL, `Usability_Ex` double(18,1) DEFAULT NULL, `Services_Ex` double(18,1) DEFAULT NULL, `Updates_Ex` double(18,1) DEFAULT NULL, `Event_Ex` double(18,1) DEFAULT NULL, `CheckDate` datetime NOT NULL, PRIMARY KEY (`ClientID`,`CheckDate`)) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE `plugin_lthc_scores_weekly` (`ClientID` int(11) NOT NULL, `Client_Name` varchar(50) NOT NULL, `Team_Assignment` longtext NOT NULL, `Antivirus` double(18,1) DEFAULT NULL, `Disk` double(18,1) DEFAULT NULL, `Intrusion` double(18,1) DEFAULT NULL, `Usability` double(18,1) DEFAULT NULL, `Services` double(18,1) DEFAULT NULL, `Updates` double(18,1) DEFAULT NULL, `Event_Log` double(18,1) DEFAULT NULL, `Avg_Score` double(18,1) DEFAULT NULL, `AV_Ex` double(18,1) DEFAULT NULL, `Disk_Ex` double(18,1) DEFAULT NULL, `Intrusion_Ex` double(18,1) DEFAULT NULL, `Usability_Ex` double(18,1) DEFAULT NULL, `Services_Ex` double(18,1) DEFAULT NULL, `Updates_Ex` double(18,1) DEFAULT NULL, `Event_Ex` double(18,1) DEFAULT NULL, `CheckDate` datetime NOT NULL, PRIMARY KEY (`ClientID`,`CheckDate`)) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/* Create Events */
DELIMITER $$
CREATE EVENT `ev_lthcUpdate_daily` ON SCHEDULE EVERY 1 DAY STARTS '2014-02-19 07:00:00' ON COMPLETION NOT PRESERVE ENABLE DO BEGIN
	    CALL sp_plugin_UpdateScores('Daily');
	END $$
DELIMITER ;

DELIMITER $$
CREATE EVENT `ev_lthcUpdate_monthly` ON SCHEDULE EVERY 1 MONTH STARTS '2014-03-01 07:15:00' ON COMPLETION NOT PRESERVE ENABLE DO BEGIN
    CALL sp_plugin_UpdateScores('Monthly');
	END $$
DELIMITER ;

DELIMITER $$
CREATE EVENT `ev_lthcUpdate_weekly` ON SCHEDULE EVERY 1 WEEK STARTS '2014-02-23 07:10:00' ON COMPLETION NOT PRESERVE ENABLE DO BEGIN
    CALL sp_plugin_UpdateScores('Weekly');
	END $$
DELIMITER ;

/* Create Procedure */
DELIMITER $$
CREATE PROCEDURE `sp_plugin_UpdateScores`(IN var1 varchar(10))
BEGIN
  IF var1 = 'Daily' THEN
    TRUNCATE `plugin_lthc_scores`;
    TRUNCATE `plugin_lthc_scores_computers`;
		INSERT INTO `plugin_lthc_scores` (clientid,`Client_Name`,`Team_Assignment`,`Antivirus`,`Disk`,`Intrusion`,`Usability`,`Services`,`Updates`,`Event_Log`,`Avg_Score`,`AV_Ex`,`Disk_Ex`,`Intrusion_Ex`,`Usability_Ex`,`Services_Ex`,`Updates_Ex`,`Event_Ex`) SELECT `ClientID`,`Client Name`,v_extradataclients.`Team Assignment`,`Antivirus`,`Disk`,`Intrusion`,`Usability`,`Services`,`Updates`,`Event Log`,`Avg Score`,`AV Ex`,`Disk Ex`,`Intrusion Ex`,`Usability Ex`,`Services Ex`,`Updates Ex`,`Event Ex` FROM `v_plugin_lthc_score_client` JOIN v_extradataclients USING (clientid) WHERE `Managed IT - Active` = 1;
    INSERT INTO `plugin_lthc_scores_computers` (SELECT * FROM `v_plugin_lthc_score_computer`);
  END IF;

  IF var1 = 'Weekly' THEN
		INSERT INTO `plugin_lthc_scores_weekly` (clientid,`Client_Name`,`Team_Assignment`,`Antivirus`,`Disk`,`Intrusion`,`Usability`,`Services`,`Updates`,`Event_Log`,`Avg_Score`,`AV_Ex`,`Disk_Ex`,`Intrusion_Ex`,`Usability_Ex`,`Services_Ex`,`Updates_Ex`,`Event_Ex`,`CheckDate`) SELECT `ClientID`,`Client_Name`,`Team_Assignment`,`Antivirus`,`Disk`,`Intrusion`,`Usability`,`Services`,`Updates`,`Event_Log`,`Avg_Score`,`AV_Ex`,`Disk_Ex`,`Intrusion_Ex`,`Usability_Ex`,`Services_Ex`,`Updates_Ex`,`Event_Ex`,NOW() FROM `plugin_lthc_scores`;
  END IF;

  IF var1 = 'Monthly' THEN
		INSERT INTO `plugin_lthc_scores_monthly` (clientid,`Client_Name`,`Team_Assignment`,`Antivirus`,`Disk`,`Intrusion`,`Usability`,`Services`,`Updates`,`Event_Log`,`Avg_Score`,`AV_Ex`,`Disk_Ex`,`Intrusion_Ex`,`Usability_Ex`,`Services_Ex`,`Updates_Ex`,`Event_Ex`,`CheckDate`) SELECT `ClientID`,`Client_Name`,`Team_Assignment`,`Antivirus`,`Disk`,`Intrusion`,`Usability`,`Services`,`Updates`,`Event_Log`,`Avg_Score`,`AV_Ex`,`Disk_Ex`,`Intrusion_Ex`,`Usability_Ex`,`Services_Ex`,`Updates_Ex`,`Event_Ex`,NOW() FROM `plugin_lthc_scores`;
  END IF;
END $$
DELIMITER ;
